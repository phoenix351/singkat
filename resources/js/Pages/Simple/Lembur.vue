<template>
  <Head title="Lembur" />
  <SimpleLayout>
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div
          class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0"
        >
          Pengajuan Lembur
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText placeholder="Cari Data" />
          </IconField>
          <Button
            icon="pi pi-download"
            rounded
            aria-label="Download"
            severity="success"
            class="mr-2 mb-2 lg:mb-0"
          />
          <Button
            @click="createDialog = true"
            severity="info"
            rounded
            class="mb-2 lg:mb-0"
          >
            <i class="pi pi-plus"></i>
            Ajukan Lembur
          </Button>
        </div>
      </div>
      <DataTable
        :value="paginatedItem.data"
        class="w-full"
        lazy
        paginator
        showGridlines
        stripedRows
        :rows="paginatedItem.per_page"
        :first="(paginatedItem.current_page - 1) * paginatedItem.per_page"
        :total-records="paginatedItem.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortField"
        :sort-order="sortOrder"
        @page="fetchData"
        @sort="fetchData"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
      >
        <template #empty>
          <div class="text-center">Data tidak ada</div>
        </template>
        <Column header="Pegawai" field="nama_pegawai" sortable />
        <Column header="Tanggal" field="tanggal" sortable />
        <Column header="Jam Mulai" field="jam_mulai" sortable />
        <Column header="Jam Selesai" field="jam_selesai" sortable />
        <Column header="No. SPKL" field="nomor_spkl" sortable />
        <Column header="Maksud" field="maksud_lembur" sortable />
      </DataTable>
    </div>
    <Dialog
      v-model:visible="createDialog"
      modal
      :header="isUpdated ? 'Edit Data' : 'Tambah Data Baru'"
      class="min-w-[40vw]"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2">Tim Kerja</label>
          <Select
            placeholder="Pilih tim kerja"
            :options="tim"
            class="w-full"
            showClear
            option-label="tim_kerja"
            option-value="tim_id"
            v-model="form.tim_id"
          />
        </div>
        <div>
          <label class="block font-bold mb-2">Maksud Lembur</label>
          <Select
            placeholder="Pilih maksud lembur yang sudah ada atau tambah baru"
            :options="maksudLembur"
            showClear
            editable
            emptyMessage="Ketik untuk menambah maksud baru..."
            v-model="form.maksud_lembur"
            fluid
          />
        </div>
        <div v-if="form.tim_id">
          <label class="block font-bold mb-2">Anggota yang Lembur</label>
          <MultiSelect
            filter
            showClear
            v-model="form.anggotalembur"
            :options="anggotaTim"
            optionLabel="label"
            optionValue="value"
            placeholder="Pilih anggota yang lembur"
            :max-selected-labels="0"
            :selected-items-label="
              form.anggotalembur.length === anggotaTim.length
                ? 'Seluruh anggota terpilih'
                : '{0} anggota terpilih'
            "
            class="w-full"
          />
        </div>
        <template v-if="form.anggotalembur && form.anggotalembur.length > 0">
          <div>
            <label class="block font-bold mb-2">Tanggal</label>
            <DatePicker
              v-model="form.tanggal"
              fluid
              showIcon
              dateFormat="dd-mm-yy"
              placeholder="Isi tanggal lembur"
            />
          </div>
          <div>
            <label class="block font-bold mb-2">Jam Mulai</label>
            <InputText
              type="time"
              placeholder="Isi jam mulai lembur"
              v-model="form.jam_mulai"
              showClear
              fluid
            />
          </div>
          <div>
            <label class="block font-bold mb-2">Jam Selesai</label>
            <InputText
              type="time"
              placeholder="Isi jam selesai lembur"
              v-model="form.jam_selesai"
              fluid
              showClear
            />
          </div>
        </template>
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
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref, watch } from "vue";

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
const currentPage = computed(
  () => (paginatedItem.value.current_page - 1) * paginatedItem.value.per_page
);
const paginated = computed(() => paginatedItem?.value?.per_page ?? 10);
const sortField = ref(null);
const sortOrder = ref(null);
const fetchData = (event = null) => {
  currentPage.value = event ? Math.floor(event.first / event.rows) + 1 : 1;
  paginated.value = event?.rows ?? paginated.value;
  router.get(
    route("simple.lembur"),
    {
      currentPage: currentPage.value,
      paginated: paginated.value,
      sortField: event?.sortField ?? sortField.value,
      sortOrder: event?.sortOrder ?? sortOrder.value,
      searchField: searchField.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  );
};

//submit
const createDialog = ref(false);
const isUpdated = ref(false);
const form = useForm({
  _token: null,
  tim_id: null,
  anggotalembur: [],
  tanggal: null,
  jam_mulai: null,
  jam_selesai: null,
  maksud_lembur: null,
});
const anggotaTim = ref([]);
const maksudLembur = ref([]);
watch(
  () => form.tim_id,
  async (tim) => {
    if (tim) {
      form.anggotalembur = [];
      const { data: anggota } = await axios.get(
        route("man-management.fetch-anggota-tim", { id: tim })
      );
      anggotaTim.value = anggota;
      form.anggotalembur = [...anggota.map((a) => a.value)];

      form.maksud_lembur = null;
      const { data: maksud } = await axios.get(
        route("simple.fetch-maksud", { tim_id: tim })
      );
      maksudLembur.value = maksud;
    } else {
      form.anggotalembur = [];
    }
  }
);
const submit = async () => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    form._token = tokens._token;
    form.post(route("simple.store"), {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        createDialog.value = false;
        form.reset();
      },
    });
  } catch (error) {
    console.error(error);
  }
};
watch(createDialog, () => {
  form.reset();
  anggotaTim.value = [];
});
</script>

<style scoped></style>
