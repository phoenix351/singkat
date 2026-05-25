<template>
  <Head title="Laporan Tim" />
  <SimpleLayout>
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
        <Button icon="pi pi-search" @click="fetchData" class="mb-0" />
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
            <Badge
              size="small"
              :value="`${data.jumlah} pegawai`"
              severity="info"
            ></Badge>
          </template>
        </Column>
        <Column class="whitespace-nowrap" header="Nomor SPKL" field="no_spkl">
          <template #body="{ data }">
            <Badge
              v-if="data.no_spkl"
              :value="data.no_spkl"
              severity="info"
              size="small"
            />
            <Badge
              v-else
              size="small"
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
          <template #body="{ data }">
            <Button
              v-if="!data.upload_status"
              @click="openUploadDialog(data.lembur_id)"
              icon="pi pi-send"
              variant="outlined"
              rounded
              class="mr-2"
              :severity="'success'"
            ></Button>
            <Badge
              v-else
              @click="openUploadDialog(data.lembur_id)"
              class="cursor-pointer"
              :value="'Terkirim, ' + formatDateTime(data.upload_status)"
              severity="success"
              v-tooltip.right="'Klik untuk update file'"
            ></Badge>
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
        <div>
          <label class="block font-bold mb-2">Tim Kerja</label>
          <InputText v-model="lemburToPrint.tim" fluid disabled />
        </div>
        <div>
          <label class="block font-bold mb-2">Tanggal Pengajuan</label>
          <DatePicker
            v-model="form.tanggal_pengajuan"
            fluid
            showIcon
            locale="id-ID"
            dateFormat="dd MM yy"
            placeholder="Isi tanggal pengajuan"
          />
        </div>
        <div>
          <label class="block font-bold mb-2"
            >Daftar Pegawai yang Lembur :</label
          >
          <ul class="list-disc pl-5 mt-2">
            <li v-for="(item, index) in currentListPegawai" :key="index">
              {{ item.pegawai.name }}
            </li>
          </ul>
        </div>
      </div>
      <template #footer>
        <Button
          label="Cetak"
          @click="toPrint"
          icon="pi pi-file-word"
          size="small"
          severity="success"
          autofocus
        />
      </template>
    </Dialog>
    <Dialog
      v-model:visible="uploadDialog"
      modal
      header="Upload Laporan"
      position="top"
      class="w-[30vw]"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2"
            >Pilih Laporan yang Sudah di-Ttd</label
          >
          <FileUpload
            ref="fileupload"
            name="file"
            accept=".docx,.pdf"
            mode="basic"
            :max-file-size="2000000"
            @select="onSelectFile"
            chooseLabel="Pilih File"
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Kirim"
          @click="toUpload"
          icon="pi pi-send"
          size="small"
          severity="info"
          autofocus
        />
      </template>
    </Dialog>
  </SimpleLayout>
</template>
<script setup>
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { onMounted, ref, watch } from "vue";
import { useToast } from "primevue";

const toast = useToast();
const props = defineProps({
  lembur: Object,
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
    .replace(/\./g, ":");
};
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
const fetchData = async () => {
  try {
    const { data } = await axios.get(route("simple.laporan-lembur"), {
      params: {
        filters: search.value,
      },
    });
    itemTable.value = data;
  } catch (error) {
    console.error(error);
  }
};
const currentListPegawai = ref([]);
const lemburToPrint = ref({});
const prepareLaporan = async (lembur) => {
  const { data: myTim } = await axios.get(
    route("man-management.fetch-my-team")
  );
  const isMyTim = myTim.includes(lembur.tim_id);
  if (!isMyTim) {
    toast.add({
      severity: "error",
      summary: "Gagal",
      detail: "Ini bukan tim kamu",
      life: 3000,
    });
    return;
  }
  try {
    printDialog.value = true;
    lemburToPrint.value = { ...lembur };
    const { data } = await axios.get(
      route("simple.fetch-lembur", { lembur_id: lembur.lembur_id })
    );
    currentListPegawai.value = data?.pegawai;
  } catch (error) {
    console.error(error);
  }
};
const form = useForm({
  bulan: null,
  tahun: null,
  lembur_id: null,
  tanggal_pengajuan: new Date(),
});
const toPrint = async () => {
  form.lembur_id = lemburToPrint.value.lembur_id;
  form.bulan = search.value.bulan;
  form.tahun = search.value.tahun;
  const nativeForm = document.createElement("form");
  nativeForm.method = "GET";
  nativeForm.action = route("simple.print.laporan-lembur");

  const formData = form.data();
  for (const key in formData) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = key;
    input.value =
      key === "tanggal_pengajuan" ? formData[key].toISOString() : formData[key];
    nativeForm.appendChild(input);
  }
  document.body.appendChild(nativeForm);
  nativeForm.submit();
  document.body.removeChild(nativeForm);
};

const uploadDialog = ref(false);
const fileupload = ref(null);
const uploadForm = useForm({
  _token: null,
  lembur_id: null,
  file: null,
});
const openUploadDialog = (lemburId) => {
  uploadForm.reset();
  uploadForm.lembur_id = lemburId;
  uploadDialog.value = true;
};
const onSelectFile = (event) => {
  const file = event?.files?.[0] ?? null;
  uploadForm.file = file;
};
const toUpload = async () => {
  const { data: tokens } = await axios.get(route("api.token.csrf"));
  uploadForm._token = tokens;
  uploadForm.post(route("simple.upload.laporan-lembur"), {
    onSuccess: () => {
      uploadDialog.value = false;
      uploadForm.reset();
      if (fileupload.value) {
        fileupload.value.clear();
      }
    },
  });
};
</script>
