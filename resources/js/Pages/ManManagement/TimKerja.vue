<template>
  <Head title="Tim Kerja" />
  <AppLayout>
    <div class="card">
      <div class="mb-4 flex flex-wrap items-center justify-between">
        <div class="text-xl font-bold w-full md:w-full lg:w-auto mb-2 md:mb-2 lg:mb-0">
          Daftar Tim Kerja
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
          <Button severity="info" rounded class="mb-2 lg:mb-0">
            <i class="pi pi-plus"></i>
            Tambah Tim Baru
          </Button>
        </div>
      </div>
      <DataTable
        :value="tim_kerja.data"
        class="w-full"
        lazy
        paginator
        :rows="tim_kerja.per_page"
        :first="(tim_kerja.current_page - 1) * tim_kerja.per_page"
        :total-records="tim_kerja.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
      >
        <template #empty><div class="text-center">Tim Kerja tidak ada</div></template>
        <Column
          v-for="item in allColumns"
          :key="item.field"
          :field="item.field"
          :header="item.header"
          sortable
        />
      </DataTable>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/ManManagement/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  tim_kerja: {
    type: Object,
  },
});
const allColumns = [
  { field: "tahun", header: "Tahun" },
  { field: "label", header: "Tim Kerja" },
];
</script>

<style scoped></style>
