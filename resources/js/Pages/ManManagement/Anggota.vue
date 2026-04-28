<template>
  <Head title="Keanggotaan" />
  <AppLayout>
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
          Keanggotaan Tim Kerja
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
            Tambah Anggota Tim Baru
          </Button>
        </div>
      </div>
      <DataTable
        :value="anggota.data"
        class="w-full"
        lazy
        paginator
        :rows="anggota.per_page"
        :first="(anggota.current_page - 1) * anggota.per_page"
        :total-records="anggota.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortField"
        :sort-order="sortOrder"
        @page="fetchData"
        @sort="fetchData"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
      >
        <Column
          v-for="item in allColumns"
          :key="item.field"
          :field="item.field"
          :header="item.header"
          sortable
        />
        <template #empty>
          <div class="text-center">Data tidak ada</div>
        </template>
        <Column :exportable="false" style="min-width: 12rem">
          <template #body="slotProps">
            <div class="flex justify-end gap-2 w-full">
              <Button
                icon="pi pi-pencil"
                @click="updateAnggota(slotProps.data)"
                variant="outlined"
                rounded
                class="mr-2"
              />
              <Button
                @click="deleteAnggota(slotProps.data)"
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
          <div v-if="form.errors.tipe" class="text-red-500 text-sm mt-2">
            {{ form.errors.tipe }}
          </div>
        </div>
        <template v-if="form.tipe == 'manual'">
          <div>
            <label for="tim" class="block font-bold mb-3">Nama Tim Kerja</label>
            <Select
              :options="tim"
              class="w-full"
              showClear
              filter
              option-label="label"
              option-value="value"
              v-model="form.tim_id"
              placeholder="Pilih Tim"
            />
            <div v-if="page.props.errors.tim_id" class="text-red-500 text-sm mt-2">
              {{ page.props.errors?.tim_id }}
            </div>
          </div>
          <div>
            <label for="pegawai" class="block font-bold mb-3">Nama Pegawai</label>
            <Select
              :options="pegawai"
              class="w-full"
              showClear
              filter
              option-label="label"
              option-value="value"
              v-model="form.pegawai_id"
              placeholder="Pilih Pegawai"
            />
            <div v-if="page.props.errors.pegawai_id" class="text-red-500 text-sm mt-2">
              {{ page.props.errors?.pegawai_id }}
            </div>
          </div>
          <div>
            <label for="keanggotaan" class="block font-bold mb-3">Keanggotaan</label>
            <Select
              :options="[
                { label: 'Anggota', value: 'anggota' },
                { label: 'Ketua', value: 'ketua' },
              ]"
              class="w-full"
              showClear
              option-label="label"
              option-value="value"
              v-model="form.keanggotaan"
              placeholder="Pilih Keanggotaan"
            />
            <div v-if="page.props.errors.keanggotaan" class="text-red-500 text-sm mt-2">
              {{ page.props.errors?.keanggotaan }}
            </div>
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
      header="Edit Keanggotaan"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-6">
        <div>
          <label for="tim" class="block font-bold mb-3">Tim Kerja</label>
          <Select
            :options="tim"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="editedAnggota.tim_id"
            placeholder="Pilih Tim"
            disabled
          />
        </div>
        <div>
          <label for="pegawai" class="block font-bold mb-3">Nama Pegawai</label>
          <Select
            :options="pegawai"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="editedAnggota.pegawai_id"
            placeholder="Pilih Pegawai"
            disabled
          />
        </div>
        <div>
          <label for="keanggotaan" class="block font-bold mb-3">Keanggotaan</label>
          <Select
            :options="[
              { label: 'Anggota', value: 'anggota' },
              { label: 'Ketua', value: 'ketua' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="editedAnggota.keanggotaan"
            placeholder="Pilih Keanggotaan"
          />
        </div>
      </div>
      <template #footer
        ><Button
          label="Cancel"
          @click="updateDialog = false"
          size="small"
          severity="danger"
          autofocus />
        <Button
          @click="submit({ update: true })"
          label="Simpan"
          size="small"
          severity="success"
          autofocus
      /></template>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/ManManagement/AppLayout.vue";
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { useConfirm, useToast } from "primevue";
import * as XLSX from "xlsx";

const toast = useToast();
const page = usePage();
const props = defineProps({
  anggota: { type: Object },
  tim: { type: Array },
  pegawai: { type: Array },
});
const allColumns = [
  { field: "tim.label", header: "Tim Kerja" },
  { field: "pegawai.name", header: "Nama Pegawai" },
  { field: "keanggotaan", header: "Keanggotaan" },
];
const searchField = ref(null);
//paginated and search
const currentPage = ref((props.anggota.current_page - 1) * props.anggota.per_page);
const paginated = ref(props?.anggota?.per_page ?? 10);
const sortField = ref(null);
const sortOrder = ref(null);
const fetchData = (event = null) => {
  currentPage.value = event ? Math.floor(event.first / event.rows) + 1 : 1;
  paginated.value = event?.rows ?? paginated.value;
  router.get(
    route("man-management.anggota.index"),
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
//submit and update
const createDialog = ref(false);
const form = useForm({
  _token: null,
  tipe: null,
  tim_id: null,
  pegawai_id: null,
  keanggotaan: null,
});
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
const updateDialog = ref(false);
const editedAnggota = ref({});
const updateAnggota = (data) => {
  updateDialog.value = true;
  editedAnggota.value = { ...data };
};
const submit = async ({ update = false, fileupload = null }) => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    if (update) {
      router.patch(
        route("man-management.anggota.patch"),
        {
          _token: tokens,
          ...editedAnggota.value,
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            fetchData();
            updateDialog.value = false;
          },
        }
      );
      return;
    }
    if (fileupload) {
      if (!selectedFile.value) return;
      router.post(
        route("man-management.anggota.store"),
        {
          _token: tokens,
          fileUpload: selectedFile.value,
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            const flash = page.props.flash;
            createDialog.value = false;
            if (flash?.notification ?? null) {
              for (const [i, n] of flash.notification.entries()) {
                setTimeout(() => {
                  toast.add({
                    severity: n.type,
                    summary: n.type == "success" ? "Berhasil" : "Gagal",
                    detail: n.message,
                    life: 3000,
                  });
                }, i * 500);
              }
            }
            fetchData();
          },
        }
      );
    } else {
      if (!form.tipe) {
        form.errors.tipe = "Tipe belum diisi";
        return;
      } else form.errors.tipe = null;

      form._token = tokens;
      form.post(route("man-management.anggota.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          form.reset();
          fetchData();
          createDialog.value = false;
        },
      });
    }
  } catch (error) {
    console.error(error);
  }
};
const confirm = useConfirm();
const deleteAnggota = (data) => {
  confirm.require({
    message: "Apakah kamu yakin menghapus Pegawai ini di tim?",
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
      router.delete(route("man-management.anggota.destroy", { id: data.id }), {
        _token: tokens,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          fetchData();
        },
      });
    },
  });
};
const downloadTemplate = () => {
  window.location.href = "/man-management/download-template/keanggotaan";
};
</script>

<style scoped></style>
