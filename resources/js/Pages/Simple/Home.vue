<template>
  <Head title="Dashboard" />

  <SimpleLayout title="Dashboard">
    <div class="flex flex-wrap items-end gap-4 mb-4">
      <div class="flex flex-col gap-2">
        <label class="font-bold">Tahun</label>
        <Select
          v-model="search.tahun"
          placeholder="Pilih tahun"
          :options="yearDrop"
          optionLabel="label"
          optionValue="value"
          class="w-40"
        />
      </div>
      <div class="flex flex-col gap-2">
        <label class="font-bold">Bulan</label>
        <Select
          v-model="search.bulan"
          placeholder="Pilih bulan"
          :options="monthDrop"
          optionLabel="label"
          optionValue="value"
          class="w-75"
        />
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
      <div
        class="bg-white rounded-xl shadow-sm p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-yellow-50 text-yellow-600 mr-4">
          <i class="pi pi-hourglass text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">
            Sedang diperiksa Katim
          </h3>
          <p class="text-2xl font-bold text-gray-800">{{ result.pending }}</p>
        </div>
      </div>

      <div
        class="bg-white rounded-xl shadow-sm p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-red-50 text-red-600 mr-4">
          <i class="pi pi-times-circle text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">Ditolak Katim</h3>
          <p class="text-2xl font-bold text-gray-800">
            {{ result.tolak_katim }}
          </p>
        </div>
      </div>

      <div
        class="bg-white rounded-xl shadow-sm p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-indigo-50 text-indigo-600 mr-4">
          <i class="pi pi-send text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">
            Sedang diperiksa Kabag
          </h3>
          <p class="text-2xl font-bold text-gray-800">
            {{ result.setuju_katim }}
          </p>
        </div>
      </div>

      <div
        class="bg-white rounded-xl shadow-sm p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-red-50 text-red-600 mr-4">
          <i class="pi pi-ban text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">Ditolak Kabag</h3>
          <p class="text-2xl font-bold text-gray-800">
            {{ result.tolak_kabag }}
          </p>
        </div>
      </div>
      <div
        class="bg-white rounded-xl shadow-sm p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-green-50 text-green-600 mr-4">
          <i class="pi pi-check-circle text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">Disetujui Kabag</h3>
          <p class="text-2xl font-bold text-gray-800">
            {{ result.setuju_kabag }}
          </p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">5 Tim dengan Lembur Terbanyak</h2>
        <div class="flex-grow h-64">
          <Chart v-if="Object.keys(timsData || {}).length > 0" type="bar" :data="timChartData" :options="chartOptions" class="h-full w-full" />
          <div v-else class="h-full flex items-center justify-center text-gray-400">Belum ada data</div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">5 Pegawai dengan Lembur Terbanyak</h2>
        <div class="flex-grow h-64">
          <Chart v-if="Object.keys(pegawaisData || {}).length > 0" type="bar" :data="pegawaiChartData" :options="chartOptions" class="h-full w-full" />
          <div v-else class="h-full flex items-center justify-center text-gray-400">Belum ada data</div>
        </div>
      </div>
    </div>
  </SimpleLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import SimpleLayout from "../../Layouts/Simple/SimpleLayout.vue";
import { ref, watch, computed } from "vue";
import Chart from "primevue/chart";

const props = defineProps({
  result: Object,
  pegawais: Object,
  tims: Object,
});

const result = ref(props.result);
const pegawaisData = ref(props.pegawais);
const timsData = ref(props.tims);

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        stepSize: 1,
      },
    },
  },
});

const timChartData = computed(() => {
  const dataObj = timsData.value || {};
  return {
    labels: Object.keys(dataObj),
    datasets: [
      {
        label: "Jumlah Lembur",
        data: Object.values(dataObj),
        backgroundColor: "#3b82f6",
        borderRadius: 4,
      },
    ],
  };
});

const pegawaiChartData = computed(() => {
  const dataObj = pegawaisData.value || {};
  return {
    labels: Object.keys(dataObj),
    datasets: [
      {
        label: "Jumlah Lembur",
        data: Object.values(dataObj),
        backgroundColor: "#10b981",
        borderRadius: 4,
      },
    ],
  };
});

const currentYear = new Date().getFullYear();
const yearDrop = ref(
  Array.from({ length: 10 }, (_, i) => ({
    label: (currentYear - i).toString(),
    value: currentYear - i,
  }))
);
const search = ref({
  tahun: currentYear,
  bulan: 0,
});
const monthDrop = ref([
  { label: "Semua Bulan", value: 0 },
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

const fetchData = async () => {
  try {
    const { data } = await axios.get(route("simple.index"), {
      params: {
        filters: search.value,
      },
    });
    result.value = data.result;
    pegawaisData.value = data.pegawais;
    timsData.value = data.tims;
  } catch (error) {
    console.error(error);
  }
};

watch(
  search,
  () => {
    fetchData();
  },
  { deep: true }
);
</script>
