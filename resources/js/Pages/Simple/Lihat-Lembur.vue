<template>
  <Head title="Lembur" />
  <SimpleLayout :is-open="isSidebarOpen">
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
          Daftar Pengajuan Lembur se-BPS Provinsi Sulawesi Utara
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText placeholder="Cari Pegawai" v-model="searchField" />
          </IconField>
        </div>
      </div>
      <DataTable
        :value="paginatedItem.data"
        class="w-full text-sm"
        lazy
        paginator
        showGridlines
        stripedRows
        v-model:expandedRows="expandedRows"
        dataKey="id"
        :rowExpandable="isRowExpandable"
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
        <Column expander style="width: 3rem" />
        <Column header="Pegawai">
          <template #body="{ data }">
            <span v-if="data.pegawai && data.pegawai.length === 1">
              {{ data.pegawai[0]?.pegawai?.name }}
            </span>
            <span v-else-if="data.pegawai && data.pegawai.length > 1">
              {{ data.pegawai.length }} Pegawai
            </span>
            <span v-else>-</span>
          </template>
        </Column>
        <Column header="Tim Kerja" field="tim_kerja" sortable :showFilterMenu="false">
          <template #body="{ data }">
            <span
              v-if="data.pegawai && data.pegawai.length > 0"
              :class="{ 'font-bold': !data.tim_id }"
            >
              {{
                data.tim_id
                  ? data.tim_kerja
                  : data.tim_penanggung_jawab_id
                  ? "Lintas Tim Kerja (PJ: " + (data.pj_kerja || "Tim") + ")"
                  : "Lintas Tim Kerja"
              }}
            </span>
            <span v-else>-</span>
          </template>
          <template #filter>
            <InputText
              v-model="filterModel.tim_kerja"
              class="text-sm"
              fluid
              placeholder="Cari tim kerja"
            />
          </template>
        </Column>
        <Column header="Tanggal" :show-filter-menu="false" sortable field="tanggal">
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? formatDateOnly(data.pegawai[0].tanggal)
                : "-"
            }}
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
        <Column header="Jumlah Jam" field="jumlah_jam" sortable>
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? data.pegawai[0].jumlah_jam + " Jam"
                : "-"
            }}
          </template>
        </Column>
        <Column
          header="Status Pengajuan"
          class="whitespace-nowrap"
          sortable
          field="status_pengajuan"
          :show-filter-menu="false"
        >
          <template #body="{ data }">
            <div class="flex flex-col gap-1">
              <Badge
                size="small"
                v-for="item in getStatusCounts(data.pegawai)"
                :key="item.label"
                :value="`${item.count} ${item.label}`"
                :severity="
                  item.code === '1'
                    ? 'warn'
                    : item.code === '2'
                    ? 'success'
                    : item.code === '3'
                    ? 'danger'
                    : item.code === '4'
                    ? 'info'
                    : item.code === '5'
                    ? 'contrast'
                    : 'secondary'
                "
              />
            </div>
          </template>
          <template #filter>
            <Select
              v-model="filterModel.status"
              :options="statusOptions"
              option-label="label"
              option-value="value"
              placeholder="Pilih status"
              show-clear
              class="text-sm"
              fluid
            />
          </template>
        </Column>
        <Column
          header="Alasan Lembur"
          field="maksud_lembur"
          sortable
          :show-filter-menu="false"
        >
          <template #filter>
            <InputText
              v-model="filterModel.maksud_lembur"
              class="text-sm"
              fluid
              placeholder="Cari alasan lembur"
            />
          </template>
        </Column>
        <template #expansion="slotProps">
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex mb-2 justify-between flex-wrap items-center">
              <h5 class="font-bold">Daftar Pegawai Lembur</h5>
              <Button
                v-if="canManage(slotProps.data)"
                @click="addPegawaiTo(slotProps.data)"
                rounded
                class="lg:mb-0"
              >
                <i class="pi pi-plus"></i>
                Tambah Pegawai
              </Button>
            </div>
            <DataTable
              :value="slotProps.data.pegawai"
              paginator
              :rows="10"
              :rowsPerPageOptions="[10, 20]"
              showGridlines
              size="small"
            >
              <Column header="No" style="width: 3rem">
                <template #body="itemProps">{{ itemProps.index + 1 }}</template>
              </Column>
              <Column header="Nama Pegawai">
                <template #body="{ data }">{{ data.pegawai?.name }}</template>
              </Column>
              <Column header="NIP">
                <template #body="{ data }">{{ data.pegawai?.nip }}</template>
              </Column>
              <Column
                header="Status"
                class="whitespace-nowrap"
                style="text-align: center"
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
              </Column>
            </DataTable>
          </div>
        </template>
      </DataTable>
    </div>
  </SimpleLayout>
</template>

<script setup>
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { useConfirm } from "primevue";
import { computed, onMounted, ref, watch } from "vue";

const isSidebarOpen = ref(true);
onMounted(() => {
  isSidebarOpen.value = false;
});
const page = usePage();
const searchField = ref(null);
const canManage = (data) => {
  const currentUserId = Number(page.props.auth?.user?.id);
  return (
    currentUserId > 0 &&
    data?.pegawai?.length > 0 &&
    data.pegawai.every((pegawai) => Number(pegawai.created_by) === currentUserId)
  );
};
const filterModel = ref({
  tim_kerja: null,
  tanggal: null,
  maksud_lembur: null,
  status: null,
});
const allStatusOptions = [
  { label: "Pending", value: "1" },
  { label: "Disetujui Katim", value: "2" },
  { label: "Ditolak Katim", value: "3" },
  { label: "Disetujui Kabag", value: "4" },
  { label: "Ditolak Kabag", value: "5" },
];
const statusOptions = computed(() => {
  const canViewAllStatuses = ["admin", "operator"].includes(page.props.auth?.role);
  return canViewAllStatuses
    ? allStatusOptions
    : allStatusOptions.filter((status) => ["2", "4"].includes(status.value));
});

const getStatusCounts = (pegawai) => {
  if (!pegawai) return [];
  const counts = {};
  pegawai.forEach((p) => {
    const statusLabel = p.status_detail || p.status || "Unknown";
    const statusCode = String(p.status);
    const key = `${statusCode}_${statusLabel}`;
    if (!counts[key]) {
      counts[key] = {
        label: statusLabel,
        code: statusCode,
        count: 0,
      };
    }
    counts[key].count++;
  });
  return Object.values(counts);
};
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
const expandedRows = ref({});
const isRowExpandable = (data) => {
  return data.pegawai && data.pegawai.length > 1;
};

const props = defineProps({
  lembur: {
    type: Object,
  },
  tim: {
    type: Array,
  },
  keanggotaan: {
    type: Array,
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
const paginated = ref(10);
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
    const { data } = await axios.get(route("simple.lembur.lihat"), {
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
</script>

<style scoped></style>
