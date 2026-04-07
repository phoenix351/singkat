<template>
  <Head title="Home" />
  <GeneralLayout>
    <div class="mb-2 flex flex-wrap items-center justify-between">
      <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
        Daftar Pegawai
      </div>
      <div class="flex items-center w-full md:w-full lg:w-auto">
        <button
          class="btn-manment bg-[#1d845b] text-[whitesmoke] mr-2 mb-2 lg:mb-0"
          title="Download"
        >
          <font-awesome-icon icon="fa-solid fa-circle-down" />
        </button>
        <button
          @click="uploadPegawai"
          class="btn-manment bg-[#2754ce] text-[whitesmoke] mb-2 lg:mb-0"
        >
          <font-awesome-icon icon="fa-solid fa-plus" /> Tambah Pegawai Baru
        </button>
      </div>
    </div>
  </GeneralLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import GeneralLayout from "@/Layouts/ManManagement/GeneralLayout.vue";

const uploadPegawai = async () => {
  const { data } = await axios.get(route("man-management.get-current-pegawai"));
  if (data) {
    for (const n of data) {
      const { data: check, status: status } = await axios.get(
        route("man-management.check-pegawai", n)
      );
      if (check?.message == "not found") {
        await axios
          .post(route("man-management.upload-pegawai"), {
            nip: n,
          })
          .then((response) => {
            console.log(response);
          });
        console.log(n);
      } else continue;
    }
  }
};
</script>

<style scoped></style>
