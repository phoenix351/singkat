<template>
  <aside
    class="fixed inset-y-0 left-0 z-50 bg-slate-900 text-white transition-all duration-300 ease-in-out lg:static overflow-hidden flex flex-col whitespace-nowrap"
    :class="
      isOpen
        ? 'w-64 translate-x-0'
        : 'w-64 -translate-x-full lg:w-20 lg:translate-x-0'
    "
  >
    <div
      class="flex items-center justify-center h-16 bg-slate-950 flex-shrink-0 relative"
    >
      <span
        class="text-xl font-bold tracking-wider text-blue-400 transition-opacity duration-300"
        :class="isOpen ? 'opacity-100' : 'opacity-0'"
        >SIMPLE</span
      >
      <i
        class="pi pi-compass text-2xl text-blue-400 absolute transition-opacity duration-300"
        :class="isOpen ? 'opacity-0' : 'opacity-100'"
      ></i>
    </div>
    <nav class="py-4" :class="isOpen ? 'px-4' : 'px-4 lg:px-3'">
      <ul class="space-y-1">
        <li v-for="(item, index) in menuItems" :key="index">
          <Link
            v-if="checkAvailability(item.role)"
            :href="route(item.route)"
            :title="!isOpen ? item.label : ''"
            class="flex items-center py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-all group overflow-hidden"
            :class="[
              page.props.route == item.route ? 'text-white bg-slate-800' : '',
              isOpen ? 'px-4 justify-start' : 'px-4 lg:px-0 lg:justify-center',
            ]"
          >
            <i
              :class="[
                item.icon,
                'text-lg w-6 flex-shrink-0 group-hover:text-white transition-all text-center',
                isOpen ? 'mr-3' : 'mr-3 lg:mr-0',
              ]"
            ></i>
            <span
              class="transition-opacity duration-300 whitespace-nowrap"
              :class="[
                { 'font-bold': page.props.route == item.route },
                isOpen ? 'opacity-100 block' : 'opacity-0 lg:hidden',
              ]"
              >{{ item.label }}</span
            >
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

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});
const page = usePage();
const menuItems = ref([
  {
    label: "Dashboard",
    icon: "pi pi-home",
    route: "simple.index",
  },
  {
    label: "MyLembur",
    icon: "pi pi-sync",
    route: "simple.my-lembur",
  },
  {
    label: "Pengajuan Lembur",
    icon: "pi pi-calendar-plus",
    route: "simple.lembur",
  },
  {
    label: "Laporan Lembur Tim",
    icon: "pi pi-file-word",
    route: "simple.laporan-lembur",
  },
  {
    role: "ketua",
    label: "Verifikasi Ketua Tim",
    icon: "pi pi-check-square",
    route: "simple.lembur.verify",
  },
  {
    role: "validator",
    label: "Verifikasi Kabag",
    icon: "pi pi-check-square",
    route: "simple.lembur.verify-kabag",
  },
  {
    role: "operator",
    label: "SPKL & Presensi",
    icon: "pi pi-file-pdf",
    route: "simple.spkl",
  },
]);
const checkAvailability = (role) => {
  const currentRole = page.props.auth.role;
  const keanggotaan = page.props.auth.keanggotaan;
  if (currentRole == "admin") return true;
  if (!role) return true;
  if (role == "ketua") return keanggotaan == role;
  if (role == "validator") return role == currentRole;
};
</script>
