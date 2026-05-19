<template>
  <Head title="Laporan Tim" />
  <SimpleLayout :is-open="isSidebarOpen">
    <div class="text-xl font-bold mb-4">Pengajuan Laporan Lembur Tim</div>
    <div class="flex flex-wrap items-end gap-2">
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
          class="w-48"
        />
      </div>
      <div class="space-x-2">
        <Button icon="pi pi-search" class="mb-0" />
      </div>
    </div>
    <div class="card mt-4">
      <DataTable
        :value="itemTable"
        class="w-full text-sm"
        lazy
        showGridlines
        :removable-sort="true"
      >
        <template #empty>
          <div class="text-center">
            Belum ada data lembur bulan ini yang sudah disetujui kabag.
          </div>
        </template>
        <Column header="No." style="width: 3rem">
          <template #body="slotProps">{{ slotProps.index + 1 }}</template>
        </Column>
        <Column header="Tim" field="tim" />
        <Column header="Maksud Lembur" field="maksud_lembur" />
        <Column header="Jumlah Lembur yang Disetujui Kabag" field="jumlah">
          <template #body="{ data }">
            <Badge :value="`${data.jumlah} pegawai`" severity="info"></Badge>
          </template>
        </Column>
        <Column header="Nomor SPKL" field="no_spkl">
          <template #body="{ data }">
            <Badge v-if="data.no_spkl" :value="data.no_spkl" severity="info" />
            <Badge
              v-else
              value="Belum diajukan"
              severity="secondary"
            /> </template
        ></Column>
        <Column header="Link" field="link_dokumentasi">
          <template #body="{ data }">
            <Button
              v-if="data.link_dokumentasi"
              icon="pi pi-external-link"
              variant="outlined"
              rounded
              class="mr-2"
              :severity="'info'"
              @click="toDocumentation(data.link_dokumentasi)"
            ></Button>
            <div v-else>-</div>
          </template>
        </Column>
        <Column header="Cetak ">
          <template #body="{ data }">
            <Button
              @click="prepareLaporan(data)"
              icon="pi pi-file-word"
              variant="outlined"
              rounded
              class="mr-2"
              :severity="'warn'"
            ></Button>
          </template>
        </Column>
        <Column header="Kirim">
          <template #body="">
            <Button
              icon="pi pi-send"
              variant="outlined"
              rounded
              class="mr-2"
              :severity="'success'"
            ></Button>
          </template>
        </Column>
      </DataTable>
    </div>
    <Dialog
      v-model:visible="printDialog"
      modal
      header="Cetak Laporan"
      position="top"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2">Tahun</label>
          <Select
            v-model="search.tahun"
            :options="yearDrop"
            optionLabel="label"
            optionValue="value"
            fluid
            disabled
          />
        </div>
        <div>
          <label class="block font-bold mb-2">Bulan</label>
          <Select
            v-model="search.bulan"
            :options="monthDrop"
            optionLabel="label"
            optionValue="value"
            fluid
            disabled
          />
        </div>
        <div></div>
      </div>
    </Dialog>
  </SimpleLayout>
</template>
<script setup>
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";

const props = defineProps({
  lembur: Object,
});
const printDialog = ref(false);
const itemTable = ref(props.lembur);
watch(
  () => props.lembur,
  (item) => {
    itemTable.value = item;
  }
);
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

const search = ref({
  tahun: currentYear,
  bulan: currentMonth,
});
const toDocumentation = (link) => {
  const url = link.startsWith("http") ? link : `https://${link}`;
  window.open(url, "_blank", "noopener,noreferrer");
};
const prepareLaporan = async (lembur) => {
  try {
    printDialog.value = true;
    const { data } = await axios.get(
      route("simple.fetch-lembur", { lembur_id: lembur.lembur_id })
    );
    console.log(data);
  } catch (error) {
    console.error(error);
  }
};
</script>