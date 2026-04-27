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
        <Column
          v-for="item in allColumns"
          :key="item.field"
          :field="item.field"
          :header="item.header"
          sortable
        />
      </DataTable>
    </div>
    <Dialog
      v-model:visible="createDialog"
      modal
      header="Tambah Data Baru"
      class="min-w-[30vw]"
    >
      <div class="flex flex-col gap-6">
        <div>
          <label class="block font-bold mb-3">Tipe Role</label>
          <Select
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
          <div v-if="page.props.errors.type" class="text-red-500 text-sm mt-2">
            {{ page.props.errors?.type }}
          </div>
        </div>
        <div v-if="form.type != 'all'">
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
        <div>
          <label class="block font-bold mb-3">Aplikasi</label>
          <Select
            :options="apps"
            class="w-full"
            showClear
            filter
            option-label="label"
            option-value="value"
            v-model="form.app_id"
            placeholder="Pilih Aplikasi"
          />
          <div v-if="page.props.errors.app_id" class="text-red-500 text-sm mt-2">
            {{ page.props.errors?.app_id }}
          </div>
        </div>
        <div>
          <label class="block font-bold mb-3">Role</label>
          <Select
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
  { field: "type", header: "Tipe Role" },
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
    form._token = tokens;
    form.post(route("man-management.role-management.store"), {
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
</script>

<style scoped></style>
