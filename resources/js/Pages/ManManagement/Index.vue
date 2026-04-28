<template>
  <Head title="Home" />
  <AppLayout>
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
          Daftar Pegawai
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model.trim="searchField" placeholder="Cari Pegawai" />
          </IconField>
          <MultiSelect
            v-model="selectedColumns"
            :options="allColumns"
            option-label="header"
            placeholder="Pilih Kolom yang ditampilkan"
            :max-selected-labels="0"
            selected-items-label="{0} kolom terpilih"
            class="md:w-24-rem"
          />
          <Button
            icon="pi pi-download"
            @click="exportPegawai"
            rounded
            aria-label="Download"
            severity="success"
            class="mr-2 mb-2 lg:mb-0"
          />
          <Button
            @click="createDialog = true"
            severity="info"
            rounded
            class="mb-2 lg:mb-0"
          >
            <i class="pi pi-plus"></i>
            Tambah Pengguna Baru
          </Button>
        </div>
      </div>
      <DataTable
        :value="pegawai.data"
        class="w-full"
        lazy
        paginator
        :rows="pegawai.per_page"
        :first="(pegawai.current_page - 1) * pegawai.per_page"
        :total-records="pegawai.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortField"
        :sort-order="sortOrder"
        @page="fetchData"
        @sort="fetchData"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} pegawai"
      >
        <template #empty>
          <div class="text-center">Pegawai tidak ada</div>
        </template>
        <Column
          v-for="item in selectedColumns"
          :key="item.field"
          :field="item.field"
          :header="item.header"
          sortable
        />
        <Column :exportable="false" style="min-width: 12rem">
          <template #body="slotProps">
            <div class="flex justify-end gap-2 w-full">
              <Button
                icon="pi pi-pencil"
                variant="outlined"
                rounded
                class="mr-2"
                @click="updatePegawai(slotProps.data)"
              />
              <Button icon="pi pi-trash" variant="outlined" rounded severity="danger" />
            </div>
          </template>
        </Column>
      </DataTable>
      <Dialog
        v-model:visible="updateDialog"
        modal
        header="Edit Pegawai"
        class="min-w-[40vw]"
      >
        <div class="flex flex-col gap-6">
          <div>
            <label for="name" class="block font-bold mb-3">Nama Pegawai</label>
            <InputText
              id="name"
              v-model.trim="editedPegawai.name"
              required="true"
              autofocus
              fluid
              placeholder="Isikan Nama Pegawai"
              disabled
            />
          </div>
          <div>
            <label for="satker" class="block font-bold mb-3">Satuan Kerja</label>
            <InputText
              id="satker"
              v-model.trim="editedPegawai.satker"
              required="true"
              autofocus
              fluid
              placeholder="Isikan Satuan Kerja"
              disabled
            />
          </div>
          <div>
            <label for="golongan" class="block font-bold mb-3">Golongan</label>
            <InputText
              id="golongan"
              v-model.trim="editedPegawai.golongan"
              required="true"
              autofocus
              fluid
              placeholder="Isikan Golongan"
              disabled
            />
          </div>
          <div>
            <label for="jabatan" class="block font-bold mb-3">Jabatan</label>
            <InputText
              id="jabatan"
              v-model.trim="editedPegawai.jabatan"
              required="true"
              autofocus
              fluid
              placeholder="Isikan Jabatan"
              disabled
            />
          </div>
        </div>
        <template #footer>
          <Button
            label="Update dari SSO"
            @click="fetchFromSSO(editedPegawai.nip_lama)"
            size="small"
            severity="info"
            autofocus
          />
          <Button
            @click="updateDialog = false"
            label="Cancel"
            size="small"
            severity="danger"
            autofocus
          />
          <Button
            @click="uploadPegawai({ update: true })"
            label="Save"
            size="small"
            severity="success"
            autofocus
          />
        </template>
      </Dialog>
      <Dialog
        v-model:visible="createDialog"
        modal
        header="Tambah Pegawai Baru"
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
            <div v-if="form.errors?.tipe" class="text-red-500 text-sm mt-2">
              {{ form.errors?.tipe }}
            </div>
          </div>
          <template v-if="form.tipe == 'manual'">
            <div>
              <label for="nip_lama" class="block font-bold mb-3">NIP Lama</label>
              <div class="flex flex-row space-x-2">
                <InputText
                  v-model="searchNIP"
                  id="nip_lama"
                  required="true"
                  placeholder="Isikan NIP lama (full)"
                  autofocus
                  fluid
                />
                <Button severity="success" @click="getPegawai">
                  <i class="pi pi-search"></i>
                </Button>
              </div>
              <div v-if="errorForm.nip" class="text-red-500 text-sm mt-2">
                {{ errorForm.nip }}
              </div>
            </div>
            <div>
              <label for="name" class="block font-bold mb-3">Nama Pegawai</label>
              <InputText
                id="name"
                v-model="form.pegawai.nama"
                required="true"
                autofocus
                fluid
                disabled
              />
            </div>
            <div>
              <label for="satker" class="block font-bold mb-3">Satuan Kerja</label>
              <InputText
                id="satker"
                v-model="form.pegawai.kabupaten"
                required="true"
                autofocus
                fluid
                disabled
              />
            </div>
          </template>
          <template v-if="form.tipe == 'upload'">
            <div class="flex flex-row space-x-2">
              <Button @click="downloadTemplate" severity="info" class="lg:mb-0">
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
            <div v-if="errorForm.file" class="text-red-500 text-sm">
              {{ errorForm.file }}
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
            @click="uploadPegawai"
            label="Tambah"
            size="small"
            severity="success"
            autofocus
          />
        </template>
      </Dialog>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/ManManagement/AppLayout.vue";
import { ref } from "vue";
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import { watch } from "vue";
import * as XLSX from "xlsx";
import axios from "axios";

//props data defined
const props = defineProps({
  pegawai: {
    type: Object,
  },
});
const allColumns = [
  { field: "nip_lama", header: "NIP Lama" },
  { field: "nip", header: "NIP Baru" },
  { field: "name", header: "Nama Pegawai" },
  { field: "satker", header: "Satuan Kerja" },
  { field: "email", header: "Email" },
  { field: "golongan", header: "Golongan" },
  { field: "jabatan", header: "Jabatan" },
  { field: "username", header: "Username SSO" },
];
const selectedColumns = ref([allColumns[0], allColumns[1], allColumns[2], allColumns[3]]);
const searchField = ref(null);
//paginated and search
const currentPage = ref((props.pegawai.current_page - 1) * props.pegawai.per_page);
const paginated = ref(props?.pegawai?.per_page ?? 10);
const sortField = ref(null);
const sortOrder = ref(null);
const fetchData = (event = null) => {
  currentPage.value = event ? Math.floor(event.first / event.rows) + 1 : 1;
  paginated.value = event?.rows ?? paginated.value;
  router.get(
    route("man-management.index"),
    {
      currentPage: currentPage.value,
      paginated: paginated.value,
      sortField: event?.sortField ?? sortField.value,
      sortOrder: event?.sortOrder ?? sortOrder.value,
      searchField: searchField.value,
      listColumn: selectedColumns.value.map((col) => col.field),
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
const updateDialog = ref(false);
const editedPegawai = ref({});
const updatePegawai = (data) => {
  updateDialog.value = true;
  editedPegawai.value = { ...data };
};
//submit biasa
const searchNIP = ref(null);
const getPegawai = async () => {
  try {
    const { data } = await axios.get(route("sso-api", { nip_lama: searchNIP.value }));
    if (data?.[0]?.attributes ?? null) {
      form.pegawai.nama =
        data?.[0]?.attributes?.["attribute-nama"]?.[0] ?? "Tidak ditemukan";
      form.pegawai.kabupaten =
        data?.[0]?.attributes?.["attribute-kabupaten"]?.[0] ?? "Tidak ditemukan";
    }
  } catch (error) {
    console.error(error);
  }
};
const form = useForm({
  tipe: null,
  pegawai: {
    nama: null,
    kabupaten: null,
  },
});
//update dari sso
const fetchFromSSO = async ($nip_lama) => {
  const { data } = await axios.get(route("sso-api", { nip_lama: $nip_lama }));
  Object.entries(editedPegawai.value).forEach(([key, value]) => {
    if (key != "id" && key != "satker") {
      editedPegawai.value[key] =
        data?.[0]?.attributes?.["attribute-" + key]?.[0] ?? "not found";
      if (key == "name") {
        editedPegawai.value[key] =
          data?.[0]?.attributes?.["attribute-nama"]?.[0] ?? "not found";
      }
      if (key == "nip_lama") {
        editedPegawai.value[key] =
          data?.[0]?.attributes?.["attribute-nip-lama"]?.[0] ?? "not found";
      }
    }
  });
};
//upload pegawai
const createDialog = ref(false);
const errorForm = ref({ nip: null, file: null });
const uploadPegawai = async ({ update = false }) => {
  const { data: tokens } = await axios.get(route("api.token.csrf"));
  if (update) {
    router.patch(
      route("man-management.patch-pegawai"),
      {
        _token: tokens,
        ...editedPegawai.value,
      },
      {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          updateDialog.value = false;
          fetchData();
        },
        onError: () => {
          updateDialog.value = true;
        },
      }
    );
  } else {
    if (!form.tipe) {
      form.errors.tipe = "Tipe belum diisi";
      return;
    } else form.errors.tipe = null;
    if (form.tipe == "manual" && !searchNIP.value) {
      errorForm.value.nip = "NIP belum diisi";
      return;
    } else errorForm.value.nip = null;
    if (form.tipe == "upload" && !selectedFile.value) {
      errorForm.value.file = "File belum diupload";
      return;
    } else errorForm.value.file = null;

    const data = !searchNIP.value
      ? selectedFile.value.map((item) => item[0]).slice(1)
      : [searchNIP.value];
    const { data: check } = await axios.get(route("man-management.check-pegawai"));
    const setCheck = new Set(check);
    const toUpload = data.filter((item) => !setCheck.has(item));
    if (toUpload.length > 0) {
      for (const n of toUpload) {
        router.post(
          route("man-management.upload-pegawai"),
          {
            _token: tokens,
            nip: n,
          },
          {
            preserveScroll: true,
            preserveState: true,
          }
        );
      }
      createDialog.value = false;
    }
  }
};
const exportPegawai = () => {
  const url =
    route("man-management.export-pegawai") +
    "?" +
    new URLSearchParams({ kabupaten: searchField.value }).toString();
  window.location.href = url;
};
const downloadTemplate = () => {
  window.location.href = "/man-management/download-template/pegawai";
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
watch(createDialog, () => {
  if (createDialog.value == false) {
    form.reset();
    searchNIP.value = null;
    selectedFile.value = null;
    errorForm.value = { nip: null, file: null };
  }
});
</script>

<style scoped></style>
