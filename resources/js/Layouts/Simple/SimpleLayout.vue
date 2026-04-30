<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300">
      <!-- Topbar -->
      <header
        class="sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 flex items-center justify-between px-4 lg:px-8"
      >
        <div class="flex items-center">
          <button
            @click="toggleSidebar"
            class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none mr-4"
          >
            <i class="pi pi-bars text-xl"></i>
          </button>
        </div>

        <div class="flex items-center space-x-4">
          <button class="text-gray-500 hover:text-blue-600 transition-colors">
            <i class="pi pi-bell text-xl relative">
              <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </i>
          </button>
          <div
            class="flex items-center gap-3 border-l border-gray-200 pl-4 cursor-pointer hover:bg-gray-50 p-1 rounded-lg transition-colors"
            @click="toggleProfileMenu"
          >
            <Avatar icon="pi pi-user" class="bg-blue-100 text-blue-600" shape="circle" />
            <div class="hidden md:block text-sm">
              <p class="font-semibold text-gray-700 leading-none">Admin Sulut</p>
              <p class="text-gray-500 text-xs mt-1">Superadmin</p>
            </div>
            <i class="pi pi-chevron-down text-xs text-gray-400"></i>
          </div>
          <Menu ref="profileMenu" :model="profileItems" :popup="true" />
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 lg:p-6">
        <slot />
      </main>

      <!-- Footer -->
      <footer
        class="bg-white py-4 px-6 text-center md:text-left flex flex-col md:flex-row justify-between items-center text-sm text-gray-500"
      >
        <div>
          &copy; {{ new Date().getFullYear() }} SulutWeb - Sistem Informasi Terpadu.
        </div>
        <div class="mt-2 md:mt-0 space-x-4">
          <a href="#" class="hover:text-blue-600 transition-colors">Bantuan</a>
          <a href="#" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a>
        </div>
      </footer>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div
      v-if="isSidebarOpen"
      @click="toggleSidebar"
      class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-sm transition-opacity"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import Menu from "primevue/menu";
import Avatar from "primevue/avatar";
import Sidebar from "./Sidebar.vue";

const isSidebarOpen = ref(false);
const profileMenu = ref();

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleProfileMenu = (event) => {
  profileMenu.value.toggle(event);
};

const profileItems = ref([
  {
    label: "Profil Saya",
    icon: "pi pi-user",
  },
  {
    separator: true,
  },
  {
    label: "Keluar",
    icon: "pi pi-sign-out",
    command: () => {
      // Implement logout logic here
      // window.location.href = '/logout';
    },
  },
]);
</script>

<style>
/* Customize PrimeVue Menu for Sidebar to make it dark themed seamlessly */
</style>
