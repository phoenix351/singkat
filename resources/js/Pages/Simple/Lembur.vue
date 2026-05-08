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
        v-model:expandedRows="expandedRows"
        dataKey="id"
        :rowExpandable="isRowExpandable"
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
        <Column header="Tim Kerja">
          <template #body="{ data }">
            {{ data.pegawai && data.pegawai.length > 0 ? data.tim_kerja : "-" }}
          </template>
        </Column>
        <Column header="Tanggal">
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? data.pegawai[0].tanggal
                : "-"
            }}
          </template>
        </Column>
        <Column header="Jam Mulai">
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? data.pegawai[0].jam_mulai
                : "-"
            }}
          </template>
        </Column>
        <Column header="Jam Selesai">
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? data.pegawai[0].jam_selesai
                : "-"
            }}
          </template>
        </Column>
        <Column header="No. SPKL" field="nomor_spkl" sortable />
        <Column header="Maksud" field="maksud_lembur" sortable />
        <Column header="Edit/Hapus" :exportable="false">
          <template #body="slotProps">
            <div class="flex justify-end gap-2 w-full">
              <Button
                @click="updateData(slotProps.data)"
                icon="pi pi-pencil"
                variant="outlined"
                rounded
                class="mr-2"
              />
              <Button
                @click="deleteData(slotProps.data)"
                icon="pi pi-trash"
                variant="outlined"
                rounded
                severity="danger"
              />
            </div>
          </template>
        </Column>
        <template #expansion="slotProps">
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex mb-2 justify-between flex-wrap items-center">
              <h5 class="font-bold">Daftar Pegawai Lembur</h5>
              <Button
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
              <Column header="Status" style="text-align: center">
                <template #body="{ data }">
                  <div class="flex flex-col items-center justify-center gap-1">
                    <Badge
                      size="small"
                      :value="data.status"
                      :severity="
                        data.status === 'pending'
                          ? 'warn'
                          : data.status === 'setuju'
                          ? 'success'
                          : data.status === 'ditolak'
                          ? 'danger'
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
              <Column header="Hapus" :exportable="false">
                <template #body="slotProps">
                  <div class="flex justify-center gap-2 w-full">
                    <Button
                      @click="deletePegawai(slotProps.data)"
                      icon="pi pi-trash"
                      variant="outlined"
                      rounded
                      severity="danger"
                    />
                  </div>
                </template>
              </Column>
            </DataTable>
          </div>
        </template>
      </DataTable>
    </div>
    <Dialog
      v-model:visible="createDialog"
      modal
      header="Tambah Data Baru"
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
          <div
            v-if="page.props.errors.tim_id"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.tim_id }}
          </div>
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
          <div
            v-if="page.props.errors.maksud_lembur"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.maksud_lembur }}
          </div>
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
          <div
            v-if="page.props.errors.anggotalembur"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.anggotalembur }}
          </div>
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
            <div
              v-if="page.props.errors.tanggal"
              class="text-red-500 text-sm mt-2"
            >
              {{ page.props.errors?.tanggal }}
            </div>
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
            <div
              v-if="page.props.errors.jam_mulai"
              class="text-red-500 text-sm mt-2"
            >
              {{ page.props.errors?.jam_mulai }}
            </div>
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
            <div
              v-if="page.props.errors.jam_selesai"
              class="text-red-500 text-sm mt-2"
            >
              {{ page.props.errors?.jam_selesai }}
            </div>
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
    <Dialog
      v-model:visible="updateDialog"
      modal
      header="Edit Data"
      class="min-w-[40vw]"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2">Maksud Lembur</label>
          <InputText
            placeholder="Pilih maksud lembur yang sudah ada atau tambah baru"
            v-model="editedLembur.maksud_lembur"
            fluid
          />
          <div
            v-if="page.props.errors.maksud_lembur"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.maksud_lembur }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-2">Tanggal</label>
          <DatePicker
            v-model="editedLembur.pegawai[0].tanggal"
            fluid
            showIcon
            dateFormat="dd-mm-yy"
            placeholder="Isi tanggal lembur"
          />
          <div
            v-if="page.props.errors['pegawai.0.tanggal']"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.["pegawai.0.tanggal"] }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-2">Jam Mulai</label>
          <InputText
            type="time"
            placeholder="Isi jam mulai lembur"
            v-model="editedLembur.pegawai[0].jam_mulai"
            showClear
            fluid
          />
          <div
            v-if="page.props.errors['pegawai.0.jam_mulai']"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.["pegawai.0.jam_mulai"] }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-2">Jam Selesai</label>
          <InputText
            type="time"
            placeholder="Isi jam selesai lembur"
            v-model="editedLembur.pegawai[0].jam_selesai"
            fluid
            showClear
          />
          <div
            v-if="page.props.errors['pegawai.0.jam_selesai']"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.["pegawai.0.jam_selesai"] }}
          </div>
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancel"
          @click="updateDialog = false"
          size="small"
          severity="danger"
          autofocus
        />
        <Button
          @click="submit({ patch: true })"
          label="Simpan"
          size="small"
          severity="success"
          autofocus
        />
      </template>
    </Dialog>
    <Dialog
      v-model:visible="pegawaiDialog"
      modal
      header="Tambah Pegawai"
      class="min-w-[40vw]"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2">Anggota yang Lembur</label>
          <MultiSelect
            filter
            showClear
            v-model="editedData.anggotalembur"
            :options="anggotaTim"
            optionLabel="label"
            optionValue="value"
            placeholder="Pilih anggota yang lembur"
            :max-selected-labels="0"
            :selected-items-label="
              editedData.anggotalembur?.length === anggotaTim.length
                ? 'Seluruh anggota terpilih'
                : '{0} anggota terpilih'
            "
            class="w-full"
          />
          <div
            v-if="page.props.errors.anggotalembur"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.anggotalembur }}
          </div>
        </div>
      </div>
      <template #footer>
        <Button
          label="Cancel"
          @click="pegawaiDialog = false"
          size="small"
          severity="danger"
          autofocus
        />
        <Button
          @click="submit({ patch: true, add_pegawai: true })"
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
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { useConfirm } from "primevue";
import { computed, ref, watch } from "vue";

const page = usePage();
const confirm = useConfirm();
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
  add_pegawai: false,
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
const updateDialog = ref(false);
const editedLembur = ref({ pegawai: [{}] });
const updateData = (data) => {
  updateDialog.value = true;
  let copy = JSON.parse(JSON.stringify(data));
  if (copy.pegawai && copy.pegawai.length > 0 && copy.pegawai[0].tanggal) {
    copy.pegawai[0].tanggal = new Date(copy.pegawai[0].tanggal);
  } else if (!copy.pegawai || copy.pegawai.length === 0) {
    copy.pegawai = [{}];
  }
  editedLembur.value = copy;
};
const submit = async ({ patch = false, add_pegawai = false }) => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    form._token = tokens._token;
    if (patch) {
      let dataToUpdate = add_pegawai ? editedData.value : editedLembur.value;
      router.patch(
        route("simple.lembur.patch"),
        {
          _token: tokens,
          ...dataToUpdate,
          add_pegawai: add_pegawai,
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            pegawaiDialog.value = false;
            updateDialog.value = false;
            fetchData();
          },
        }
      );
    } else {
      form.post(route("simple.lembur.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          createDialog.value = false;
          form.reset();
        },
      });
    }
  } catch (error) {
    console.error(error);
  }
};
const pegawaiDialog = ref(false);
const editedData = ref({ anggotalembur: [] });
const addPegawaiTo = async (data) => {
  pegawaiDialog.value = true;
  editedData.value = {
    ...data,
    anggotalembur: data.pegawai ? data.pegawai.map((p) => p.pegawai_id) : [],
  };
  const { data: anggota } = await axios.get(
    route("man-management.fetch-anggota-tim", { id: editedData.value.tim_id })
  );
  anggotaTim.value = anggota;
};
const deletePegawai = (data) => {
  confirm.require({
    message: "Apakah kamu yakin menghapus pegawai ini?",
    header: "Konfirmasi",
    icon: "pi pi-info-circle",
    rejectLabel: "Cancel",
    acceptLabel: "Hapus",
    rejectProps: {
      label: "Cancel",
      size: "small",
      severity: "danger",
    },
    acceptProps: {
      label: "Hapus",
      size: "small",
      severity: "success",
    },
    accept: async () => {
      const { data: tokens } = await axios.get(route("api.token.csrf"));
      router.delete(route("simple.lembur.destroy-pegawai", { id: data.id }), {
        _token: tokens,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          fetchData();
        },
      });
    },
  });
};
const deleteData = (data) => {
  confirm.require({
    message: "Apakah kamu yakin menghapus pegawai ini?",
    header: "Konfirmasi",
    icon: "pi pi-info-circle",
    rejectLabel: "Cancel",
    acceptLabel: "Hapus",
    rejectProps: {
      label: "Cancel",
      size: "small",
      severity: "danger",
    },
    acceptProps: {
      label: "Hapus",
      size: "small",
      severity: "success",
    },
    accept: async () => {
      const { data: tokens } = await axios.get(route("api.token.csrf"));
      router.delete(route("simple.lembur.destroy", { id: data.id }), {
        _token: tokens,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          fetchData();
        },
      });
    },
  });
};
watch(createDialog, () => {
  form.reset();
  anggotaTim.value = [];
});
watch(pegawaiDialog, () => {
  anggotaTim.value = [];
});
</script>

<style scoped></style>
