<template>
  <Head title="SPKL" />
  <SimpleLayout :is-open="isSidebarOpen">
    <div class="text-xl font-bold mb-4">Pengajuan SPKL</div>
    <div class="flex flex-wrap items-end gap-4">
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
        <Button @click="fetchData" icon="pi pi-search" class="mb-0" />
        <Button
          @click="presensiDialog = true"
          label="Presensi"
          icon="pi pi-upload"
          severity="warn"
          class="mb-0"
        />
        <Button
          @click="createDialog = true"
          label="Laporan"
          icon="pi pi-print"
          severity="info"
          class="mb-0"
        />
        <Button
          @click="printUang"
          label="Uang"
          icon="pi pi-print"
          severity="contrast"
          class="mb-0"
        />
      </div>
    </div>
    <div class="card mt-4">
      <div class="flex justify-end mb-4">
        <IconField>
          <InputIcon>
            <i class="pi pi-search" />
          </InputIcon>
          <InputText placeholder="Cari Pegawai" v-model="search.pegawai" />
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
        groupRowsBy="pegawai_id"
        rowGroupMode="rowspan"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
      >
        <template #empty>
          <div class="text-center">
            Data bulan ini belum ada yang sudah disetujui
          </div>
        </template>
        <Column header="No." style="width: 3rem">
          <template #body="slotProps">{{ slotProps.index + 1 }}</template>
        </Column>
        <Column hidden field="pegawai_id" />
        <Column class="whitespace-nowrap" header="No. SPKL" field="pegawai_id">
          <template #body="{ data }">
            <Badge
              v-if="data.lembur.spkl?.nomor_spkl"
              :value="data.lembur.spkl.nomor_spkl"
              severity="info"
            />
            <Badge v-else value="Belum diajukan" severity="secondary" />
          </template>
        </Column>
        <Column header="Nama Pegawai" field="pegawai_id">
          <template #body="{ data }">{{ data.pegawai.name }}</template>
        </Column>
        <Column header="Jabatan" field="pegawai_id">
          <template #body="{ data }">{{ data.pegawai.jabatan }}</template>
        </Column>
        <Column header="Tanggal Pelaksanaan Pengajuan" field="tanggal">
          <template #body="{ data }">{{
            formatDateOnly(data.tanggal)
          }}</template>
        </Column>
        <Column class="whitespace-nowrap" header="Jam Berangkat">
          <template #body="{ data }"
            ><span v-if="data.jam_berangkat">{{ data.jam_berangkat }}</span>
            <Badge size="small" v-if="!data.jam_berangkat" severity="secondary"
              ><span class="italic">Tidak ditemukan</span></Badge
            >
          </template>
        </Column>
        <Column class="whitespace-nowrap" header="Jam Pulang">
          <template #body="{ data }"
            ><span v-if="data.jam_pulang">{{ data.jam_pulang }}</span>
            <Badge size="small" v-if="!data.jam_pulang" severity="secondary"
              ><span class="italic">Tidak ditemukan</span></Badge
            >
          </template>
        </Column>
        <Column header="Maksud Lembur" field="lembur.maksud_lembur" />
        <Column header="Link" field="link_dokumentasi">
          <template #body="{ data }">
            <Button
              v-if="data.lembur.link_dokumentasi"
              icon="pi pi-external-link"
              variant="outlined"
              rounded
              class="mr-2"
              :severity="'info'"
              @click="toDocumentation(data.lembur.link_dokumentasi)"
            ></Button>
            <div v-else>-</div>
          </template>
        </Column>
      </DataTable>
    </div>
    <Dialog
      position="top"
      v-model:visible="createDialog"
      modal
      header="Cetak SPKL"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2">DIPA Tahun Anggaran</label>
          <Select
            v-model="form.tahun_dipa"
            placeholder="Pilih tahun DIPA"
            :options="yearDrop"
            optionLabel="label"
            optionValue="value"
            fluid
            showClear
          />
          <div
            v-if="page.props.errors.tahun_dipa"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.tahun_dipa }}
          </div>
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
          <div
            v-if="page.props.errors.tanggal_pengajuan"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.tanggal_pengajuan }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-2">Nomor SPKL</label>
          <InputText
            placeholder="Isikan nomor spkl"
            v-model="form.nomor_spkl"
            fluid
          />
          <div
            v-if="page.props.errors.nomor_spkl"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.nomor_spkl }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-2">Ttd di Rekap Presensi</label>
          <Select
            v-model="form.ttd_rekap"
            placeholder="Pilih Yang Ttd Rekap Presensi"
            :options="tim_kerja"
            optionLabel="label"
            optionValue="value"
            fluid
            filter
            showClear
          />
          <div
            v-if="page.props.errors.ttd_rekap"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.ttd_rekap }}
          </div>
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancel"
          @click="createDialog = false"
          size="small"
          severity="danger"
          autofocus
        />
        <Button
          @click="submit"
          label="Cetak"
          size="small"
          severity="success"
          autofocus
        />
      </template>
    </Dialog>
    <Dialog
      position="top"
      v-model:visible="presensiDialog"
      modal
      header="Upload Presensi"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2">Pilih Bulan</label>
          <div class="flex flex-wrap flex-row space-x-2">
            <Select
              class="w-[45%]"
              v-model="presensiYear"
              placeholder="Pilih tahun"
              :options="yearDrop"
              optionLabel="label"
              optionValue="value"
            />
            <Select
              class="w-[45%]"
              v-model="presensiMonth"
              placeholder="Pilih bulan"
              :options="monthDrop"
              optionLabel="label"
              optionValue="value"
            />
          </div>
        </div>
        <div>
          <label class="block font-bold">Pilih File Excel :</label>
        </div>
        <div class="flex flex-row space-x-2">
          <Button @click="downloadTemplate" severity="info" class="lg:mb-0">
            <i class="pi pi-file"></i>
            Template
          </Button>
          <FileUpload
            ref="fileupload"
            mode="basic"
            name="file"
            accept=".xlsx,.xls,.csv"
            :maxFileSize="1000000"
            @select="onSelectFile"
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancel"
          @click="presensiDialog = false"
          size="small"
          severity="danger"
          autofocus
        />
        <Button
          @click="submitPresensi"
          label="Upload"
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
import { computed, onMounted, ref, watch } from "vue";
import * as XLSX from "xlsx";

const isSidebarOpen = ref(true);
const page = usePage();
onMounted(() => {
  isSidebarOpen.value = false;
});
const currentYear = new Date().getFullYear();
const currentMonth = new Date().getMonth() + 1;
const formatDateOnly = (dateString) => {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};
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
  pegawai: null,
});
const props = defineProps({
  lembur: {
    type: Object,
  },
  tim_kerja: {
    type: Array,
  },
});
const paginatedItem = ref(props.lembur);
watch(
  () => props.lembur,
  (lembur) => {
    paginatedItem.value = lembur;
  }
);
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
    const { data } = await axios.get(route("simple.spkl"), {
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
  () => search.value.pegawai,
  (p) => {
    delayedFetchData();
  }
);

//search
const submit = async () => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    form._token = tokens;
    form.post(route("simple.spkl.store"), {
      onSuccess: () => {
        toPrint();
        fetchData();
        createDialog.value = false;
      },
      preserveScroll: true,
      preserveState: true,
      replace: true,
    });
  } catch (error) {
    console.error(error);
  }
};

//presensi
const presensiDialog = ref(false);
const presensiMonth = ref(null);
const presensiYear = ref(null);
const downloadTemplate = () => {
  window.location.href = "/simple/download-template/presensi";
};
const selectedFile = ref(false);
const onSelectFile = (e) => {
  let fileS = e?.files?.[0] ?? null;
  if (fileS) {
    const reader = new FileReader();
    reader.onload = (e) => {
      /* Parse data */
      const bstr = e.target.result;
      const wb = XLSX.read(bstr, { type: "binary" });
      /* Get first worksheet */
      const wsname = wb.SheetNames[0];
      const ws = wb.Sheets[wsname];
      /* Convert array of arrays */
      const data = XLSX.utils.sheet_to_json(ws, { header: 1 });
      selectedFile.value = data;
    };
    reader.readAsBinaryString(fileS);
  }
};
const presensiUploaded = computed(() => {
  if (selectedFile.value && selectedFile.value.length > 2) {
    const tanggalArray = selectedFile.value[0].slice(1);
    const hasil = [];

    const pYear = presensiYear.value || new Date().getFullYear();
    const pMonth = presensiMonth.value || new Date().getMonth() + 1;

    for (let i = 2; i < selectedFile.value.length; i++) {
      const row = selectedFile.value[i];
      const nip = row[0];
      if (!nip) continue;
      for (let j = 1; j < row.length; j++) {
        const sel = row[j];
        if (typeof sel === "string" && sel.includes("WFOL")) {
          const parts = sel.split("\r\n");
          if (parts.length >= 3) {
            const masuk = parts[0].trim();
            const pulang = parts[1].trim();

            const [mH, mM, mS] = masuk.split(":").map(Number);
            const [pH, pM, pS] = pulang.split(":").map(Number);
            const dateMasuk = new Date(0, 0, 0, mH || 0, mM || 0, mS || 0);
            const datePulang = new Date(0, 0, 0, pH || 0, pM || 0, pS || 0);

            let selisih = (datePulang - dateMasuk) / (1000 * 60 * 60);
            if (selisih < 0) selisih += 24;
            selisih = Math.round(selisih * 100) / 100;
            const tgl = Number(tanggalArray[j - 1]);
            const tanggalDate = new Date(pYear, pMonth - 1, tgl);

            hasil.push({
              nip: nip.toString(),
              tanggal: tanggalDate,
              jam_masuk: masuk,
              jam_pulang: pulang,
              lamanya: selisih,
              status_kehadiran: "WFOL",
            });
          }
        }
      }
    }

    return hasil;
  }
  return [];
});
const submitPresensi = async () => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    router.patch(
      route("simple.presensi.patch"),
      {
        _token: tokens,
        data: presensiUploaded.value,
      },
      {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          fetchData();
          presensiDialog.value = false;
        },
      }
    );
  } catch (error) {
    console.error(error);
  }
};
watch(
  () => presensiDialog.value,
  (v) => {
    if (!v) {
      selectedFile.value = false;
    }
  }
);
//print
const createDialog = ref(false);
const form = useForm({
  _token: null,
  bulan: search.value.bulan,
  tahun: search.value.tahun,
  tahun_dipa: currentYear,
  tanggal_pengajuan: new Date(),
  nomor_spkl: `SPKL-${String(new Date().getMonth() + 1).padStart(
    2,
    "0"
  )}/KP.300/${currentYear}`,
  ttd_rekap: null,
});
watch(
  () => createDialog.value,
  (v) => {
    if (v) {
      form.bulan = search.value.bulan;
      form.tahun = search.value.tahun;
      form.nomor_spkl = `SPKL-${String(search.value.bulan).padStart(
        2,
        "0"
      )}/KP.300/${currentYear}`;
    }
  }
);
const toPrint = async () => {
  const { data: tokens } = await axios.get(route("api.token.csrf"));
  form._token = tokens;
  const nativeForm = document.createElement("form");
  nativeForm.method = "GET";
  nativeForm.action = route("simple.spkl.print");

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
const toDocumentation = (link) => {
  const url = link.startsWith("http") ? link : `https://${link}`;
  window.open(url, "_blank", "noopener,noreferrer");
};

//print keuangan
const formKeuangan = useForm({
  tahun: null,
  bulan: null,
});
const printUang = async () => {
  formKeuangan.tahun = search.value.tahun;
  formKeuangan.bulan = search.value.bulan;
  const nativeForm = document.createElement("form");
  nativeForm.method = "GET";
  nativeForm.action = route("simple.spkl.keuangan-print");
  const formData = formKeuangan.data();
  for (const key in formData) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = key;
    input.value = formData[key];
    nativeForm.appendChild(input);
  }
  document.body.appendChild(nativeForm);
  nativeForm.submit();
  document.body.removeChild(nativeForm);
};
</script> 