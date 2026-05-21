<template>
  <Head title="Lembur" />
  <SimpleLayout :is-open="isSidebarOpen">
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div
          class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0"
        >
          Verifikasi Kepala Bagian Umum
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText placeholder="Cari Pegawai" v-model="searchField" />
          </IconField>
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
        <Column header="Tim Kerja" sortable :show-filter-menu="false">
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
          field="tanggal"
          sortable
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
          class="whitespace-nowrap"
          header="Status Pengajuan"
          field="status_pengajuan"
          sortable
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
        <Column header="Maksud" field="maksud_lembur" sortable />
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
        <Column header="Setuju/Tolak" :exportable="false">
          <template #body="slotProps">
            <div class="flex justify-end gap-2 w-full">
              <Button
                @click="updateData({ data: slotProps.data, status: 'setuju' })"
                icon="pi pi-check"
                variant="outlined"
                rounded
                class="mr-2"
              />
              <Button
                @click="updateData({ data: slotProps.data, status: 'ditolak' })"
                icon="pi pi-times"
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
              <div class="flex justify-end w-auto">
                <Button
                  @click="updateData({ status: 'setuju', individual: true })"
                  v-if="selectedPegawai.length > 0"
                  rounded
                  class="mr-2"
                >
                  <i class="pi pi-check" />
                  Terima
                </Button>
                <Button
                  @click="updateData({ status: 'ditolak', individual: true })"
                  v-if="selectedPegawai.length > 0"
                  rounded
                  severity="danger"
                >
                  <i class="pi pi-times" />
                  Tolak
                </Button>
              </div>
            </div>
            <DataTable
              v-model:selection="selectedPegawai"
              :value="slotProps.data.pegawai"
              data-key="id"
              showGridlines
              paginator
              :rows="10"
              :rowsPerPageOptions="[10, 20]"
              size="small"
            >
              <Column selection-mode="multiple" />
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
                class="whitespace-nowrap"
                header="Status"
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
              <Column header="Terakhir diedit">
                <template #body="{ data }">{{
                  data.edited?.username
                }}</template>
              </Column>
            </DataTable>
          </div>
        </template>
      </DataTable>
    </div>
    <Dialog
      v-model:visible="confirmDialog"
      modal
      header="Konfirmasi"
      class="w-full md:w-[30vw]"
    >
      <div class="flex flex-col gap-4">
        <p>
          <span v-if="!confirmData.individual"
            >Kamu akan melakukan perubahan status ke seluruh pegawai di lembur
            ini.
          </span>
          Apakah kamu yakin
          {{ confirmData.status === "setuju" ? "menyetujui" : "menolak" }} data
          lembur ini?
        </p>
        <div v-if="confirmData.status === 'ditolak'">
          <label class="block font-bold mb-2">Catatan Penolakan</label>
          <Textarea
            v-model="confirmData.catatan"
            rows="3"
            placeholder="Alasan penolakan..."
            fluid
            class="w-full"
          />
        </div>
      </div>
      <template #footer>
        <Button
          label="Batal"
          @click="confirmDialog = false"
          variant="outlined"
          severity="secondary"
          size="small"
        />
        <Button
          :label="confirmData.status === 'setuju' ? 'Setujui' : 'Tolak'"
          :severity="confirmData.status === 'setuju' ? 'success' : 'danger'"
          @click="processUpdate"
          size="small"
        />
      </template>
    </Dialog>
  </SimpleLayout>
</template>

<script setup>
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { onMounted, ref, watch } from "vue";

const isSidebarOpen = ref(true);
onMounted(() => {
  isSidebarOpen.value = false;
});
const searchField = ref(null);
const filterModel = ref({
  tim_kerja: null,
  tanggal: null,
  maksud_lembur: null,
});
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
    .replace(/\./g, ":");
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
    const { data } = await axios.get(route("simple.lembur.verify-kabag"), {
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
const selectedPegawai = ref([]);
const confirmDialog = ref(false);
const confirmData = ref({
  data: {},
  status: "",
  individual: false,
  catatan: "",
});

const updateData = ({ data = {}, status, individual = false }) => {
  confirmData.value = {
    data,
    status,
    individual,
    catatan: "",
  };
  confirmDialog.value = true;
};

const processUpdate = async () => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    router.patch(
      route("simple.lembur.verify-kabag-patch"),
      {
        _token: tokens,
        individual: confirmData.value.individual,
        status: confirmData.value.status,
        catatan: confirmData.value.catatan,
        lembur_id: confirmData.value.data.id,
        lembur_pegawai: selectedPegawai.value.map((p) => p.id),
      },
      {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          confirmDialog.value = false;
          selectedPegawai.value = [];
          fetchData();
        },
      }
    );
  } catch (error) {
    console.error(error);
  }
};
const toDocumentation = (link) => {
  const url = link.startsWith("http") ? link : `https://${link}`;
  window.open(url, "_blank", "noopener,noreferrer");
};
</script>

<style scoped></style>
