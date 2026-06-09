<template>
  <Head title="MyLembur" />
  <SimpleLayout :is-open="isSidebarOpen">
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div
          class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0"
        >
          Lembur Saya
        </div>
      </div>
      <DataTable
        :value="paginatedItem.data"
        class="w-full text-sm"
        lazy
        paginator
        showGridlines
        stripedRows
        :rows="paginatedItem.per_page"
        :first="(paginatedItem.current_page - 1) * paginatedItem.per_page"
        :total-records="paginatedItem.total"
        :rows-per-page-options="[5, 10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortField"
        :sort-order="sortOrder"
        filterDisplay="row"
        @page="fetchData"
        @sort="fetchData"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
      >
        <template #empty>
          <div class="text-center">Data tidak ada</div>
        </template>
        <Column
          header="Tim Kerja"
          field="lembur.tim.label"
          sortable
          :show-filter-menu="false"
        >
          <template #filter>
            <InputText
              v-model="filterModel.tim_kerja"
              class="text-sm"
              fluid
              placeholder="Cari tim kerja"
            />
          </template>
        </Column>
        <Column
          header="Tanggal"
          sortable
          :show-filter-menu="false"
          field="tanggal"
        >
          <template #body="{ data }">
            {{ formatDateOnly(data.tanggal) }}
          </template>
          <template #filter>
            <InputText
              v-model="filterModel.tanggal"
              class="text-sm"
              fluid
              placeholder="Cari tanggal"
            />
          </template>
        </Column>
        <Column header="Jumlah Jam" sortable>
          <template #body="{ data }">
            {{ data.jumlah_jam + " Jam" }}
          </template>
        </Column>
        <Column
          header="Alasan Lembur"
          field="lembur.maksud_lembur"
          sortable
          :show-filter-menu="false"
        >
          <template #filter>
            <InputText
              v-model="filterModel.maksud_lembur"
              class="text-sm"
              fluid
              placeholder="Cari maksud"
            />
          </template>
        </Column>
        <Column header="Output Lembur">
          <template #body="{ data }">
            <div class="flex flex-wrap gap-6 items-center">
              <Badge
                v-if="!data.output"
                severity="secondary"
                value="Belum diisi"
              />
              <Button
                v-if="!data.output && data.status != '4' && data.status != '2'"
                @click="fillOutput(data)"
                v-tooltip.top="'Isi output'"
                icon="pi pi-pencil"
                variant="outlined"
                rounded
                class="mr-2"
                severity="success"
              />
            </div>
            <span
              @click="fillOutput(data)"
              v-if="data.output"
              class="cursor-pointer whitespace-pre-wrap"
              v-tooltip.top="'Klik untuk update output'"
              >{{ data.output }}</span
            >
          </template>
        </Column>
        <Column header="Catatan" class="min-w-[180px]">
          <template #body="{ data }">
            <Badge
              v-if="!data.catatan"
              severity="secondary"
              value="Tidak ada catatan"
            />
            <span v-else>{{ data.catatan }}</span>
          </template>
        </Column>
        <Column
          header="Status"
          field="status"
          sortable
          style="text-align: center"
          :show-filter-menu="false"
        >
          <template #body="{ data }">
            <div class="flex flex-col items-center justify-center gap-1">
              <Badge
                size="small"
                :value="data.status_detail || data.status"
                :severity="
                  String(data.status) === '1'
                    ? 'warn'
                    : String(data.status) === '2'
                    ? 'success'
                    : String(data.status) === '3'
                    ? 'danger'
                    : String(data.status) === '4'
                    ? 'info'
                    : String(data.status) === '5'
                    ? 'contrast'
                    : 'secondary'
                "
              />
              <Badge
                size="small"
                severity="secondary"
                :value="formatDateTime(data.updated_at)"
              />
            </div>
          </template>
          <template #filter>
            <InputText
              v-model="filterModel.status"
              class="text-sm"
              fluid
              placeholder="Cari status"
            />
          </template>
        </Column>
      </DataTable>
    </div>
    <Dialog
      v-model:visible="outputDialog"
      modal
      header="Isi Output Lembur"
      class="min-w-[40vw]"
      position="top"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2">Tim Kerja</label>
          <InputText v-model="editedData.lembur.tim.label" fluid disabled />
        </div>
        <div>
          <label class="block font-bold mb-2">Alasan Lembur</label>
          <InputText v-model="editedData.lembur.maksud_lembur" fluid disabled />
        </div>
        <div>
          <label class="block font-bold mb-2">Output Lembur</label>
          <Textarea
            v-model="form.output"
            placeholder="Masukkan Output Lembur"
            rows="4"
            fluid
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancel"
          @click="outputDialog = false"
          size="small"
          severity="danger"
          autofocus
        />
        <Button
          @click="submit"
          label="Simpan"
          size="small"
          severity="success"
          autofocus
        />
      </template>
    </Dialog>
  </SimpleLayout>
</template>

<script setup>
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { onMounted, ref, watch } from "vue";

const isSidebarOpen = ref(true);
onMounted(() => {
  isSidebarOpen.value = false;
});
const page = usePage();
const searchField = ref(null);
const filterModel = ref({
  tim_kerja: null,
  tanggal: null,
  jam_mulai: null,
  jam_selesai: null,
  maksud_lembur: null,
  status: null,
});
const formatDateTime = (dateString) => {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date
    .toLocaleString("id-ID", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    })
    .replace(/\./g, ":"); // Ganti format titik (bawaan id-ID) menjadi titik dua untuk jam
};
const formatDateOnly = (dateString) => {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};
const formatTimeOnly = (timeString) => {
  if (!timeString) return "-";
  // timeString is typically "HH:mm:ss" or "HH:mm"
  return timeString.substring(0, 5);
};
const props = defineProps({
  lembur: {
    type: Object,
  },
});
const paginatedItem = ref(props.lembur);
watch(
  () => props.lembur,
  (value) => {
    paginatedItem.value = value;
  }
);
//paginated and search
const currentPage = ref(1);
const paginated = ref(5);
const sortField = ref(null);
const sortOrder = ref(null);
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
    const { data } = await axios.get(route("simple.my-lembur"), {
      params: {
        currentPage: currentPage.value,
        paginated: paginated.value,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
        searchField: searchField.value,
        filters: filterModel.value,
      },
    });
    paginatedItem.value = data;
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};
const delayedFetchData = debounce(() => {
  fetchData();
});
watch(searchField, () => delayedFetchData());
watch(filterModel, () => delayedFetchData(), { deep: true });

const editedData = ref({});
const outputDialog = ref(false);
const fillOutput = (data) => {
  editedData.value = { ...data };
  outputDialog.value = true;
  form.id = editedData.value.id;
  form.output = editedData.value.output;
};
const form = useForm({
  _token: null,
  id: null,
  output: null,
});
const submit = async (params) => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    form._token = tokens;
    form.post(route("simple.my-lembur.fill-output"), {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        outputDialog.value = false;
        form.reset();
      },
    });
  } catch (error) {}
};
</script>

<style scoped></style>
