<template>
  <Head title="Aplikasi" />
  <AppLayout>
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
          Daftar Aplikasi
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model.trim="searchField" placeholder="Cari Tim Kerja" />
          </IconField>
          <Button
            icon="pi pi-download"
            rounded
            aria-label="Download"
            severity="success"
            class="mr-2 mb-2 lg:mb-0"
          />
          <!-- @click="showToast" -->
          <Button
            @click="createDialog = true"
            severity="info"
            rounded
            class="mb-2 lg:mb-0"
          >
            <i class="pi pi-plus"></i>
            Tambah Aplikasi Baru
          </Button>
        </div>
      </div>
      <DataTable
        :value="apps.data"
        class="w-full"
        lazy
        paginator
        :rows="apps.per_page"
        :first="(apps.current_page - 1) * apps.per_page"
        :total-records="apps.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortField"
        :sort-order="sortOrder"
        @page="fetchData"
        @sort="fetchData"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
      >
        <Column header="Nama Aplikasi" field="label" sortable>
          <template #body="{ data }">
            <div class="flex items-center gap-2">
              <img style="width: 32px" :src="'/storage/' + data.image_path" />
              <span>{{ data.label }}</span>
            </div>
          </template>
        </Column>
        <Column header="Deskripsi" field="deskripsi" sortable />
        <Column header="Link Aplikasi" field="route_link" sortable>
          <template #body="{ data }">
            <Badge
              @click="goTo(data.route_link)"
              class="badge-app link-col"
              v-tooltip.right="data.route_link"
              :value="data.route_link"
              severity="secondary"
            ></Badge>
          </template>
        </Column>
        <Column header="Navigation" field="navigation" sortable>
          <template #body="{ data }">
            <Badge :value="data.navigation" severity="secondary"></Badge>
          </template>
        </Column>
        <Column header="Maintenance" field="maintenance" sortable>
          <template #body="{ data }">
            <Badge
              :value="data.maintenance == 1 ? 'TRUE' : 'FALSE'"
              :severity="data.maintenance == 1 ? 'success' : 'danger'"
              size="small"
            ></Badge>
          </template>
        </Column>
        <Column header="Message" field="maintenance_message" sortable />
        <Column :exportable="false">
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
      </DataTable>
    </div>
    <Dialog
      v-model:visible="createDialog"
      modal
      :header="isUpdated ? 'Edit Data' : 'Tambah Aplikasi Baru'"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-6">
        <div>
          <label class="block font-bold mb-3">Nama Aplikasi</label>
          <InputText
            v-if="!isUpdated"
            v-model="form.label"
            placeholder="Isi nama aplikasi"
            class="w-full"
          />
          <InputText
            v-if="isUpdated"
            v-model="editedData.label"
            placeholder="Isi nama aplikasi"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Deskripsi</label>
          <InputText
            v-if="!isUpdated"
            v-model="form.deskripsi"
            placeholder="Isi deskripsi"
            class="w-full"
          />
          <InputText
            v-if="isUpdated"
            v-model="editedData.deskripsi"
            placeholder="Isi deskripsi"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Link Aplikasi</label>
          <InputText
            v-if="!isUpdated"
            v-model="form.route_link"
            placeholder="Isi link aplikasi"
            class="w-full"
          />
          <InputText
            v-if="isUpdated"
            v-model="editedData.route_link"
            placeholder="Isi link aplikasi"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Navigation</label>
          <Select
            v-if="!isUpdated"
            :options="[
              { label: 'react', value: 'react' },
              { label: 'reload', value: 'reload' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="form.navigation"
            placeholder="Isi navigation"
          />
          <Select
            v-if="isUpdated"
            :options="[
              { label: 'react', value: 'react' },
              { label: 'reload', value: 'reload' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="editedData.navigation"
            placeholder="Isi navigation"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Sedang maintenance</label>
          <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2">
              <RadioButton
                v-if="!isUpdated"
                v-model="form.maintenance"
                input-id="maintenance_true"
                name="maintenance"
                :value="1"
              />
              <RadioButton
                v-if="isUpdated"
                v-model="editedData.maintenance"
                input-id="maintenance_true"
                name="maintenance"
                :value="1"
              />
              <label for="maintenance_true">Ya</label>
            </div>
            <div class="flex items-center gap-2">
              <RadioButton
                v-if="!isUpdated"
                v-model="form.maintenance"
                input-id="maintenance_false"
                name="maintenance"
                :value="0"
              />
              <RadioButton
                v-if="isUpdated"
                v-model="editedData.maintenance"
                input-id="maintenance_false"
                name="maintenance"
                :value="0"
              />
              <label for="maintenance_false">Tidak</label>
            </div>
          </div>
        </div>
        <div v-if="form.maintenance == true || editedData.maintenance == true">
          <label class="block font-bold mb-3">Pesan Maintenance</label>
          <InputText
            v-if="!isUpdated"
            v-model="form.maintenance_message"
            placeholder="Isi pesan maintenance"
            class="w-full"
          />
          <InputText
            v-if="isUpdated"
            v-model="editedData.maintenance_message"
            placeholder="Isi pesan maintenance"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-bold mb-3">Upload Logo Aplikasi</label>
          <FileUpload
            ref="fileupload"
            mode="basic"
            name="file"
            accept="image/*"
            :max-file-size="1000000"
            @select="onSelectFile"
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
          label="Simpan"
          size="small"
          severity="success"
          autofocus
        />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/ManManagement/AppLayout.vue";
import { debounce } from "@/Layouts/ManManagement/Composables/debounce";
import { Head, router, useForm } from "@inertiajs/vue3";
import { useConfirm } from "primevue";
import { computed, ref, watch } from "vue";

const searchField = ref(null);
const createDialog = ref(false);
const props = defineProps({
  apps: {
    type: Object,
  },
});
const form = useForm({
  _token: null,
  label: null,
  deskripsi: null,
  route_link: null,
  navigation: null,
  maintenance: 0,
  maintenance_message: null,
  image: null,
});

watch(
  () => form.maintenance,
  () => {
    if (!form.maintenance) form.maintenance_message = null;
  }
);

//paginated and search
const currentPage = ref((props.apps.current_page - 1) * props.apps.per_page);
const paginated = ref(props?.apps?.per_page ?? 10);
const sortField = ref(null);
const sortOrder = ref(null);
const fetchData = (event = null) => {
  currentPage.value = event ? Math.floor(event.first / event.rows) + 1 : 1;
  paginated.value = event?.rows ?? paginated.value;
  router.get(
    route("man-management.app-management.index"),
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
const delayedFetchData = debounce(() => {
  fetchData();
});
watch(searchField, () => {
  delayedFetchData();
});

const onSelectFile = (event) => {
  if (!isUpdated.value) {
    form.image = event?.files?.[0] ?? null;
  } else {
    const image = event?.files?.[0] ?? null;
    editedData.value.image = image;
  }
};

//submit and update
const isUpdated = ref(false);
const editedData = ref({});
const updateData = (data) => {
  isUpdated.value = true;
  createDialog.value = true;
  editedData.value = { ...data };
};
watch(editedData, () => {
  if (!editedData?.value?.maintenance ?? null)
    editedData.value.maintenance_message = null;
});
const submit = async () => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    form._token = tokens;
    if (!isUpdated.value) {
      form.post(route("man-management.app-management.store"), {
        preserveScroll: false,
        preserveState: false,
      });
      return;
    }
    if (isUpdated.value) {
      const formData = new FormData();
      formData.append("_token", tokens);
      formData.append("_method", "PATCH");
      Object.keys(editedData.value).forEach((k) => {
        let value = editedData.value[k];
        if (value === null || value === undefined) return;
        formData.append(k, value);
      });
      router.post(route("man-management.app-management.patch"), formData, {
        preserveScroll: false,
        preserveState: false,
      });
      return;
    }
  } catch (error) {
    console.error(error);
  }
};
//delete
const confirm = useConfirm();
const deleteData = (data) => {
  confirm.require({
    message: "Apakah kamu yakin menghapus data ini?",
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
      router.delete(route("man-management.app-management.destroy", { id: data.id }), {
        _token: tokens,
      });
    },
  });
};
watch(createDialog, () => {
  if (createDialog.value == false) {
    isUpdated.value = false;
    editedData.value = {};
  }
});
const goTo = (link) => {
  const url = link.startsWith("http") ? link : `https://${link}`;
  window.open(url, "_blank", "noopener,noreferrer");
};
</script>

<style scoped>
.badge-app:hover {
  cursor: pointer;
}
.link-col {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 220px;
}
</style>
