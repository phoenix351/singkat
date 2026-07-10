<template>
  <aside
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
    class="fixed inset-y-0 left-0 z-50 bg-slate-900 text-white transition-all duration-300 ease-in-out lg:static overflow-hidden flex flex-col whitespace-nowrap"
    :class="
      isEffectivelyOpen
        ? 'w-64 translate-x-0'
        : 'w-64 -translate-x-full lg:w-20 lg:translate-x-0'
    "
  >
    <div
      class="flex items-center justify-center h-16 bg-slate-950 flex-shrink-0 relative"
    >
      <span
        class="text-xl font-bold tracking-wider text-blue-400 transition-opacity duration-300"
        :class="isEffectivelyOpen ? 'opacity-100' : 'opacity-0'"
        >SIMPLE</span
      >
      <i
        class="pi pi-compass text-2xl text-blue-400 absolute transition-opacity duration-300"
        :class="isEffectivelyOpen ? 'opacity-0' : 'opacity-100'"
      ></i>
    </div>
    <nav
      class="py-4 overflow-y-auto flex-1"
      :class="isEffectivelyOpen ? 'px-4' : 'px-4 lg:px-3'"
    >
      <ul class="space-y-1">
        <li v-for="(item, index) in menuItems" :key="index">
          <template v-if="item.children">
            <button
              v-if="checkAvailability(item.role)"
              @click="toggleDropdown(index)"
              :title="!isEffectivelyOpen ? item.label : ''"
              class="w-full flex items-center py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-all group overflow-hidden"
              :class="
                isEffectivelyOpen ? 'px-4 justify-start' : 'px-4 lg:px-0 lg:justify-center'
              "
            >
              <i
                :class="[
                  item.icon,
                  'text-lg w-6 flex-shrink-0 group-hover:text-white transition-all text-center',
                  isEffectivelyOpen ? 'mr-3' : 'mr-3 lg:mr-0',
                ]"
              ></i>
              <span
                class="flex-1 text-left transition-opacity duration-300 whitespace-nowrap"
                :class="isEffectivelyOpen ? 'opacity-100 block' : 'opacity-0 lg:hidden'"
                >{{ item.label }}</span
              >
              <i
                class="pi pi-chevron-down text-xs transition-transform duration-300"
                :class="[
                  openDropdowns.has(index) ? 'rotate-180' : 'rotate-0',
                  isEffectivelyOpen ? 'opacity-100' : 'opacity-0 lg:hidden',
                ]"
              ></i>
            </button>
            <ul
              v-show="openDropdowns.has(index) && isEffectivelyOpen"
              class="mt-1 ml-3 pl-3 border-l border-slate-700 space-y-1"
            >
              <li
                v-for="(child, childIndex) in item.children"
                :key="childIndex"
              >
                <Link
                  v-if="child.route"
                  :href="route(child.route)"
                  class="flex items-center px-3 py-2 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-all group text-sm"
                  :class="
                    page.props.route == child.route
                      ? 'text-white bg-slate-800 font-bold'
                      : ''
                  "
                >
                  <i
                    :class="[
                      child.icon || 'pi pi-minus',
                      'text-sm w-5 mr-2 flex-shrink-0 text-center group-hover:text-white',
                    ]"
                  ></i>
                  <span class="whitespace-nowrap">{{ child.label }}</span>
                </Link>
                <span
                  v-else
                  class="flex items-center px-3 py-2 rounded-lg text-slate-500 text-sm cursor-not-allowed"
                >
                  <i
                    :class="[
                      child.icon || 'pi pi-minus',
                      'text-sm w-5 mr-2 flex-shrink-0 text-center',
                    ]"
                  ></i>
                  <span class="whitespace-nowrap">{{ child.label }}</span>
                </span>
              </li>
            </ul>
          </template>

          <Link
            v-else-if="checkAvailability(item.role)"
            :href="route(item.route)"
            :title="!isEffectivelyOpen ? item.label : ''"
            class="flex items-center py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-all group overflow-hidden"
            :class="[
              page.props.route == item.route ? 'text-white bg-slate-800' : '',
              isEffectivelyOpen ? 'px-4 justify-start' : 'px-4 lg:px-0 lg:justify-center',
            ]"
          >
            <i
              :class="[
                item.icon,
                'text-lg w-6 flex-shrink-0 group-hover:text-white transition-all text-center',
                isEffectivelyOpen ? 'mr-3' : 'mr-3 lg:mr-0',
              ]"
            ></i>
            <span
              class="transition-opacity duration-300 whitespace-nowrap"
              :class="[
                { 'font-bold': page.props.route == item.route },
                isEffectivelyOpen ? 'opacity-100 block' : 'opacity-0 lg:hidden',
              ]"
              >{{ item.label }}</span
            >
          </Link>
        </li>
      </ul>
    </nav>
  </aside>
</template>
<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});
const page = usePage();

const isHovered = ref(false);
const isEffectivelyOpen = computed(() => props.isOpen || isHovered.value);

const openDropdowns = ref(new Set());
const toggleDropdown = (index) => {
  if (openDropdowns.value.has(index)) {
    openDropdowns.value.delete(index);
  } else {
    openDropdowns.value.add(index);
  }
};

const menuItems = ref([
  {
    label: "Dashboard",
    icon: "pi pi-home",
    route: "simple.index",
  },
  {
    label: "Summary",
    icon: "pi pi-building-columns",
    route: "simple.summary",
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
  {
    label: "FAQ dan Panduan",
    icon: "pi pi-question-circle",
    route: "simple.faq.index",
  },
]);
const checkAvailability = (role) => {
  const currentRole = page.props.auth.role;
  const keanggotaan = page.props.auth.keanggotaan;
  if (currentRole == "admin") return true;
  if (!role) return true;
  if (role == "ketua") return role == keanggotaan;
  if (role == "validator") return role == currentRole;
  if (role == "operator") return role == currentRole;
};
</script>
