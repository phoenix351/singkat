<template>
  <Head title="Summary" />
  <SimpleLayout>
    <div class="text-xl font-bold mb-4">Ringkasan Data Lembur</div>
    <div class="flex flex-wrap items-end gap-2">
      <div class="flex flex-col gap-2">
        <label class="font-bold">Rentang Waktu</label>
        <DatePicker
          v-model="search.range_waktu"
          selection-mode="range"
          :manual-input="false"
          date-format="dd MM yy"
          :show-clear="true"
          fluid
          class="w-72"
          placeholder="Isi rentang waktu"
          input-class="h-10"
        />
      </div>
      <div class="flex flex-col gap-2">
        <label class="font-bold">Jenis Data</label>
        <Select
          v-model="search.data_type"
          placeholder="Pilih Jenis Data"
          fluid
          :options="[
            { label: 'Ringkasan Tim', value: 'tim' },
            { label: 'Ringkasan Pegawai', value: 'pegawai' },
          ]"
          optionLabel="label"
          optionValue="value"
          class="w-48"
        />
      </div>
      <div class="space-x-2">
        <Button @click="fetchData" icon="pi pi-search" class="mb-0" />
      </div>
    </div>
    <div class="card mt-4">
      <div class="flex justify-end mb-4">
        <IconField>
          <InputIcon>
            <i class="pi pi-search" />
          </InputIcon>
          <InputText placeholder="Cari Pegawai/Tim" v-model="search.pegawai_tim" />
        </IconField>
      </div>
      <DataTable
        :value="paginatedItem.data"
        class="w-full text-sm"
        lazy
        paginator
        showGridlines
        :rows="paginatedItem.per_page"
        :first="(paginatedItem.current_page - 1) * paginatedItem.per_page"
        :total-records="paginatedItem.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortField"
        :sort-order="sortOrder"
        @page="fetchData"
        @sort="fetchData"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
      >
        <template #empty>
          <div class="text-center">Data bulan ini belum ada yang sudah disetujui</div>
        </template>
        <Column field="label" header="Nama Pegawai/Tim"></Column>
        <Column field="total_lembur" header="Total Pengajuan Lembur"></Column>
        <Column field="durasi_lembur" header="Total Durasi Lembur"></Column>
      </DataTable>
    </div>
  </SimpleLayout>
</template>

<script setup>
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref, watch } from "vue";

const page = usePage();

const currentYear = new Date().getFullYear();
const search = ref({
  tahun: currentYear,
  bulan: 0,
  range_waktu: [new Date(currentYear, 0, 1), new Date()],
  data_type: "pegawai",
  pegawai_tim: null,
});
const props = defineProps({
  data: {
    type: Object,
  },
});
const paginatedItem = ref(props.data);
const currentPage = ref(1);
const paginated = ref(10);
const sortField = ref(null);
const sortOrder = ref(null);
watch(
  () => props.data,
  (data) => {
    paginatedItem.value = data;
  }
);
const fetchData = async (event = null) => {
  if (event) {
    if (event.first !== undefined && event.rows !== undefined) {
      currentPage.value = Math.floor(event.first / event.rows) + 1;
      paginated.value = event.rows;
    }
    if (event.sortField !== undefined) sortField.value = event.sortField;
    if (event.sortOrder !== undefined) sortOrder.value = event.sortOrder;
  }

  try {
    const { data } = await axios.get(route("simple.summary"), {
      params: {
        currentPage: currentPage.value,
        paginated: paginated.value,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
        filters: search.value,
      },
    });
    paginatedItem.value = data;
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};
const delayedFetchData = debounce(() => {
  fetchData();
}, 500);
watch(
  () => search.value.pegawai_tim,
  () => {
    delayedFetchData();
  }
);
</script>
