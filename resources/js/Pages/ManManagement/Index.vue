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
            rounded
            aria-label="Download"
            severity="success"
            class="mr-2 mb-2 lg:mb-0"
          />
          <Button severity="info" rounded class="mb-2 lg:mb-0">
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
            />
            <!-- :invalid="submitted && !product.name" -->
            <!-- <small v-if="submitted && !product.name" class="text-red-500"
              >Name is required.</small
            > -->
          </div>
        </div>
      </Dialog>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/ManManagement/AppLayout.vue";
import { ref } from "vue";
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import { watch } from "vue";

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
const uploadPegawai = async () => {
  const { data } = await axios.get(route("man-management.get-current-pegawai"));
  const { data: check } = await axios.get(route("man-management.check-pegawai"));
  const setCheck = new Set(check);
  const toUpload = data.filter((item) => !setCheck.has(item));
  if (toUpload.length > 0) {
    for (const n of toUpload) {
      await axios
        .post(route("man-management.upload-pegawai"), {
          nip: n,
        })
        .then((response) => {
          console.log(response);
        });
      console.log(n);
    }
  }
};
</script>

<style scoped></style>
