<template>

  <Head title="Lembur" />
  <SimpleLayout :is-open="isSidebarOpen">
    <div class="text-xl font-bold mb-4">Verifikasi Kepala Bagian Umum</div>
    <div class="flex flex-wrap items-end gap-4 mb-4">
      <div class="flex flex-col gap-2">
        <label class="font-bold">Tahun</label>
        <Select v-model="filterModel.tahun" placeholder="Pilih tahun" :options="yearDrop" optionLabel="label"
          optionValue="value" class="w-40" />
      </div>
      <div class="flex flex-col gap-2">
        <label class="font-bold">Bulan</label>
        <Select v-model="filterModel.bulan" placeholder="Pilih bulan" :options="monthDrop" optionLabel="label"
          optionValue="value" class="w-48" />
      </div>
      <div>
        <Button @click="fetchData" icon="pi pi-search" class="mb-0" />
      </div>
    </div>
    <!-- Keterangan / Panduan Aksi Verifikasi -->
    <div
      class="bg-blue-50/80 border border-blue-200 text-blue-900 rounded-xl p-3 mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
      <div class="flex items-center gap-2 font-semibold shrink-0">
        <i class="pi pi-info-circle text-blue-600 text-sm"></i>
        <span>Keterangan Aksi Verifikasi:</span>
      </div>
      <div class="flex flex-wrap items-center gap-4 text-gray-700">
        <div class="flex items-center gap-1.5">
          <span
            class="inline-flex items-center justify-center w-6 h-6 rounded-full border border-green-500 text-green-600 bg-white shadow-xs">
            <i class="pi pi-check text-xs font-bold"></i>
          </span>
          <span> Menerima pengajuan sesuai jumlah jam asal</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span
            class="inline-flex items-center justify-center w-6 h-6 rounded-full border border-amber-500 text-amber-600 bg-white shadow-xs">
            <i class="pi pi-clock text-xs font-bold"></i>
          </span>
          <span> Menerima pengajuan dengan mengubah jumlah jam lembur</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span
            class="inline-flex items-center justify-center w-6 h-6 rounded-full border border-red-500 text-red-600 bg-white shadow-xs">
            <i class="pi pi-times text-xs font-bold"></i>
          </span>
          <span> Menolak pengajuan lembur</span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div></div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText placeholder="Cari Pegawai" v-model="searchField" />
          </IconField>
        </div>
      </div>
      <DataTable :value="paginatedItem.data" class="w-full text-sm" lazy paginator showGridlines stripedRows
        v-model:expandedRows="expandedRows" dataKey="id" :rowExpandable="isRowExpandable" :rows="paginatedItem.per_page"
        :first="(paginatedItem.current_page - 1) * paginatedItem.per_page" :total-records="paginatedItem.total"
        :rows-per-page-options="[5, 10, 20, 50, 100]" :removable-sort="true" :sort-field="sortField"
        :sort-order="sortOrder" filterDisplay="row" @page="fetchData" @sort="fetchData"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data">
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
        <Column header="Tim Kerja" sortable :show-filter-menu="false">
          <template #body="{ data }">
            <span v-if="data.pegawai && data.pegawai.length > 0" :class="{ 'font-bold': !data.tim_id }">
              {{ data.tim_id ? data.tim_kerja : (data.tim_penanggung_jawab_id ? 'Lintas Tim Kerja (PJ: ' + data.pj_kerja
                + ')' : 'Lintas Tim Kerja') }}
            </span>
            <span v-else>-</span>
          </template>
          <template #filter>
            <InputText v-model="filterModel.tim_kerja" class="text-sm" fluid placeholder="Cari tim kerja" />
          </template>
        </Column>
        <Column header="Tanggal" :show-filter-menu="false" field="tanggal" sortable>
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? formatDateOnly(data.pegawai[0].tanggal)
                : "-"
            }}
          </template>
        </Column>
        <Column header="Jumlah Jam" sortable>
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? data.pegawai[0].jumlah_jam + " Jam"
                : "-"
            }}
          </template>
        </Column>
        <Column class="whitespace-nowrap" header="Status Pengajuan" field="status_pengajuan" sortable>
          <template #body="{ data }">
            <div class="flex flex-col gap-1">
              <Badge size="small" v-for="item in getStatusCounts(data.pegawai)" :key="item.label"
                :value="`${item.count} ${item.label}`" :severity="item.code === '1'
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
                  " />
            </div>
          </template>
        </Column>
        <Column header="Alasan Lembur" field="maksud_lembur" sortable>
          <template #filter>
            <InputText v-model="filterModel.maksud_lembur" class="text-sm" fluid placeholder="Cari alasan lembur" />
          </template>
        </Column>
        <Column header="Link" field="link_dokumentasi">
          <template #body="{ data }">
            <Button v-if="data.link_dokumentasi" icon="pi pi-external-link" variant="outlined" rounded class="mr-2"
              :severity="'info'" @click="toDocumentation(data.link_dokumentasi)"></Button>
            <div v-else>-</div>
          </template>
        </Column>
        <Column header="Aksi" :exportable="false" style="min-width: 145px">
          <template #body="slotProps">
            <div class="flex justify-end gap-1.5 w-full">
              <Button @click="updateData({ data: slotProps.data, status: 'setuju' })" icon="pi pi-check"
                variant="outlined" rounded severity="success" v-tooltip.top="'Setujui (sesuai jam pengajuan)'" />
              <Button @click="updateData({ data: slotProps.data, status: 'sesuaikan' })" icon="pi pi-clock"
                variant="outlined" rounded severity="warn" v-tooltip.top="'Setujui dengan Penyesuaian Jam'" />
              <Button @click="updateData({ data: slotProps.data, status: 'ditolak' })" icon="pi pi-times"
                variant="outlined" rounded severity="danger" v-tooltip.top="'Tolak Pengajuan'" />
            </div>
          </template>
        </Column>
        <template #expansion="slotProps">
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex mb-2 justify-between flex-wrap items-center gap-2">
              <h5 class="font-bold">Daftar Pegawai Lembur</h5>
              <div class="flex justify-end w-auto gap-2">
                <Button @click="updateData({ status: 'setuju', individual: true })" v-if="selectedPegawai.length > 0"
                  rounded severity="success" size="small">
                  <i class="pi pi-check" />
                  Terima
                </Button>
                <Button @click="updateData({ status: 'sesuaikan', individual: true })" v-if="selectedPegawai.length > 0"
                  rounded severity="warn" size="small">
                  <i class="pi pi-clock" />
                  Sesuaikan Jam
                </Button>
                <Button @click="updateData({ status: 'ditolak', individual: true })" v-if="selectedPegawai.length > 0"
                  rounded severity="danger" size="small">
                  <i class="pi pi-times" />
                  Tolak
                </Button>
              </div>
            </div>
            <DataTable v-model:selection="selectedPegawai" :value="slotProps.data.pegawai" data-key="id" showGridlines
              paginator :rows="10" :rowsPerPageOptions="[10, 20]" size="small">
              <Column selection-mode="multiple" />
              <Column header="No" style="width: 3rem">
                <template #body="itemProps">{{ itemProps.index + 1 }}</template>
              </Column>
              <Column header="Nama Pegawai">
                <template #body="{ data }">{{ data.pegawai?.name }}</template>
              </Column>
              <Column header="NIP">
                <template #body="{ data }">{{ data.pegawai?.nip }}</template>
              </Column>
              <Column header="Output Lembur">
                <template #body="{ data }">
                  <Badge v-if="!data.output" severity="secondary" value="Belum diisi" />
                  <span v-if="data.output" class="whitespace-pre-wrap">{{
                    data.output
                  }}</span>
                </template>
              </Column>
              <Column class="whitespace-nowrap" header="Catatan">
                <template #body="{ data }">
                  <Badge v-if="!data.catatan" severity="secondary" value="Tidak ada catatan" />
                  <span v-else>{{ data.catatan }}</span>
                </template>
              </Column>
              <Column class="whitespace-nowrap" header="Status" style="text-align: center">
                <template #body="{ data }">
                  <div class="flex flex-col items-center justify-center gap-1">
                    <Badge size="small" :value="data.status_detail || data.status" :severity="String(data.status) === '1'
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
                      " />
                    <Badge size="small" severity="secondary" :value="formatDateTime(data.updated_at)" />
                  </div>
                </template>
              </Column>
              <Column header="Terakhir diedit">
                <template #body="{ data }">{{
                  data.edited?.username
                }}</template>
              </Column>
            </DataTable>
          </div>
        </template>
      </DataTable>
    </div>
    <Dialog v-model:visible="confirmDialog" modal :header="confirmDialogHeader" class="w-full md:w-[32vw]">
      <div class="flex flex-col gap-4">
        <div class="text-sm text-gray-700 leading-relaxed">
          <div v-if="!confirmData.individual"
            class="mb-3 text-xs text-amber-800 bg-amber-50 p-2.5 rounded-lg border border-amber-200 flex items-center gap-1.5">
            <i class="pi pi-exclamation-triangle text-amber-600"></i>
            <span>Tindakan ini akan diterapkan ke <strong>seluruh pegawai</strong> dalam pengajuan ini.</span>
          </div>
          <div v-else
            class="mb-3 text-xs text-blue-800 bg-blue-50 p-2.5 rounded-lg border border-blue-200 flex items-center gap-1.5">
            <i class="pi pi-users text-blue-600"></i>
            <span>Tindakan ini akan diterapkan ke <strong>{{ selectedPegawai.length }} pegawai terpilih</strong>.</span>
          </div>

          <p v-if="confirmData.status === 'setuju'">
            Apakah kamu yakin ingin <strong>menyetujui</strong> pengajuan lembur ini sesuai jumlah jam yang diajukan?
          </p>
          <p v-else-if="confirmData.status === 'sesuaikan'">
            Kamu akan <strong>menyetujui</strong> pengajuan lembur ini sekaligus <strong>menyesuaikan jumlah
              jam</strong>
            lembur.
          </p>
          <p v-else-if="confirmData.status === 'ditolak'">
            Apakah kamu yakin ingin <strong>menolak</strong> pengajuan lembur ini?
          </p>
        </div>

        <!-- Input Khusus Sesuaikan Jam -->
        <div v-if="confirmData.status === 'sesuaikan'" class="flex flex-col gap-3 p-3.5 bg-gray-50 rounded-lg">
          <div>
            <label class="block font-bold text-xs mb-1.5 text-gray-800">
              Jumlah Jam Lembur Baru <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-2">
              <InputNumber v-model="confirmData.jumlah_jam" :min="1" :max="24" :minFractionDigits="0"
                :maxFractionDigits="2" showButtons buttonLayout="horizontal" class="w-full" fluid>
                <template #incrementbuttonicon>
                  <span class="pi pi-plus" />
                </template>
                <template #decrementbuttonicon>
                  <span class="pi pi-minus" />
                </template>
              </InputNumber>
              <span class="font-bold text-gray-700 whitespace-nowrap text-sm">Jam</span>
            </div>
            <small v-if="confirmData.current_jam" class="text-gray-500 text-xs mt-1 block">
              Jam pengajuan asal: <span class="font-medium text-gray-700">{{ confirmData.current_jam }} Jam</span>
            </small>
          </div>

          <div>
            <label class="block font-bold text-xs mb-1.5 text-gray-800">
              Alasan / Catatan Penyesuaian (Opsional)
            </label>
            <Textarea v-model="confirmData.catatan" rows="3"
              placeholder="Contoh: Disesuaikan dengan beban kerja aktual..." fluid class="w-full text-sm" />
          </div>
        </div>

        <!-- Input Khusus Tolak -->
        <div v-if="confirmData.status === 'ditolak'">
          <label class="block font-bold text-xs mb-1.5 text-gray-800">Catatan Penolakan <span
              class="text-red-500">*</span></label>
          <Textarea v-model="confirmData.catatan" rows="3" placeholder="Alasan penolakan..." fluid
            class="w-full text-sm" />
        </div>
      </div>
      <template #footer>
        <Button label="Batal" @click="confirmDialog = false" variant="outlined" severity="secondary" size="small" />
        <Button v-if="confirmData.status === 'setuju'" label="Setujui" icon="pi pi-check" severity="success"
          @click="processUpdate" size="small" />
        <Button v-else-if="confirmData.status === 'sesuaikan'" label="Setujui & Ubah Jam" icon="pi pi-check"
          severity="warn" :disabled="!confirmData.jumlah_jam || confirmData.jumlah_jam <= 0" @click="processUpdate"
          size="small" />
        <Button v-else-if="confirmData.status === 'ditolak'" label="Tolak" icon="pi pi-times" severity="danger"
          :disabled="!confirmData.catatan || confirmData.catatan.trim() === ''" @click="processUpdate" size="small" />
      </template>
    </Dialog>
  </SimpleLayout>
</template>

<script setup>
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";

const isSidebarOpen = ref(true);
onMounted(() => {
  isSidebarOpen.value = false;
});
const currentYear = new Date().getFullYear();
const currentMonth = new Date().getMonth() + 1;

const yearDrop = ref(
  Array.from({ length: 10 }, (_, i) => ({
    label: (currentYear - i).toString(),
    value: currentYear - i,
  }))
);

const monthDrop = ref([
  { label: "Januari", value: 1 },
  { label: "Februari", value: 2 },
  { label: "Maret", value: 3 },
  { label: "April", value: 4 },
  { label: "Mei", value: 5 },
  { label: "Juni", value: 6 },
  { label: "Juli", value: 7 },
  { label: "Agustus", value: 8 },
  { label: "September", value: 9 },
  { label: "Oktober", value: 10 },
  { label: "November", value: 11 },
  { label: "Desember", value: 12 },
]);

const searchField = ref(null);
const filterModel = ref({
  tahun: currentYear,
  bulan: currentMonth,
  tim_kerja: null,
  maksud_lembur: null,
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
    .replace(/\./g, ":");
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
    const { data } = await axios.get(route("simple.lembur.verify-kabag"), {
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

//submit
const selectedPegawai = ref([]);
const confirmDialog = ref(false);
const confirmData = ref({
  data: {},
  status: "",
  individual: false,
  catatan: "",
  jumlah_jam: null,
  current_jam: null,
});

const confirmDialogHeader = computed(() => {
  if (confirmData.value.status === "sesuaikan")
    return "Konfirmasi Penyesuaian Jam Lembur";
  if (confirmData.value.status === "setuju")
    return "Konfirmasi Persetujuan Lembur";
  if (confirmData.value.status === "ditolak")
    return "Konfirmasi Penolakan Lembur";
  return "Konfirmasi";
});

const updateData = ({ data = {}, status, individual = false }) => {
  let defaultJam = null;
  if (!individual) {
    defaultJam = data.pegawai?.[0]?.jumlah_jam ?? null;
  } else if (selectedPegawai.value.length > 0) {
    defaultJam = selectedPegawai.value[0]?.jumlah_jam ?? null;
  }

  confirmData.value = {
    data,
    status,
    individual,
    catatan: "",
    jumlah_jam: defaultJam,
    current_jam: defaultJam,
  };
  confirmDialog.value = true;
};

const processUpdate = async () => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    router.patch(
      route("simple.lembur.verify-kabag-patch"),
      {
        _token: tokens,
        individual: confirmData.value.individual,
        status: confirmData.value.status,
        catatan: confirmData.value.catatan,
        jumlah_jam: confirmData.value.jumlah_jam,
        lembur_id: confirmData.value.data.id,
        lembur_pegawai: selectedPegawai.value.map((p) => p.id),
      },
      {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          confirmDialog.value = false;
          selectedPegawai.value = [];
          fetchData();
        },
      }
    );
  } catch (error) {
    console.error(error);
  }
};
const toDocumentation = (link) => {
  const url = link.startsWith("http") ? link : `https://${link}`;
  window.open(url, "_blank", "noopener,noreferrer");
};
</script>

<style scoped></style>
