<template>
  <Head title="Lembur" />
  <SimpleLayout :is-open="isSidebarOpen">
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div
          class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0"
        >
          Pengajuan Lembur Saya
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText placeholder="Cari Pegawai" v-model="searchField" />
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
        class="w-full text-sm"
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
        :rows-per-page-options="[5, 10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortField"
        :sort-order="sortOrder"
        filterDisplay="row"
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
        <Column
          header="Tim Kerja"
          field="tim_kerja"
          sortable
          :showFilterMenu="false"
        >
          <template #body="{ data }">
            {{ data.pegawai && data.pegawai.length > 0 ? data.tim_kerja : "-" }}
          </template>
          <template #filter>
            <InputText
              v-model="filterModel.tim_kerja"
              class="text-sm"
              fluid
              placeholder="Cari tim kerja"
            />
          </template>
        </Column>
        <Column
          header="Tanggal"
          :show-filter-menu="false"
          sortable
          field="tanggal"
        >
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? formatDateOnly(data.pegawai[0].tanggal)
                : "-"
            }}
          </template>
          <template #filter>
            <InputText
              v-model="filterModel.tanggal"
              class="text-sm"
              fluid
              placeholder="Cari tanggal"
            />
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
        <Column
          header="Status Pengajuan"
          class="whitespace-nowrap"
          sortable
          field="status_pengajuan"
        >
          <template #body="{ data }">
            <div class="flex flex-col gap-1">
              <Badge
                size="small"
                v-for="item in getStatusCounts(data.pegawai)"
                :key="item.label"
                :value="`${item.count} ${item.label}`"
                :severity="
                  item.code === '1'
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
                "
              />
            </div>
          </template>
        </Column>
        <Column
          header="Maksud"
          field="maksud_lembur"
          sortable
          :show-filter-menu="false"
        >
          <template #filter>
            <InputText
              v-model="filterModel.maksud_lembur"
              class="text-sm"
              fluid
              placeholder="Cari maksud lembur"
            />
          </template>
        </Column>
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
        <Column header="Edit/Hapus" :exportable="false">
          <template #body="slotProps">
            <div class="flex justify-end gap-2 w-full">
              <Button
                @click="copyData(slotProps.data)"
                icon="pi pi-copy"
                variant="outlined"
                rounded
                class="mr-2"
                severity="success"
                v-tooltip.top="'Copy'"
              />
              <Button
                @click="
                  isProcessed(slotProps.data)
                    ? null
                    : updateData(slotProps.data)
                "
                :icon="
                  isProcessed(slotProps.data) ? 'pi pi-lock' : 'pi pi-pencil'
                "
                variant="outlined"
                rounded
                class="mr-2"
                :severity="
                  isProcessed(slotProps.data) ? 'secondary' : 'success'
                "
                v-tooltip.top="
                  isProcessed(slotProps.data)
                    ? 'Ada data yang sudah diproses, tidak bisa bulk edit'
                    : ''
                "
              />
              <Button
                @click="
                  isProcessed(slotProps.data)
                    ? null
                    : deleteData(slotProps.data)
                "
                :icon="
                  isProcessed(slotProps.data) ? 'pi pi-lock' : 'pi pi-trash'
                "
                variant="outlined"
                rounded
                :severity="isProcessed(slotProps.data) ? 'secondary' : 'danger'"
                v-tooltip.top="
                  isProcessed(slotProps.data)
                    ? 'Ada data yang sudah diproses, tidak bisa bulk edit'
                    : ''
                "
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
              paginator
              :rows="10"
              :rowsPerPageOptions="[10, 20]"
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
              <Column
                header="Status"
                class="whitespace-nowrap"
                style="text-align: center"
              >
                <template #body="{ data }">
                  <div class="flex flex-col items-center justify-center gap-1">
                    <Badge
                      size="small"
                      :value="data.status_detail || data.status"
                      :severity="
                        String(data.status) === '1'
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
              <Column header="Catatan" class="whitespace-nowrap">
                <template #body="{ data }">
                  <Badge
                    v-if="!data.catatan"
                    severity="secondary"
                    value="Tidak ada catatan"
                  />
                  <span v-else>{{ data.catatan }}</span>
                </template>
              </Column>
              <Column header="Terakhir diedit">
                <template #body="{ data }">{{
                  data.edited?.username
                }}</template>
              </Column>
              <Column header="Hapus" :exportable="false">
                <template #body="itemProps">
                  <div class="flex justify-center gap-2 w-full">
                    <Button
                      @click="
                        itemProps.data.status >= 2
                          ? null
                          : deletePegawai(itemProps.data)
                      "
                      :icon="
                        itemProps.data.status >= 2
                          ? 'pi pi-lock'
                          : 'pi pi-trash'
                      "
                      variant="outlined"
                      rounded
                      :severity="
                        itemProps.data.status >= 2 ? 'secondary' : 'danger'
                      "
                      v-tooltip.top="
                        itemProps.data.status >= 2
                          ? 'Status pegawai sudah diproses'
                          : ''
                      "
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
      position="top"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label class="block font-bold mb-2">Tim Kerja</label>
          <Select
            placeholder="Pilih tim kerja"
            :options="tim"
            class="w-full"
            showClear
            filter
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
            <label class="block font-bold mb-2">Jumlah Jam</label>
            <InputText
              type="number"
              max="24"
              placeholder="Isi jumlah jam lembur"
              v-model="form.jumlah_jam"
              showClear
              fluid
            />
            <div
              v-if="page.props.errors.jumlah_jam"
              class="text-red-500 text-sm mt-2"
            >
              {{ page.props.errors?.jumlah_jam }}
            </div>
          </div>
          <div>
            <label class="block font-bold mb-2">Link Dokumentasi</label>
            <InputText
              type="text"
              placeholder="Isi link dokumentasi lembur"
              v-model="form.link_dokumentasi"
              fluid
              showClear
            />
            <div
              v-if="page.props.errors.link_dokumentasi"
              class="text-red-500 text-sm mt-2"
            >
              {{ page.props.errors?.link_dokumentasi }}
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
      position="top"
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
          <label class="block font-bold mb-2">Jumlah Jam</label>
          <InputText
            type="number"
            placeholder="Isi jumlah jam lembur"
            v-model="editedLembur.pegawai[0].jumlah_jam"
            showClear
            fluid
          />
          <div
            v-if="page.props.errors['pegawai.0.jumlah_jam']"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.["pegawai.0.jumlah_jam"] }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-2">Link Dokumentasi</label>
          <InputText
            type="text"
            placeholder="Isi link dokumentasi lembur"
            v-model="editedLembur.link_dokumentasi"
            fluid
            showClear
          />
          <div
            v-if="page.props.errors.link_dokumentasi"
            class="text-red-500 text-sm mt-2"
          >
            {{ page.props.errors?.link_dokumentasi }}
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
      position="top"
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
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { useConfirm } from "primevue";
import { computed, onMounted, ref, watch } from "vue";

const isSidebarOpen = ref(true);
onMounted(() => {
  isSidebarOpen.value = false;
});
const page = usePage();
const confirm = useConfirm();
const searchField = ref(null);
const filterModel = ref({
  tim_kerja: null,
  tanggal: null,
  maksud_lembur: null,
});

const isProcessed = (data) => {
  if (!data || !data.pegawai) return false;
  return data.pegawai.some((p) => p.status == 2 || p.status > 3);
};
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
    .replace(/\./g, ":"); // Ganti format titik (bawaan id-ID) menjadi titik dua untuk jam
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
    const { data } = await axios.get(route("simple.lembur"), {
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
const createDialog = ref(false);
const form = useForm({
  _token: null,
  tim_id: null,
  anggotalembur: [],
  tanggal: null,
  jumlah_jam: null,
  maksud_lembur: null,
  add_pegawai: false,
  link_dokumentasi: null,
});
const anggotaTim = ref([]);
const maksudLembur = ref([]);
let skipWatch = false;
watch(
  () => form.tim_id,
  async (tim) => {
    if (tim) {
      if (!skipWatch) form.anggotalembur = [];
      const { data: anggota } = await axios.get(
        route("man-management.fetch-anggota-tim", { id: tim })
      );
      anggotaTim.value = anggota;
      if (!skipWatch) form.anggotalembur = [...anggota.map((a) => a.value)];

      if (!skipWatch) form.maksud_lembur = null;
      const { data: maksud } = await axios.get(
        route("simple.fetch-maksud", { tim_id: tim })
      );
      maksudLembur.value = maksud;
      skipWatch = false;
    } else {
      form.anggotalembur = [];
      skipWatch = false;
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
    form._token = tokens;
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
        data: {
          _token: tokens,
        },
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
        data: {
          _token: tokens,
        },
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          fetchData();
        },
      });
    },
  });
};
watch(createDialog, (val) => {
  if (!val) {
    form.reset();
    anggotaTim.value = [];
  }
});
watch(pegawaiDialog, (val) => {
  if (!val) anggotaTim.value = [];
});
const toDocumentation = (link) => {
  const url = link.startsWith("http") ? link : `https://${link}`;
  window.open(url, "_blank", "noopener,noreferrer");
};
const copyData = async (data) => {
  if (form.tim_id !== data.tim_id) {
    skipWatch = true;
  } else {
    if (anggotaTim.value.length === 0) {
      const { data: anggota } = await axios.get(route("man-management.fetch-anggota-tim", { id: data.tim_id }));
      anggotaTim.value = anggota;
    }
    if (maksudLembur.value.length === 0) {
      const { data: maksud } = await axios.get(route("simple.fetch-maksud", { tim_id: data.tim_id }));
      maksudLembur.value = maksud;
    }
  }

  form.tim_id = data.tim_id;
  form.anggotalembur = data.pegawai ? data.pegawai.map((p) => p.pegawai_id) : [];
  form.tanggal = null;
  form.jumlah_jam = data.pegawai && data.pegawai.length > 0 ? data.pegawai[0].jumlah_jam : null;
  form.maksud_lembur = data.maksud_lembur;
  form.add_pegawai = false;
  form.link_dokumentasi = null;
  createDialog.value = true;
};
</script>

<style scoped></style>
