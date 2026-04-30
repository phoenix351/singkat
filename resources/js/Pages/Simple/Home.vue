<template>
  <Head title="Dashboard" />

  <SimpleLayout title="Dashboard">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <!-- Widget 1 -->
      <div
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-blue-50 text-blue-600 mr-4">
          <i class="pi pi-file-edit text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">Pengajuan Hari Ini</h3>
          <p class="text-2xl font-bold text-gray-800">12</p>
        </div>
      </div>

      <!-- Widget 2 -->
      <div
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-green-50 text-green-600 mr-4">
          <i class="pi pi-check-circle text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">Disetujui</h3>
          <p class="text-2xl font-bold text-gray-800">8</p>
        </div>
      </div>

      <!-- Widget 3 -->
      <div
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-orange-50 text-orange-600 mr-4">
          <i class="pi pi-clock text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">Menunggu Verifikasi</h3>
          <p class="text-2xl font-bold text-gray-800">4</p>
        </div>
      </div>

      <!-- Widget 4 -->
      <div
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md"
      >
        <div class="p-4 rounded-full bg-red-50 text-red-600 mr-4">
          <i class="pi pi-times-circle text-2xl"></i>
        </div>
        <div>
          <h3 class="text-gray-500 text-sm font-medium">Ditolak</h3>
          <p class="text-2xl font-bold text-gray-800">0</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h2>
      <div class="text-center text-gray-500 py-8">
        <i class="pi pi-inbox text-4xl mb-3 text-gray-300"></i>
        <p>Belum ada data aktivitas lembur.</p>
      </div>
    </div>
  </SimpleLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import SimpleLayout from "../../Layouts/Simple/SimpleLayout.vue";
</script>
