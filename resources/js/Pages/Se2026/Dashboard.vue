<template>
  <Head title="Dashboard" />
  <SpinnerBorder v-if="thisTriggerSpinner" />
  <SeLayout @refresh="refreshData">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
      <!-- TOTAL TARGET PRELIST -->
      <div class="stat-card">
        <span class="stat-label">Total Assignment Petugas</span>
        <span class="stat-value text-slate-800">{{ formatNumber(total) }}</span>
        <!-- <div class="stat-footer text-sm !justify-start">
          Total dihitung dari assignment petugas
        </div> -->
      </div>

      <!-- TOTAL REALISASI -->
      <div class="stat-card">
        <span class="stat-label">Realisasi Pendataan</span>
        <span class="stat-value text-emerald-600">{{
          formatNumber(realisasi)
        }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-emerald-500"
            :style="{ width: pctRealisasi + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-emerald-600">{{ pctRealisasi }}%</span>
        </div>
        <span class="stat-note">*Selain status OPEN dan DRAFT</span>
      </div>

      <!-- REALISASI VIA FASIH SM -->
      <!-- <div class="stat-card">
        <span class="stat-label">Realisasi via FASIH SM</span>
        <span class="stat-value text-slate-800">{{
          formatNumber(completed)
        }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-emerald-500"
            :style="{ width: pctCompleted + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-emerald-600">{{ pctCompleted }}%</span>
        </div>
        <span class="stat-note"
          >* Selain Open & Draft (Submit + Approve + Reject)</span
        >
      </div> -->

      <!-- STATUS TERBUKA (OPEN) -->
      <div class="stat-card">
        <span class="stat-label">Status OPEN</span>
        <span class="stat-value text-amber-500">{{ formatNumber(open) }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-yellow-500"
            :style="{ width: pctOpen + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-amber-500">{{ pctOpen }}%</span>
        </div>
      </div>

      <!-- SUBMITTED BY PENCACAH -->
      <div class="stat-card">
        <span class="stat-label">Submitted by Pencacah</span>
        <span class="stat-value text-green-600">{{
          formatNumber(submitted_p)
        }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-green-500"
            :style="{ width: pctSubmittedP + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-green-600">{{ pctSubmittedP }}%</span>
        </div>
      </div>

      <!-- APPROVED BY PENGAWAS -->
      <div class="stat-card">
        <span class="stat-label">Approved by Pengawas</span>
        <span class="stat-value text-green-600">{{
          formatNumber(approved)
        }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-green-500"
            :style="{ width: pctApproved + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-green-600">{{ pctApproved }}%</span>
        </div>
      </div>

      <!-- STATUS DRAFT -->
      <div class="stat-card">
        <span class="stat-label">Status Draft</span>
        <span class="stat-value text-blue-600">{{ formatNumber(draft) }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-blue-500"
            :style="{ width: pctDraft + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-blue-600">{{ pctDraft }}%</span>
        </div>
      </div>

      <!-- REJECTED BY PENGAWAS -->
      <div class="stat-card">
        <span class="stat-label">Rejected by Pengawas</span>
        <span class="stat-value text-red-600">{{
          formatNumber(rejected)
        }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-red-500"
            :style="{ width: pctRejected + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-red-600">{{ pctRejected }}%</span>
        </div>
      </div>
    </div>
    <div class="rank-container mb-4">
      <div class="rank-header justify-between">
        <div class="flex items-center gap-2">
          <i class="pi pi-history text-lg text-orange-500"></i>
          <div>
            <h2 class="text-base font-bold text-slate-800">Update Terakhir</h2>
            <p class="text-xs text-slate-500">
              User yang melakukan 3 update terakhir:
            </p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Badge
            v-for="(item, index) in lastThreeUpdate"
            :key="index"
            :value="
              item.pegawai.username +
              ', (Kode ' +
              item.kode +
              ', ' +
              item.formatted_time +
              ')'
            "
            :severity="getSeverity(item.level)"
          />
        </div>
      </div>
    </div>

    <!-- TAB: WILAYAH -->
    <div v-if="activeTab === 'wilayah'" class="rank-container">
      <div class="rank-header justify-between">
        <div class="flex items-center gap-2">
          <i class="pi pi-chart-line text-lg text-orange-500"></i>
          <div>
            <h2 class="text-base font-bold text-slate-800">
              Progres Realisasi
            </h2>
            <p class="text-xs text-slate-500">
              Distribusi persentase realisasi (Selain status Open dan Draft)
            </p>
          </div>
        </div>
        <div class="space-x-2 flex items-center">
          <Button
            title="Download"
            @click="wilayahDialog = true"
            rounded
            icon="pi pi-download"
            size="small"
            severity="success"
          />
          <Select
            :pt="{
              label: { class: 'text-sm px-2 py-1' },
              option: { class: 'text-sm px-2 py-1' },
            }"
            v-model="sortMode"
            :options="sortOptions"
            optionLabel="label"
            optionValue="value"
            placeholder="Urutkan"
            size="small"
            class="text-xs"
          />
          <Badge
            @click="goToRegion('71')"
            class="cursor-pointer"
            :severity="getSeverity('kabkot')"
            value="Level Kab/Kota"
          />
          <Badge
            @click="goToRegion(currentCode, 'kec')"
            v-if="getVisibility('kec')"
            class="cursor-pointer"
            :severity="getSeverity('kec')"
            value="Level Kecamatan"
          />
          <Badge
            @click="goToRegion(currentCode, 'desa')"
            v-if="getVisibility('desa')"
            class="cursor-pointer"
            :severity="getSeverity('desa')"
            value="Level Desa"
          />
          <Badge
            v-if="getVisibility('sls')"
            class="cursor-pointer"
            :severity="getSeverity('sls')"
            value="Level SLS"
          />
        </div>
      </div>
      <div class="flex gap-x-6 mt-4 overflow-x-auto pb-2">
        <div
          v-for="(col, colIdx) in columns"
          :key="colIdx"
          class="flex-shrink-0"
          style="min-width: 320px; flex: 1"
        >
          <div
            v-for="(item, idx) in col"
            :key="item.code"
            class="rank-item cursor-pointer"
            @click="goToRegion(item.code)"
          >
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <span class="rank-number" :class="rankColor(colIdx * 15 + idx)">{{
                colIdx * 15 + idx + 1
              }}</span>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <span class="rank-name">{{ item.name }}</span>
                  <div class="flex items-center gap-2">
                    <span class="rank-stats"
                      >{{ formatNumber(kabkotRealisasi(item)) }} /
                      {{ formatNumber(kabkotTotal(item)) }}</span
                    >
                    <span
                      class="rank-pct"
                      :class="rankPctColor(kabkotPct(item))"
                      >{{ kabkotPct(item) }}%</span
                    >
                  </div>
                </div>
                <div class="rank-bar-wrapper">
                  <div
                    class="rank-bar"
                    :class="rankBarColor(kabkotPct(item))"
                    :style="{ width: kabkotPct(item) + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Dialog
      v-model:visible="wilayahDialog"
      modal
      header="Download Wilayah"
      class="w-[30vw]"
      position="top"
    >
      <div class="flex flex-col gap-4">
        <div class="space-y-2">
          <label class="font-bold block">Pilih Kabupaten/Kota:</label>
          <Select
            :options="kabkot"
            v-model="selectedWilayahForDownload"
            placeholder="Pilih Kabupaten/Kota"
            optionLabel="label"
            optionValue="code"
            class="w-full"
          />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-2">
          <Button
            label="Batal"
            @click="wilayahDialog = false"
            size="small"
            severity="secondary"
            outlined
          />
          <Button
            @click="downloadWilayah"
            label="Download"
            size="small"
            severity="success"
            icon="pi pi-download"
          />
        </div>
      </template>
    </Dialog>
  </SeLayout>
</template>

<script setup>
import SpinnerBorder from "@/Components/ManManagement/SpinnerBorder.vue";
import SeLayout from "@/Layouts/Se2026/SeLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref } from "vue";

const page = usePage();
const uploadDialog = ref(false);

const props = defineProps({
  open: Number,
  draft: Number,
  submitted_p: Number,
  submitted_r: Number,
  approved: Number,
  rejected: Number,
  revoked: Number,
  completed: Number,
  total: Number,
  data_progress: Array,
  current_level: String,
  lastThreeUpdate: Object,
  kabkot: Array,
});

const thisTriggerSpinner = ref(false);
const refreshData = () => {
  router.reload({
    only: [
      "open",
      "draft",
      "submitted_p",
      "submitted_r",
      "approved",
      "rejected",
      "revoked",
      "completed",
      "total",
      "data_progress",
      "current_level",
      "lastThreeUpdate",
      "kabkot",
    ],
    preserveScroll: true,
    onStart: () => {
      thisTriggerSpinner.value = true;
    },
    onFinish: () => {
      thisTriggerSpinner.value = false;
      progressData.value = props.data_progress;
    },
  });
};

const selectedKabkot = ref(null);
const selectedKec = ref(null);
const selectedDesa = ref(null);
const selectedSls = ref(null);
const kec = ref([]);
const desa = ref([]);
const sls = ref([]);
const fetchKec = async () => {
  const { data } = await axios.get(
    route("se2026.fetch-kec", { kabkot: selectedKabkot.value })
  );
  kec.value = data;
};

const fetchDesa = async () => {
  const { data } = await axios.get(
    route("se2026.fetch-desa", { kec: selectedKec.value })
  );
  desa.value = data;
};

const fetchSls = async () => {
  const { data } = await axios.get(
    route("se2026.fetch-sls", { desa: selectedDesa.value })
  );
  sls.value = data;
};

const activeTab = ref("wilayah"); // wilayah | ppl | pml

const switchTab = async (tab) => {
  activeTab.value = tab;
  if (tab === "ppl" || tab === "pml") {
    await fetchDataPetugas(tab);
  }
  // PML: implement later
};

const progressData = ref(props.data_progress);
const currentLevel = ref(props.current_level);

const sortMode = ref("code");
const sortOptions = [
  { label: "Kode Wilayah", value: "code" },
  { label: "Realisasi Terbesar", value: "realisasi" },
  { label: "Target Terbesar", value: "target" },
];

const getTotal = (item) => {
  return (
    (item.open || 0) +
    (item.draft || 0) +
    (item.submitted_p || 0) +
    (item.submitted_r || 0) +
    (item.approved || 0) +
    (item.rejected || 0) +
    (item.revoked || 0) +
    (item.completed || 0)
  );
};

const getRealisasi = (item) => {
  return (
    (item.submitted_p || 0) +
    (item.submitted_r || 0) +
    (item.approved || 0) +
    (item.rejected || 0) +
    (item.revoked || 0) +
    (item.completed || 0)
  );
};

// Chunk data into groups of 15, sorted by selected mode
const columns = computed(() => {
  const data = [...(progressData.value || [])];

  if (sortMode.value === "code") {
    data.sort((a, b) => String(a.code).localeCompare(String(b.code)));
  } else if (sortMode.value === "realisasi") {
    data.sort((a, b) => {
      const pctA = getTotal(a) ? getRealisasi(a) / getTotal(a) : 0;
      const pctB = getTotal(b) ? getRealisasi(b) / getTotal(b) : 0;
      return pctB - pctA;
    });
  } else if (sortMode.value === "target") {
    data.sort((a, b) => getTotal(b) - getTotal(a));
  }

  const chunks = [];
  for (let i = 0; i < data.length; i += 15) {
    chunks.push(data.slice(i, i + 15));
  }
  return chunks;
});

const kabkotTotal = (item) => {
  return (
    (item.open || 0) +
    (item.draft || 0) +
    (item.submitted_p || 0) +
    (item.submitted_r || 0) +
    (item.approved || 0) +
    (item.rejected || 0) +
    (item.revoked || 0) +
    (item.completed || 0)
  );
};

const kabkotRealisasi = (item) => {
  return (
    (item.submitted_p || 0) +
    (item.submitted_r || 0) +
    (item.approved || 0) +
    (item.rejected || 0) +
    (item.revoked || 0) +
    (item.completed || 0)
  );
};

const kabkotPct = (item) => {
  const tot = kabkotTotal(item);
  if (tot === 0) return "0.00";
  return ((kabkotRealisasi(item) / tot) * 100).toFixed(2);
};

const rankColor = (idx) => {
  return "rank-default";
};

const rankPctColor = (idx) => {
  if (idx < 20) return "text-red-400";
  else if (idx < 50) return "text-yellow-400";
  else if (idx < 75) return "text-orange-400";
  else return "text-emerald-600";
};

const rankBarColor = (idx) => {
  if (idx < 20) return "bg-red-400";
  else if (idx < 50) return "bg-yellow-400";
  else if (idx < 75) return "bg-orange-400";
  else return "bg-emerald-600";
};

const getSeverity = (level) => {
  if (level == currentLevel.value) return "info";
  else return "secondary";
};
const getVisibility = (level) => {
  if (
    level == "kabkot" &&
    (currentLevel.value == "kabkot" ||
      currentLevel.value == "kec" ||
      currentLevel.value == "desa" ||
      currentLevel.value == "sls")
  )
    return true;
  if (
    level == "kec" &&
    (currentLevel.value == "kec" ||
      currentLevel.value == "desa" ||
      currentLevel.value == "sls")
  )
    return true;
  if (
    level == "desa" &&
    (currentLevel.value == "desa" || currentLevel.value == "sls")
  )
    return true;
  if (level == "sls" && currentLevel.value == "sls") return true;
  return false;
};

// Realisasi = semua selain Open
const realisasi = computed(
  () =>
    (props.submitted_p || 0) +
    (props.submitted_r || 0) +
    (props.approved || 0) +
    (props.rejected || 0) +
    (props.revoked || 0) +
    (props.completed || 0)
);

const pct = (val) => {
  if (!props.total || props.total === 0) return "0.00";
  return ((val / props.total) * 100).toFixed(2);
};

const pctRealisasi = computed(() => pct(realisasi.value));
const pctCompleted = computed(() => pct(props.completed || 0));
const pctOpen = computed(() => pct(props.open || 0));
const pctSubmittedP = computed(() => pct(props.submitted_p || 0));
const pctApproved = computed(() => pct(props.approved || 0));
const pctDraft = computed(() => pct(props.draft || 0));
const pctRejected = computed(() => pct(props.rejected || 0));

const formatNumber = (n) => {
  if (n == null) return "0";
  return n.toLocaleString("id-ID");
};

const currentCode = ref("71");
const goToRegion = async (code, type = null) => {
  thisTriggerSpinner.value = true;
  currentCode.value = code;
  if (type == "kec") currentCode.value = code.substring(0, 4);
  if (type == "desa") currentCode.value = code.substring(0, 7);
  if (code.length > 10) return;
  try {
    const { data } = await axios.get(route("se2026.data.index"), {
      params: {
        code: currentCode.value,
      },
    });
    progressData.value = data.data_progress;
    currentLevel.value = data.current_level;
  } catch (error) {
    console.error(error);
  } finally {
    thisTriggerSpinner.value = false;
  }
};
const wilayahDialog = ref(false);
const selectedWilayahForDownload = ref(null);
const downloadWilayah = () => {
  const url =
    route(`se2026.download-wilayah`) +
    "?" +
    new URLSearchParams({
      kabkot: selectedWilayahForDownload.value || "",
    });
  window.location.href = url;
  wilayahDialog.value = false;
  selectedWilayahForDownload.value = null;
};
</script>

<style scoped>
.stat-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.25rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(148, 163, 184, 0.15),
    transparent
  );
}

.stat-card:hover {
  border-color: #cbd5e1;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.stat-label {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
}

.stat-value {
  font-size: 2.25rem;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.02em;
}

.stat-bar-wrapper {
  width: 100%;
  height: 6px;
  background: #e2e8f0;
  border-radius: 999px;
  overflow: hidden;
}

.stat-bar {
  height: 100%;
  border-radius: 999px;
  transition: width 1s cubic-bezier(0.22, 1, 0.36, 1);
  min-width: 4px;
}

.stat-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.stat-pct {
  font-size: 0.85rem;
  font-weight: 700;
}

.stat-note {
  font-size: 0.6rem;
  color: #64748b;
  line-height: 1.3;
}

/* Rank Section */
.rank-container {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.rank-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.rank-badge {
  font-size: 0.65rem;
  font-weight: 600;
  color: #64748b;
  background: #f1f5f9;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  border: 1px solid #e2e8f0;
}

.rank-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 0.5rem;
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.2s ease;
}

.rank-item:hover {
  background: #f8fafc;
  border-radius: 8px;
}

.rank-number {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  flex-shrink: 0;
}

.rank-top {
  background: #f97316;
  color: #ffffff;
}

.rank-default {
  background: #f1f5f9;
  color: #64748b;
}

.rank-name {
  font-size: 0.8rem;
  font-weight: 700;
  color: #1e293b;
  text-transform: uppercase;
}

.rank-stats {
  font-size: 0.7rem;
  color: #94a3b8;
  font-weight: 500;
  white-space: nowrap;
}

.rank-pct {
  font-size: 0.75rem;
  font-weight: 700;
  white-space: nowrap;
}

.rank-bar-wrapper {
  width: 100%;
  height: 4px;
  background: #e2e8f0;
  border-radius: 999px;
  overflow: hidden;
}

.rank-bar {
  height: 100%;
  border-radius: 999px;
  transition: width 1s cubic-bezier(0.22, 1, 0.36, 1);
  min-width: 2px;
}

/* Tab Navigation */
.tab-nav {
  display: flex;
  gap: 0.25rem;
  background: #f1f5f9;
  border-radius: 12px;
  padding: 0.25rem;
}

.tab-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.25rem;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  background: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.tab-btn:hover {
  color: #334155;
  background: rgba(255, 255, 255, 0.5);
}

.tab-active {
  color: #ea580c !important;
  background: #ffffff !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
</style>
