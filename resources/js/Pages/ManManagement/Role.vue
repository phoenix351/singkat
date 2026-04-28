<template>
  <Head title="Role Management" />
  <AppLayout>
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
          Role Management
        </div>
        <div class="flex space-x-2 items-center w-full md:w-full lg:w-auto">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model.trim="searchField" placeholder="Cari Data" />
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
            Tambah Pengaturan Baru
          </Button>
        </div>
      </div>
      <DataTable
        :value="roles.data"
        class="w-full"
        lazy
        paginator
        :rows="roles.per_page"
        :first="(roles.current_page - 1) * roles.per_page"
        :total-records="roles.total"
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
        <Column header="Tipe Role" field="type">
          <template #body="{ data }">
            <Badge severity="secondary" :value="data.type"></Badge>
          </template>
        </Column>
        <Column
          v-for="item in allColumns"
          :key="item.field"
          :field="item.field"
          :header="item.header"
          sortable
        />
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
      :header="isUpdated ? 'Edit Data' : 'Tambah Data Baru'"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-6">
        <div>
          <label class="block font-bold mb-3">Tipe Role</label>
          <Select
            v-if="!isUpdated"
            :options="[
              { label: 'Unit', value: 'unit' },
              { label: 'Tim', value: 'tim' },
              { label: 'Semua', value: 'all' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="form.type"
            placeholder="Pilih Tipe Role"
          />
          <Select
            v-if="isUpdated"
            :options="[
              { label: 'Unit', value: 'unit' },
              { label: 'Tim', value: 'tim' },
              { label: 'Semua', value: 'all' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="editedData.type"
            placeholder="Pilih Tipe Role"
          />
          <div v-if="page.props.errors.type" class="text-red-500 text-sm mt-2">
            {{ page.props.errors?.type }}
          </div>
        </div>
        <div v-if="form.type != 'all' && !isUpdated">
          <label class="block font-bold mb-3">Pegawai/Tim</label>
          <Select
            :options="userDrop"
            class="w-full"
            showClear
            filter
            option-label="label"
            option-value="value"
            v-model="form.to_role_id"
            placeholder="Pilih Pegawai/Tim"
          />
          <div v-if="page.props.errors.to_role_id" class="text-red-500 text-sm mt-2">
            {{ page.props.errors?.to_role_id }}
          </div>
        </div>
        <div v-if="isUpdated && editedData.type != 'all'">
          <label class="block font-bold mb-3">Pegawai/Tim</label>
          <Select
            :options="userUpdDrop"
            class="w-full"
            showClear
            filter
            option-label="label"
            option-value="value"
            v-model="editedData.to_role_id"
            placeholder="Pilih Pegawai/Tim"
          />
          <div v-if="page.props.errors.to_role_id" class="text-red-500 text-sm mt-2">
            {{ page.props.errors?.to_role_id }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-3">Aplikasi</label>
          <Select
            v-if="!isUpdated"
            :options="apps"
            class="w-full"
            showClear
            filter
            option-label="label"
            option-value="value"
            v-model="form.app_id"
            placeholder="Pilih Aplikasi"
          />
          <Select
            v-if="isUpdated"
            :options="apps"
            class="w-full"
            showClear
            filter
            option-label="label"
            option-value="value"
            v-model="editedData.app_id"
            placeholder="Pilih Aplikasi"
          />
          <div v-if="page.props.errors.app_id" class="text-red-500 text-sm mt-2">
            {{ page.props.errors?.app_id }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-3">Role</label>
          <Select
            v-if="!isUpdated"
            :options="[
              { label: 'Viewer', value: 'viewer' },
              { label: 'Operator', value: 'operator' },
              { label: 'Validator', value: 'validator' },
              { label: 'Administrator', value: 'admin' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="form.roles"
            placeholder="Pilih Role"
          />
          <Select
            v-if="isUpdated"
            :options="[
              { label: 'Viewer', value: 'viewer' },
              { label: 'Operator', value: 'operator' },
              { label: 'Validator', value: 'validator' },
              { label: 'Administrator', value: 'admin' },
            ]"
            class="w-full"
            showClear
            option-label="label"
            option-value="value"
            v-model="form.roles"
            placeholder="Pilih Role"
          />
          <div v-if="page.props.errors.roles" class="text-red-500 text-sm mt-2">
            {{ page.props.errors?.roles }}
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
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { useConfirm } from "primevue";
import { ref, watch } from "vue";

const page = usePage();
const searchField = ref(null);
const createDialog = ref(false);
const props = defineProps({
  roles: {
    type: Object,
  },
  apps: {
    type: Object,
  },
});
const form = useForm({
  _token: null,
  type: null,
  to_role_id: null,
  app_id: null,
  roles: "viewer",
});
const allColumns = [
  { field: "pengguna", header: "Pengguna" },
  { field: "app", header: "Aplikasi" },
  { field: "roles", header: "Role" },
];
const userDrop = ref([]);
watch(
  () => form.type,
  async (type) => {
    let data = null;
    if (type) {
      if (type == "unit") {
        const { data: unit } = await axios.get(route("man-management.fetch-pegawai"));
        data = unit;
      } else if (type == "tim") {
        const { data: tim } = await axios.get(route("man-management.fetch-tim"));
        data = tim;
      }
      userDrop.value = data;
    } else userDrop.value = [];
  }
);
//paginated and search
const currentPage = ref((props.roles.current_page - 1) * props.roles.per_page);
const paginated = ref(props?.roles?.per_page ?? 10);
const sortField = ref(null);
const sortOrder = ref(null);
const fetchData = (event = null) => {
  currentPage.value = event ? Math.floor(event.first / event.rows) + 1 : 1;
  paginated.value = event?.rows ?? paginated.value;
  router.get(
    route("man-management.role-management.index"),
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

//submit and update
const submit = async () => {
  try {
    const { data: tokens } = await axios.get(route("api.token.csrf"));
    if (!isUpdated.value) {
      form._token = tokens;
      form.post(route("man-management.role-management.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          createDialog.value = false;
          form.reset();
        },
      });
      return;
    }
    if (isUpdated.value) {
      router.patch(
        route("man-management.role-management.patch"),
        {
          _token: tokens,
          ...editedData.value,
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            createDialog.value = false;
            fetchData();
          },
        }
      );
    }
  } catch (error) {
    console.error(error);
  }
};
const isUpdated = ref(false);
const editedData = ref({});
const updateData = (data) => {
  isUpdated.value = true;
  createDialog.value = true;
  editedData.value = { ...data };
};
const userUpdDrop = ref([]);
watch(editedData, async () => {
  let data = null;
  if (editedData.value?.type == "unit") {
    const { data: unit } = await axios.get(route("man-management.fetch-pegawai"));
    data = unit;
  } else if (editedData.value?.type == "tim") {
    const { data: tim } = await axios.get(route("man-management.fetch-tim"));
    data = tim;
  }
  userUpdDrop.value = data;
});
watch(createDialog, () => {
  if (createDialog.value == false) {
    isUpdated.value = false;
    editedData.value = {};
  }
});
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
      router.delete(route("man-management.role-management.destroy", { id: data.id }), {
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
</script>

<style scoped></style>
