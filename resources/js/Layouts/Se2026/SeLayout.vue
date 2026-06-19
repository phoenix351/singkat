<template>
  <div class="min-h-screen bg-gray-50 flex overflow-hidden">
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300">
      <header
        class="sticky top-0 z-30 border-b backdrop-blur-md transition-colors bg-white/80 dark:bg-slate-900/80 border-slate-200 dark:border-slate-800"
      >
        <div
          class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between"
        >
          <div class="flex items-center gap-3">
            <img
              src="../../../../public/images/logo/logo-se2026-singkat.png"
              alt="Logo BPS"
              class="w-10 h-10 rounded-full shadow-lg opacity-80"
            />
            <div>
              <h1
                class="text-sm sm:text-base font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2"
              >
                Dashboard Sensus Ekonomi 2026
              </h1>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Provinsi Sulawesi Utara (versi beta-1.0)
              </p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <Button
              @click="openUploadDialog"
              icon="pi pi-upload"
              size="small"
              class="rounded-[12px]"
              severity="secondary"
              label="Upload Data"
            />
            <Button
              @click="openLogsDialog"
              icon="pi pi-list"
              size="small"
              class="rounded-[12px]"
              severity="warn"
              variant="outlined"
              label="Logs Upload"
            />
          </div>
        </div>
      </header>

      <main class="flex-1 p-4 lg:p-6">
        <Toast />
        <ConfirmDialog />
        <!-- <SpinnerBorder v-if="triggerSpinner" /> -->
        <slot />
      </main>
    </div>
  </div>

  <!-- BATCH UPLOAD DIALOG -->
  <Dialog
    v-model:visible="uploadDialog"
    modal
    :closable="!isUploading"
    :header="dialogTitle"
    class="w-[95vw] max-w-[600px]"
    position="top"
  >
    <div class="flex flex-col gap-4">
      <!-- FILE SELECTION (hidden during upload) -->
      <div v-if="!isUploading && !isCompleted">
        <label class="block font-bold text-sm mb-2"
          >Pilih File Hasil Scrapping (.csv)</label
        >
        <p class="text-xs text-slate-500 mb-3">
          Bisa pilih banyak file sekaligus. Format nama:
          <code class="bg-slate-100 px-1 rounded text-orange-600"
            >scraped_data_7171.csv</code
          >
        </p>
        <input
          ref="fileInput"
          type="file"
          accept=".csv,.xlsx,.xls"
          multiple
          @change="onFilesSelected"
          class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 file:cursor-pointer file:transition-colors"
        />
      </div>

      <!-- FILE LIST PREVIEW -->
      <div
        v-if="selectedFiles.length > 0"
        class="border border-slate-200 rounded-xl overflow-hidden"
      >
        <div
          class="bg-slate-50 px-4 py-2 flex items-center justify-between border-b border-slate-200"
        >
          <span class="text-xs font-bold text-slate-600 uppercase tracking-wide"
            >{{ selectedFiles.length }} file dipilih</span
          >
          <span
            v-if="isUploading || isCompleted"
            class="text-xs font-semibold"
            :class="{
              'text-blue-600': isUploading,
              'text-emerald-600': isCompleted,
            }"
          >
            {{ completedCount }}/{{ selectedFiles.length }} selesai
          </span>
        </div>

        <!-- OVERALL PROGRESS BAR -->
        <div v-if="isUploading || isCompleted" class="px-4 pt-3 pb-1">
          <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-500 ease-out"
              :class="hasErrors ? 'bg-amber-500' : 'bg-emerald-500'"
              :style="{ width: overallProgress + '%' }"
            ></div>
          </div>
          <p class="text-xs text-slate-500 mt-1 text-right">{{ overallProgress }}%</p>
        </div>

        <div class="max-h-[40vh] overflow-y-auto">
          <div
            v-for="(file, idx) in selectedFiles"
            :key="idx"
            class="flex items-center gap-3 px-4 py-2.5 border-b border-slate-100 last:border-b-0 transition-colors"
            :class="{
              'bg-blue-50/50': file.status === 'uploading',
              'bg-emerald-50/40': file.status === 'success',
              'bg-red-50/40': file.status === 'error',
            }"
          >
            <!-- Status icon -->
            <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
              <!-- Pending -->
              <i
                v-if="file.status === 'pending'"
                class="pi pi-clock text-slate-400 text-sm"
              ></i>
              <!-- Uploading (spinning) -->
              <i
                v-else-if="file.status === 'uploading'"
                class="pi pi-spin pi-spinner text-blue-600 text-sm"
              ></i>
              <!-- Success -->
              <i
                v-else-if="file.status === 'success'"
                class="pi pi-check-circle text-emerald-600 text-sm"
              ></i>
              <!-- Error -->
              <i
                v-else-if="file.status === 'error'"
                class="pi pi-times-circle text-red-500 text-sm"
              ></i>
              <!-- Cancelled -->
              <i
                v-else-if="file.status === 'cancelled'"
                class="pi pi-ban text-slate-400 text-sm"
              ></i>
            </div>

            <!-- File info -->
            <div class="flex-1 min-w-0">
              <p
                class="text-sm font-medium truncate"
                :class="{
                  'text-slate-800': file.status === 'pending',
                  'text-blue-700': file.status === 'uploading',
                  'text-emerald-700': file.status === 'success',
                  'text-red-600': file.status === 'error',
                  'text-slate-400': file.status === 'cancelled',
                }"
              >
                {{ file.name }}
              </p>
              <p v-if="file.status === 'success'" class="text-xs text-emerald-600">
                {{ file.rowsProcessed }} baris diproses dari
                {{ file.rowsTotal }}
              </p>
              <p v-if="file.status === 'error'" class="text-xs text-red-500">
                {{ file.errorMessage }}
              </p>
              <p v-if="file.status === 'uploading'" class="text-xs text-blue-500">
                Sedang memproses...
              </p>
              <p v-if="file.status === 'cancelled'" class="text-xs text-slate-400">
                Dibatalkan
              </p>
            </div>

            <!-- Remove button (only before upload starts) -->
            <button
              v-if="!isUploading && !isCompleted"
              @click="removeFile(idx)"
              class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors"
            >
              <i class="pi pi-times text-xs"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- SUMMARY (after completion) -->
      <div
        v-if="isCompleted"
        class="rounded-xl p-4 border"
        :class="
          hasErrors ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200'
        "
      >
        <div class="flex items-center gap-2 mb-2">
          <i
            class="pi text-lg"
            :class="
              hasErrors
                ? 'pi-exclamation-triangle text-amber-600'
                : 'pi-check-circle text-emerald-600'
            "
          ></i>
          <span
            class="font-bold text-sm"
            :class="hasErrors ? 'text-amber-800' : 'text-emerald-800'"
          >
            Upload Selesai
          </span>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center">
          <div class="bg-white/70 rounded-lg p-2">
            <p class="text-lg font-bold text-emerald-600">{{ successCount }}</p>
            <p class="text-xs text-slate-500">Berhasil</p>
          </div>
          <div class="bg-white/70 rounded-lg p-2">
            <p class="text-lg font-bold text-red-500">{{ errorCount }}</p>
            <p class="text-xs text-slate-500">Gagal</p>
          </div>
          <div class="bg-white/70 rounded-lg p-2">
            <p class="text-lg font-bold text-slate-400">{{ cancelledCount }}</p>
            <p class="text-xs text-slate-500">Dibatalkan</p>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <!-- Before upload -->
      <div v-if="!isUploading && !isCompleted" class="flex justify-end gap-2">
        <Button
          label="Batal"
          @click="closeDialog"
          size="small"
          severity="secondary"
          outlined
        />
        <Button
          @click="startBatchUpload"
          label="Upload Semua"
          size="small"
          severity="success"
          icon="pi pi-upload"
          :disabled="selectedFiles.length === 0"
        />
      </div>

      <!-- During upload -->
      <div v-if="isUploading" class="flex justify-end gap-2">
        <Button
          label="Batalkan Sisa"
          @click="cancelUpload"
          size="small"
          severity="danger"
          icon="pi pi-stop"
          outlined
        />
      </div>

      <!-- After completion -->
      <div v-if="isCompleted" class="flex justify-end gap-2">
        <Button
          label="Tutup"
          @click="closeAndRefresh"
          size="small"
          severity="info"
          icon="pi pi-check"
        />
      </div>
    </template>
  </Dialog>

  <Dialog
    v-model:visible="logsDialog"
    modal
    class="w-[50vw]"
    header="Log Upload"
    position="top"
  >
    <DataTable
      :value="logPaginatedItem.data"
      class="w-full text-sm"
      lazy
      paginator
      showGridlines
      stripedRows
      :rows="logPaginatedItem.per_page"
      :first="(logPaginatedItem.current_page - 1) * logPaginatedItem.per_page"
      :total-records="logPaginatedItem.total"
      :rows-per-page-options="[10, 20, 50, 100]"
      paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
      current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
    >
      <Column field="formatted_time" header="Upload Tanggal" />
      <Column field="kabkot.label" header="Data Kabupaten/Kota" />
      <Column field="pegawai.name" header="Nama Pegawai" />
    </DataTable>
  </Dialog>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { ConfirmDialog, Toast, useToast } from "primevue";
import { computed, nextTick, ref, watch } from "vue";
import * as XLSX from "xlsx";
import { triggerSpinner } from "../ManManagement/Composables/axiosSetup";
import SpinnerBorder from "@/Components/ManManagement/SpinnerBorder.vue";

const page = usePage();
const toast = useToast();
watch(
  () => page.props.flash,
  async (flash) => {
    await nextTick();
    if (flash?.success) {
      toast.add({
        severity: "success",
        summary: "Berhasil",
        detail: flash.success,
        life: 3000,
      });
    }
    if (flash?.error) {
      toast.add({
        severity: "error",
        summary: "Gagal",
        detail: flash.error,
        life: 3000,
      });
    }
  },
  { deep: true, immediate: true }
);

// ─── Upload State ───
const uploadDialog = ref(false);
const fileInput = ref(null);
const selectedFiles = ref([]);
const isUploading = ref(false);
const isCompleted = ref(false);
const cancelRequested = ref(false);

const dialogTitle = computed(() => {
  if (isCompleted.value) return "Hasil Upload";
  if (isUploading.value)
    return `Mengupload... (${completedCount.value}/${selectedFiles.value.length})`;
  return "Upload Data Terbaru";
});

// ─── Computed Stats ───
const completedCount = computed(
  () =>
    selectedFiles.value.filter((f) => f.status === "success" || f.status === "error")
      .length
);
const successCount = computed(
  () => selectedFiles.value.filter((f) => f.status === "success").length
);
const errorCount = computed(
  () => selectedFiles.value.filter((f) => f.status === "error").length
);
const cancelledCount = computed(
  () => selectedFiles.value.filter((f) => f.status === "cancelled").length
);
const hasErrors = computed(() => errorCount.value > 0 || cancelledCount.value > 0);
const overallProgress = computed(() => {
  if (selectedFiles.value.length === 0) return 0;
  const done = selectedFiles.value.filter(
    (f) => f.status !== "pending" && f.status !== "uploading"
  ).length;
  return Math.round((done / selectedFiles.value.length) * 100);
});

// ─── Dialog Lifecycle ───
const openUploadDialog = () => {
  selectedFiles.value = [];
  isUploading.value = false;
  isCompleted.value = false;
  cancelRequested.value = false;
  uploadDialog.value = true;
};

const closeDialog = () => {
  uploadDialog.value = false;
  selectedFiles.value = [];
};

const closeAndRefresh = () => {
  uploadDialog.value = false;
  selectedFiles.value = [];
  // Full page reload to refresh dashboard data
  window.location.reload();
};

// ─── File Selection ───
const onFilesSelected = (e) => {
  const files = Array.from(e.target.files || []);
  selectedFiles.value = files.map((file) => ({
    raw: file,
    name: file.name,
    status: "pending", // pending | uploading | success | error | cancelled
    parsedData: null,
    rowsProcessed: 0,
    rowsTotal: 0,
    errorMessage: "",
  }));
};

const removeFile = (idx) => {
  selectedFiles.value.splice(idx, 1);
};

// ─── Parse a single file using XLSX ───
const parseFile = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const dataBuffer = e.target.result;
        const wb = XLSX.read(dataBuffer, { type: "array" });
        const wsname = wb.SheetNames[0];
        const ws = wb.Sheets[wsname];
        const data = XLSX.utils.sheet_to_json(ws, { header: 1 });
        resolve(data);
      } catch (err) {
        reject(err);
      }
    };
    reader.onerror = () => reject(new Error("Gagal membaca file"));
    reader.readAsArrayBuffer(file);
  });
};

// ─── Sequential Batch Upload ───
const startBatchUpload = async () => {
  if (selectedFiles.value.length === 0) return;

  isUploading.value = true;
  isCompleted.value = false;
  cancelRequested.value = false;

  // Get CSRF token once
  let csrfToken;
  try {
    const { data: token } = await axios.get(route("api.token.csrf"));
    csrfToken = token;
  } catch (err) {
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "Gagal mendapatkan CSRF token",
      life: 3000,
    });
    isUploading.value = false;
    return;
  }

  // Process files one by one
  for (let i = 0; i < selectedFiles.value.length; i++) {
    // Check if cancel was requested
    if (cancelRequested.value) {
      // Mark remaining files as cancelled
      for (let j = i; j < selectedFiles.value.length; j++) {
        selectedFiles.value[j].status = "cancelled";
      }
      break;
    }

    const fileEntry = selectedFiles.value[i];
    fileEntry.status = "uploading";

    try {
      // Parse the CSV file
      const parsedData = await parseFile(fileEntry.raw);
      fileEntry.parsedData = parsedData;

      // Send to backend
      const response = await axios.post(route("se2026.upload-data-batch"), {
        _token: csrfToken,
        file: parsedData,
        file_name: fileEntry.name,
      });

      fileEntry.status = "success";
      fileEntry.rowsProcessed = response.data.rows_processed;
      fileEntry.rowsTotal = response.data.rows_total;
    } catch (err) {
      fileEntry.status = "error";
      fileEntry.errorMessage =
        err.response?.data?.message || err.message || "Terjadi kesalahan tidak diketahui";
    }

    // Free memory
    fileEntry.parsedData = null;
  }

  isUploading.value = false;
  isCompleted.value = true;

  // Show summary toast
  if (successCount.value > 0) {
    toast.add({
      severity: hasErrors.value ? "warn" : "success",
      summary: "Upload Selesai",
      detail: `${successCount.value} berhasil, ${errorCount.value} gagal, ${cancelledCount.value} dibatalkan`,
      life: 5000,
    });
  }
};

const cancelUpload = () => {
  cancelRequested.value = true;
};

const logsDialog = ref(false);
const logPaginatedItem = ref({});
const openLogsDialog = async () => {
  logsDialog.value = true;
  try {
    const { data } = await axios.get(route("se2026.fetch-log"));
    logPaginatedItem.value = data;
  } catch (error) {
    console.error(error);
  }
};
</script>
