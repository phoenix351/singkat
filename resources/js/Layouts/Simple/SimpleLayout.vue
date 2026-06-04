<template>
  <div class="min-h-screen bg-gray-50 flex overflow-hidden">
    <!-- Sidebar -->
    <Sidebar :isOpen="isSidebarOpen" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300">
      <!-- Topbar -->
      <header
        class="sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 flex items-center justify-between px-4 lg:px-8"
      >
        <div class="flex items-center">
          <button
            @click="toggleSidebar"
            class="text-gray-500 hover:text-gray-700 focus:outline-none mr-4"
          >
            <i class="pi pi-bars text-xl"></i>
          </button>
        </div>

        <div class="flex items-center space-x-4">
          <!-- Notification Bell -->
          <div class="relative flex items-center">
            <button
              @click="toggleNotificationMenu"
              class="relative p-2 text-gray-500 hover:text-gray-700 transition-colors flex items-center justify-center focus:outline-none"
              title="Anda memiliki lembur yang belum diisi outputnya"
            >
              <OverlayBadge
                :value="page.props.pendingOutputCount"
                severity="danger"
                size="small"
              >
                <i class="pi pi-bell text-xl"></i>
              </OverlayBadge>
            </button>
            <Menu
              ref="notificationMenu"
              :model="notificationItems"
              :popup="true"
              class="w-75 text-sm"
            />
          </div>

          <div
            class="flex items-center gap-3 pl-4 cursor-pointer hover:bg-gray-50 p-1 rounded-lg transition-colors"
            @click="toggleProfileMenu"
          >
            <Avatar
              icon="pi pi-user"
              class="bg-blue-100 text-blue-600"
              shape="circle"
            />
            <div class="hidden md:block text-sm">
              <p class="font-semibold text-gray-700 leading-none">
                {{ page.props.auth.user.name }}
              </p>
              <p class="text-gray-500 text-xs mt-1">
                {{ page.props.auth.role }}
              </p>
            </div>
            <i class="pi pi-chevron-down text-xs text-gray-400"></i>
          </div>
          <Menu ref="profileMenu" :model="profileItems" :popup="true" />
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 lg:p-6">
        <Toast />
        <ConfirmDialog />
        <SpinnerBorder v-if="triggerSpinner" />
        <slot />
      </main>

      <!-- Footer -->
      <footer
        class="bg-white py-4 px-6 text-center md:text-left flex flex-col md:flex-row justify-between items-center text-sm text-gray-500"
      >
        <div>&copy; 2026 Simple v.1.1.0 - Sistem Pengajuan Lembur</div>
        <div class="mt-2 md:mt-0 space-x-4"></div>
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
import { ref, computed, watch, nextTick, onMounted } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Menu from "primevue/menu";
import Avatar from "primevue/avatar";
import OverlayBadge from "primevue/overlaybadge";
import Sidebar from "./Sidebar.vue";
import { useToast, Toast, ConfirmDialog } from "primevue";
import SpinnerBorder from "@/Components/ManManagement/SpinnerBorder.vue";
import { triggerSpinner } from "../ManManagement/Composables/axiosSetup";
import axios from "axios";

const props = defineProps({
  isOpen: {
    default: false,
    type: Boolean,
  },
});
const isSidebarOpen = ref(props.isOpen);
const profileMenu = ref();
const notificationMenu = ref();

onMounted(() => {
  if (window.innerWidth >= 1024) {
    isSidebarOpen.value = true;
  }
});

watch(
  () => props.isOpen,
  (open) => {
    isSidebarOpen.value = open;
  }
);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleProfileMenu = (event) => {
  profileMenu.value.toggle(event);
};

const toggleNotificationMenu = (event) => {
  notificationMenu.value.toggle(event);
};

const page = usePage();

const notificationItems = computed(() => {
  const items = page.props.pendingOutputs || [];
  if (items.length === 0) {
    return [{ label: "Tidak ada notifikasi", disabled: true }];
  }
  return items.map((item) => ({
    label: `Ada pengajuan lembur dari ${item.tim_kerja} terkait ${item.maksud_lembur}`,
    icon: "pi pi-file-edit",
    command: () => {
      router.visit(route("simple.my-lembur"));
    },
  }));
});

const profileItems = ref([
  {
    label: "Keluar",
    icon: "pi pi-sign-out",
    command: async () => {
      const { data } = await axios.get(route("api.token.csrf"));
      router.visit(route("logout"), {
        method: "post",
        data: {
          _token: data,
        },
      });
    },
  },
]);
const toast = useToast();
watch(
  () => page.props.flash,
  async (flash) => {
    await nextTick();
    if (flash?.success) {
      toast.add({
        severity: "success",
        summary: "Berhasil",
        detail: flash.success,
        life: 3000,
      });
    }
    if (flash?.error) {
      toast.add({
        severity: "error",
        summary: "Gagal",
        detail: flash.error,
        life: 3000,
      });
    }
  },
  { deep: true, immediate: true }
);
</script>

<style scoped>
:deep(.p-inputtext::placeholder) {
  @apply text-sm;
}
:deep(.p-inputtext) {
  @apply text-sm;
}
</style>
