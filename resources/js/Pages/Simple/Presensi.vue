<template>
  <Head title="Presensi Lembur" />
  <SimpleLayout>
    <div class="text-xl font-bold mb-4">Rekap Presensi Lembur</div>
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
      <div>
        <Button
          label="Tampilkan"
          @click="fetchData"
          icon="pi pi-search"
          class="mb-0"
        />
      </div>
      <div>
        <Button
          @click="createDialog = true"
          label="Cetak SPKL"
          icon="pi pi-print"
          severity="info"
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
        <Column header="No. SPKL" field="pegawai_id">
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
        <Column header="Tanggal Pelaksanaan" field="tanggal">
          <template #body="{ data }">{{
            formatDateOnly(data.tanggal)
          }}</template>
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
          <label class="block font-bold mb-2">Nomor SPKL</label>
          <InputText
            placeholder="Isikan nomor spkl"
            v-model="form.nomor_spkl"
            fluid
          />
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
  </SimpleLayout>
</template>

<script setup>
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { ref, watch } from "vue";

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
</script> 