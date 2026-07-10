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
          <div class="relative flex items-center" ref="notifContainer">
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

            <!-- Custom Notification Popover -->
            <Transition name="notif-fade">
              <div v-if="showNotificationPanel" class="notif-panel">
                <div class="notif-header">
                  <span class="notif-title">
                    <i class="pi pi-bell mr-2"></i>Notifikasi
                  </span>
                  <button
                    @click="showNotificationPanel = false"
                    class="notif-close"
                  >
                    <i class="pi pi-times"></i>
                  </button>
                </div>

                <div class="notif-body" v-if="hasAnyNotification">
                  <!-- Output Lembur -->
                  <template v-if="outputItems.length > 0">
                    <div class="notif-section-label">Output Lembur Saya</div>
                    <div
                      v-for="(item, i) in outputItems"
                      :key="'out-' + i"
                      class="notif-item"
                      @click="navigateTo('simple.my-lembur')"
                    >
                      <span class="notif-item-icon bg-blue-100 text-blue-600">
                        <i class="pi pi-file-edit"></i>
                      </span>
                      <div class="notif-item-text">
                        <p class="notif-item-title">Isi Output Lembur</p>
                        <p class="notif-item-sub">
                          {{ item.tim_kerja }} &mdash; {{ item.maksud_lembur }}
                        </p>
                      </div>
                    </div>
                  </template>

                  <!-- Ketua Tim -->
                  <template v-if="pendingDetail.length > 0">
                    <div class="notif-section-label">Verifikasi Ketua Tim</div>
                    <div
                      v-for="(item, i) in pendingDetail"
                      :key="'katim-' + i"
                      class="notif-item"
                      @click="navigateTo('simple.lembur.verify')"
                    >
                      <span class="notif-item-icon bg-amber-100 text-amber-600">
                        <i class="pi pi-check-square"></i>
                      </span>
                      <div class="notif-item-text">
                        <p class="notif-item-title">
                          {{ item.jumlah }} lembur perlu diperiksa
                          <span
                            v-if="item.is_lintas_tim"
                            class="notif-badge-lintas"
                            >Lintas Tim</span
                          >
                        </p>
                        <p class="notif-item-sub">
                          <template v-if="item.is_lintas_tim">
                            Dari tim lain &mdash; PJ:
                            {{ item.tim_pj ?? item.tim_kerja }}
                          </template>
                          <template v-else>
                            Tim: {{ item.tim_kerja }}
                          </template>
                        </p>
                      </div>
                    </div>
                  </template>

                  <!-- Kabag -->
                  <template v-if="verifyDetail.length > 0">
                    <div class="notif-section-label">Verifikasi Kabag</div>
                    <div
                      v-for="(item, i) in verifyDetail"
                      :key="'kabag-' + i"
                      class="notif-item"
                      @click="navigateTo('simple.lembur.verify-kabag')"
                    >
                      <span
                        class="notif-item-icon bg-purple-100 text-purple-600"
                      >
                        <i class="pi pi-shield"></i>
                      </span>
                      <div class="notif-item-text">
                        <p class="notif-item-title">
                          {{ item.jumlah }} lembur perlu diverifikasi
                        </p>
                        <p class="notif-item-sub">Tim: {{ item.tim_kerja }}</p>
                      </div>
                    </div>
                  </template>
                </div>

                <!-- Empty state -->
                <div v-else class="notif-empty">
                  <i class="pi pi-inbox text-3xl mb-2 text-gray-300"></i>
                  <p>Tidak ada notifikasi</p>
                </div>
              </div>
            </Transition>
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
          &copy; 2026 Simple versi 1.1 &mdash;
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
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
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
const showNotificationPanel = ref(false);
const notifContainer = ref(null);

onMounted(() => {
  if (window.innerWidth >= 1024) {
    isSidebarOpen.value = true;
  }

  // Tutup notifikasi jika klik di luar
  document.addEventListener("click", handleOutsideClick);
});

onUnmounted(() => {
  document.removeEventListener("click", handleOutsideClick);
});

const handleOutsideClick = (event) => {
  if (notifContainer.value && !notifContainer.value.contains(event.target)) {
    showNotificationPanel.value = false;
  }
};

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

const toggleNotificationMenu = () => {
  showNotificationPanel.value = !showNotificationPanel.value;
};

const page = usePage();

const outputItems = computed(() => page.props.pendingOutputs || []);
const pendingDetail = computed(() => page.props.lemburPendingDetail || []);
const verifyDetail = computed(() => page.props.lemburToVerifyDetail || []);

const hasAnyNotification = computed(
  () =>
    outputItems.value.length > 0 ||
    pendingDetail.value.length > 0 ||
    verifyDetail.value.length > 0
);

const navigateTo = (routeName) => {
  showNotificationPanel.value = false;
  router.visit(route(routeName));
};

const totalNotificationCount = computed(() => {
  return (
    (page.props.pendingOutputCount || 0) +
    (page.props.lemburPending || 0) +
    (page.props.lemburToVerify || 0)
  );
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

/* ── Notification Panel ── */
.notif-panel {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 340px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
  z-index: 9999;
  overflow: hidden;
}

.notif-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-bottom: 1px solid #f3f4f6;
  background: #f9fafb;
}

.notif-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  display: flex;
  align-items: center;
}

.notif-close {
  background: none;
  border: none;
  cursor: pointer;
  color: #9ca3af;
  font-size: 0.8rem;
  padding: 4px;
  border-radius: 6px;
  transition: background 0.15s, color 0.15s;
  line-height: 1;
}
.notif-close:hover {
  background: #e5e7eb;
  color: #374151;
}

.notif-body {
  max-height: 420px;
  overflow-y: auto;
  padding: 8px 0;
}

.notif-section-label {
  font-size: 0.65rem;
  font-weight: 700;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 8px 16px 4px;
}

.notif-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 10px 16px;
  cursor: pointer;
  transition: background 0.15s;
}
.notif-item:hover {
  background: #f9fafb;
}

.notif-item-icon {
  flex-shrink: 0;
  width: 34px;
  height: 34px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
}

.notif-item-text {
  flex: 1;
  min-width: 0;
}

.notif-item-title {
  font-size: 0.8rem;
  font-weight: 600;
  color: #1f2937;
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
  line-height: 1.4;
}

.notif-item-sub {
  font-size: 0.72rem;
  color: #6b7280;
  margin-top: 2px;
  line-height: 1.4;
}

.notif-badge-lintas {
  display: inline-block;
  font-size: 0.6rem;
  font-weight: 700;
  padding: 1px 6px;
  background: #fef3c7;
  color: #92400e;
  border-radius: 9999px;
  letter-spacing: 0.02em;
  white-space: nowrap;
}

.notif-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 36px 16px;
  color: #9ca3af;
  font-size: 0.8rem;
}

/* ── Transition ── */
.notif-fade-enter-active,
.notif-fade-leave-active {
  transition: opacity 0.18s ease, transform 0.18s ease;
}
.notif-fade-enter-from,
.notif-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px) scale(0.98);
}
</style>
