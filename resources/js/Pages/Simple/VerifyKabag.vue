<template>
  <Head title="Lembur" />
  <SimpleLayout>
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
            <InputText placeholder="Cari Data" />
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
                ? formatDateOnly(data.pegawai[0].tanggal)
                : "-"
            }}
          </template>
        </Column>
        <Column header="Jam Mulai">
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? formatTimeOnly(data.pegawai[0].jam_mulai)
                : "-"
            }}
          </template>
        </Column>
        <Column header="Jam Selesai">
          <template #body="{ data }">
            {{
              data.pegawai && data.pegawai.length > 0
                ? formatTimeOnly(data.pegawai[0].jam_selesai)
                : "-"
            }}
          </template>
        </Column>
        <Column
          class="min-w-[150px]"
          header="No. SPKL"
          field="nomor_spkl"
          sortable
        >
          <template #body="{ data }">
            <span v-if="data.nomor_spkl">{{ data.nomor_spkl }}</span>
            <Badge v-else severity="secondary" value="belum diajukan" />
          </template>
        </Column>
        <Column header="Maksud" field="maksud_lembur" sortable />
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
              <Column header="Status" style="text-align: center">
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
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { useConfirm } from "primevue";
import { computed, ref, watch } from "vue";

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
    route("simple.lembur.verify-kabag"),
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
</script>

<style scoped></style>
