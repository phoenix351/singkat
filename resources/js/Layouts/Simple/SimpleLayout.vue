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
              :title="
                totalNotificationCount > 0
                  ? `Ada ${totalNotificationCount} notifikasi yang perlu perhatian`
                  : 'Tidak ada notifikasi'
              "
            >
              <OverlayBadge
                :value="totalNotificationCount || null"
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
        <p class="text-xs text-slate-500 dark:text-slate-400">
          &copy; 2026 Simple versi 1.0.1-rc &mdash;
          <a href="https://sulut.bps.go.id" class="font-bold"
            >BPS Provinsi Sulawesi Utara</a
          >
          dibuat oleh:
          <a href="https://github.com/rifqind" class="font-bold">AuraSphere</a>,
          didukung oleh:
          <span class="font-bold">Tim SDM</span>
        </p>
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

const totalNotificationCount = computed(() => {
  return (
    (page.props.pendingOutputCount || 0) +
    (page.props.lemburPending || 0) +
    (page.props.lemburToVerify || 0)
  );
});

const notificationItems = computed(() => {
  const outputItems = page.props.pendingOutputs || [];
  const pendingDetail = page.props.lemburPendingDetail || [];
  const verifyDetail = page.props.lemburToVerifyDetail || [];

  const items = [];

  // --- Notifikasi Output (semua pegawai) ---
  if (outputItems.length > 0) {
    items.push({
      label: "Output Lembur Saya",
      disabled: true,
      class: "text-xs text-gray-400 font-semibold uppercase tracking-wider",
    });
    outputItems.forEach((item) => {
      items.push({
        label: `Ada pengajuan lembur dari ${item.tim_kerja} terkait ${item.maksud_lembur}`,
        icon: "pi pi-file-edit",
        command: () => router.visit(route("simple.my-lembur")),
      });
    });
  }

  // --- Notifikasi Ketua Tim ---
  if (pendingDetail.length > 0) {
    if (items.length > 0) items.push({ separator: true });
    items.push({
      label: "Verifikasi Ketua Tim",
      disabled: true,
      class: "text-xs text-gray-400 font-semibold uppercase tracking-wider",
    });
    pendingDetail.forEach((item) => {
      items.push({
        label: `Ada ${item.jumlah} lembur dari ${item.tim_kerja} yang perlu diperiksa`,
        icon: "pi pi-check-square",
        command: () => router.visit(route("simple.lembur.verify")),
      });
    });
  }

  // --- Notifikasi Kabag ---
  if (verifyDetail.length > 0) {
    if (items.length > 0) items.push({ separator: true });
    items.push({
      label: "Verifikasi Kabag",
      disabled: true,
      class: "text-xs text-gray-400 font-semibold uppercase tracking-wider",
    });
    verifyDetail.forEach((item) => {
      items.push({
        label: `Ada ${item.jumlah} lembur dari ${item.tim_kerja} yang perlu diverifikasi`,
        icon: "pi pi-shield",
        command: () => router.visit(route("simple.lembur.verify-kabag")),
      });
    });
  }

  if (items.length === 0) {
    return [{ label: "Tidak ada notifikasi", disabled: true }];
  }

  return items;
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
