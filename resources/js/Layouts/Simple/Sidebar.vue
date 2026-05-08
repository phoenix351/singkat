<template>
  <aside
    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
    :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
  >
    <div class="flex items-center justify-center h-16 bg-slate-950">
      <span class="text-xl font-bold tracking-wider text-blue-400">SIMPLE</span>
    </div>
    <nav class="p-4">
      <ul class="space-y-1">
        <li v-for="(item, index) in menuItems" :key="index">
          <Link
            v-if="item.route"
            :href="route(item.route)"
            class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors group"
            :class="{ 'text-white': page.props.route == item.route }"
          >
            <i
              :class="[item.icon, 'mr-3 text-lg w-6 group-hover:text-white']"
            ></i>
            <span :class="{ 'font-bold': page.props.route == item.route }">{{
              item.label
            }}</span>
          </Link>
          <!-- <a
            v-else
            class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors cursor-pointer group"
          >
            <i :class="[item.icon, 'mr-3 text-lg w-6 group-hover:text-white']"></i>
            <span class="font-medium">{{ item.label }}</span>
          </a> -->
        </li>
      </ul>
    </nav>
  </aside>
</template>
<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const isSidebarOpen = ref(false);
const page = usePage();
const menuItems = ref([
  {
    label: "Dashboard",
    icon: "pi pi-home",
    route: "simple.index",
  },
  {
    label: "Pengajuan Lembur",
    icon: "pi pi-calendar-plus",
    route: "simple.lembur",
  },
  {
    label: "Verifikasi Ketua Tim",
    icon: "pi pi-check-square",
    route: "simple.lembur.verify",
  },
  {
    label: "Kelola Pegawai",
    icon: "pi pi-users",
    // route: 'simple.pegawai' // Akan dibuat nanti
  },
  {
    label: "Pengaturan",
    icon: "pi pi-cog",
    // route: 'simple.pengaturan' // Akan dibuat nanti
  },
]);
</script>
<style scoped>
.active-route {
}
</style>
