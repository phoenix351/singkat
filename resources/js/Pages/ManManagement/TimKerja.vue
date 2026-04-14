<template>
  <Head title="Tim Kerja" />
  <AppLayout>
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
          Daftar Tim Kerja
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model.trim="searchField" placeholder="Cari Tim Kerja" />
          </IconField>
          <Button
            icon="pi pi-download"
            rounded
            aria-label="Download"
            severity="success"
            class="mr-2 mb-2 lg:mb-0"
          />
          <!-- @click="showToast" -->
          <Button
            @click="createDialog = true"
            severity="info"
            rounded
            class="mb-2 lg:mb-0"
          >
            <i class="pi pi-plus"></i>
            Tambah Tim Baru
          </Button>
        </div>
      </div>
      <DataTable
        :value="tim_kerja.data"
        class="w-full"
        lazy
        paginator
        :rows="tim_kerja.per_page"
        :first="(tim_kerja.current_page - 1) * tim_kerja.per_page"
        :total-records="tim_kerja.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortField"
        :sort-order="sortOrder"
        @page="fetchData"
        @sort="fetchData"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} tim kerja"
      >
        <template #empty><div class="text-center">Tim Kerja tidak ada</div></template>
        <Column
          v-for="item in allColumns"
          :key="item.field"
          :field="item.field"
          :header="item.header"
          sortable
        />
        <Column :exportable="false" style="min-width: 12rem">
          <template #body="slotProps">
            <div class="flex justify-end gap-2 w-full">
              <Button
                @click="updateTimKerja(slotProps.data)"
                icon="pi pi-pencil"
                variant="outlined"
                rounded
                class="mr-2"
              />
              <Button
                @click="deleteTimKerja(slotProps.data)"
                icon="pi pi-trash"
                variant="outlined"
                rounded
                severity="danger"
              />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
    <Dialog
      v-model:visible="createDialog"
      modal
      header="Tambah Tim Kerja"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-6">
        <div>
          <label class="block font-bold mb-3">Upload/Manual</label>
          <Select
            :options="[
              { label: 'Manual', value: 'manual' },
              { label: 'Upload', value: 'upload' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="form.tipe"
            placeholder="Pilih Mode"
          />
        </div>
        <template v-if="form.tipe == 'manual'">
          <div>
            <label for="tahun" class="block font-bold mb-3">Tahun</label>
            <Select
              :options="tahunDrop"
              class="w-full"
              showClear
              option-label="label"
              option-value="value"
              v-model="form.tahun"
              placeholder="Pilih Tahun"
            />
          </div>
          <div>
            <label for="label" class="block font-bold mb-3">Nama Tim Kerja</label>
            <InputText
              id="form-label"
              v-model="form.label"
              required="true"
              autofocus
              fluid
              placeholder="Isi Nama Tim Kerja"
            />
          </div>
        </template>
        <template v-if="form.tipe == 'upload'">
          <div class="flex flex-row space-x-2">
            <Button @click="downloadTemplate" severity="info" class="mb-2 lg:mb-0">
              <i class="pi pi-file"></i>
              Template
            </Button>
            <FileUpload
              ref="fileupload"
              mode="basic"
              name="file"
              accept=".xlsx,.xls,.csv"
              :maxFileSize="1000000"
              @select="onSelectFile"
            />
          </div>
        </template>
      </div>
      <template #footer>
        <Button
          label="Cancel"
          @click="createDialog = false"
          size="small"
          severity="danger"
          autofocus
        />
        <Button
          @click="submit({ fileupload: selectedFile })"
          label="Simpan"
          size="small"
          severity="success"
          autofocus
        />
      </template>
    </Dialog>
    <Dialog
      v-model:visible="updateDialog"
      modal
      header="Edit Tim Kerja"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-6">
        <div>
          <label for="tahun" class="block font-bold mb-3">Tahun</label>
          <Select
            :options="tahunDrop"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="editedTimKerja.tahun"
            placeholder="Pilih Tahun"
          />
        </div>
        <div>
          <label for="label" class="block font-bold mb-3">Nama Tim Kerja</label>
          <InputText
            id="form-label"
            v-model="editedTimKerja.label"
            required="true"
            autofocus
            fluid
            placeholder="Isi Nama Tim Kerja"
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancel"
          @click="updateDialog = false"
          size="small"
          severity="danger"
          autofocus
        />
        <Button
          @click="submit({ updated: true })"
          label="Simpan"
          size="small"
          severity="success"
          autofocus
        />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/ManManagement/AppLayout.vue";
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { FileUpload, Toast, useToast, useConfirm } from "primevue";
import { ref, watch } from "vue";
import * as XLSX from "xlsx";

const props = defineProps({
  tim_kerja: {
    type: Object,
  },
});
const allColumns = [
  { field: "tahun", header: "Tahun" },
  { field: "label", header: "Tim Kerja" },
];
const searchField = ref(null);
//paginated and search
const currentPage = ref((props.tim_kerja.current_page - 1) * props.tim_kerja.per_page);
const paginated = ref(props?.tim_kerja?.per_page ?? 10);
const sortField = ref(null);
const sortOrder = ref(null);
const fetchData = (event = null) => {
  currentPage.value = event ? Math.floor(event.first / event.rows) + 1 : 1;
  paginated.value = event?.rows ?? paginated.value;
  router.get(
    route("man-management.tim-kerja.index"),
    {
      currentPage: currentPage.value,
      paginated: paginated.value,
      sortField: event?.sortField ?? sortField.value,
      sortOrder: event?.sortOrder ?? sortOrder.value,
      searchField: searchField.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  );
};
const delayedFetchData = debounce(() => {
  fetchData();
});
watch(searchField, () => {
  delayedFetchData();
});
//submit
const createDialog = ref(false);
const updateDialog = ref(false);
const form = useForm({
  _token: null,
  tipe: null,
  tahun: null,
  label: null,
});
const tahunDrop = Array.from(
  { length: 11 },
  (_, index) => new Date().getFullYear() - index
).map((t) => ({
  label: t.toString(),
  value: t.toString(),
}));
const editedTimKerja = ref({});
const updateTimKerja = (data) => {
  updateDialog.value = true;
  editedTimKerja.value = { ...data };
};
const page = usePage();
const toast = useToast();
const submit = async ({ updated = false, fileupload = null }) => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    if (fileupload) {
      if (!selectedFile.value) return;
      router.post(
        route("man-management.tim-kerja.store"),
        {
          _token: tokens,
          fileUpload: selectedFile.value,
        },
        {
          preserveScroll: false,
          preserveState: false,
          onSuccess: () => {
            const flash = page.props.flash;
            if (flash?.notification ?? null) {
              for (const n of flash.notification) {
                toast.add({
                  severity: n.type,
                  summary: n.type == "success" ? "Berhasil" : "Gagal",
                  detail: n.message,
                  life: 3000,
                });
              }
            }
          },
        }
      );
      return;
    }
    if (updated) {
      router.patch(
        route("man-management.tim-kerja.patch"),
        {
          _token: tokens,
          ...editedTimKerja.value,
        },
        { preserveScroll: false, preserveState: false }
      );
      return;
    }
    form._token = tokens;
    await form.post(route("man-management.tim-kerja.store"), {
      preserveState: false,
      preserveScroll: false,
      onSuccess: (response) => {
        form.reset();
      },
    });
  } catch (error) {
    console.error(error);
  }
};
const confirm = useConfirm();
const deleteTimKerja = (data) => {
  confirm.require({
    message: "Apakah kamu yakin menghapus Tim Kerja ini?",
    header: "Konfirmasi",
    icon: "pi pi-info-circle",
    rejectLabel: "Cancel",
    acceptLabel: "Hapus",
    rejectProps: {
      label: "Cancel",
      size: "small",
      severity: "danger",
    },
    acceptProps: {
      label: "Hapus",
      size: "small",
      severity: "success",
    },
    accept: async () => {
      const { data: tokens } = await axios.get(route("api.token.csrf"));
      router.delete(route("man-management.tim-kerja.destroy", { id: data.id }), {
        _token: tokens,
      });
    },
  });
};
const selectedFile = ref(null);
const onSelectFile = (e) => {
  let fileS = e?.files?.[0] ?? null;
  if (fileS) {
    const reader = new FileReader();
    reader.onload = (e) => {
      /* Parse data */
      const bstr = e.target.result;
      const wb = XLSX.read(bstr, { type: "binary" });
      /* Get first worksheet */
      const wsname = wb.SheetNames[0];
      const ws = wb.Sheets[wsname];
      /* Convert array of arrays */
      const data = XLSX.utils.sheet_to_json(ws, { header: 1 });
      selectedFile.value = data;
    };
    reader.readAsBinaryString(fileS);
    // console.log(result)
  }
};
const downloadTemplate = () => {
  window.location.href = "/man-management/download-template/tim-kerja";
};
watch(createDialog, () => {
  if (createDialog.value == false) {
    form.reset();
    selectedFile.value = null;
  }
});
</script>

<style scoped></style>
