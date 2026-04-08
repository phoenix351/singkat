<template>
  <Head title="Home" />
  <AppLayout>
    <div class="mb-2 flex flex-wrap items-center justify-between">
      <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
        Daftar Pengguna
      </div>
      <div class="flex items-center w-full md:w-full lg:w-auto">
        <Button
          icon="pi pi-download"
          rounded
          aria-label="Download"
          severity="success"
          class="mr-2 mb-2 lg:mb-0"
        />
        <Button severity="info" rounded class="mb-2 lg:mb-0">
          <i class="pi pi-plus"></i>
          Tambah Pengguna Baru
        </Button>
      </div>
    </div>
    <div class="card">
      <DataTable :value="pegawai" class="w-full" paginator :rows="10">
        <Column field="nip_lama" header="NIP Lama" sortable />
      </DataTable>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/ManManagement/AppLayout.vue";

const props = defineProps({
  pegawai: {
    type: Array,
  },
});
const uploadPegawai = async () => {
  const { data } = await axios.get(route("man-management.get-current-pegawai"));
  const { data: check } = await axios.get(route("man-management.check-pegawai"));
  const setCheck = new Set(check);
  const toUpload = data.filter((item) => !setCheck.has(item));
  if (toUpload.length > 0) {
    for (const n of toUpload) {
      await axios
        .post(route("man-management.upload-pegawai"), {
          nip: n,
        })
        .then((response) => {
          console.log(response);
        });
      console.log(n);
    }
  }
};
</script>

<style scoped></style>
