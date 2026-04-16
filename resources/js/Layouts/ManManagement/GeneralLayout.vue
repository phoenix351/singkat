<template>
  <div class="mt-14">
    <!-- Sidebar -->
    <Transition name="slide">
      <Sidebar
        v-if="isSidebarVisible"
        @update:updateSidebarValue="showSidebar"
        :isSidebarVisible="isSidebarVisible"
        class="transition-transform duration-300"
      />
    </Transition>
    <!-- Content Wrapper -->
    <div class="flex-grow" :class="mobileChecked">
      <!-- Navbar -->
      <Navbar
        :isSidebarVisible="isSidebarVisible"
        @update:triggerSidebar="triggerSidebar"
      />
      <!-- Main Content -->
      <main class="bg-gray-100 p-4">
        <slot />
      </main>
      <Footer />
    </div>
  </div>
</template>

<script setup>
import Footer from "@/Components/ManManagement/Footer.vue";
import Navbar from "@/Components/ManManagement/Navbar.vue";
import Sidebar from "@/Components/ManManagement/Sidebar.vue";
import { computed, onUnmounted, ref } from "vue";
const props = defineProps({
  entri: {
    type: Boolean,
    default: true,
    required: false,
  },
});
const isSidebarVisible = ref(true);
isSidebarVisible.value = props.entri;
const triggerSidebar = (value) => {
  isSidebarVisible.value = value;
};
const isMobile = ref(false);
const isTablet = ref(false);
const updateDeviceType = () => {
  isMobile.value = window.matchMedia("(max-width: 767px)").matches;
  isTablet.value = window.matchMedia(
    "(min-width: 768px) and (max-width: 1024px)"
  ).matches;
};

onUnmounted(() => {
  window.removeEventListener("resize", updateDeviceType);
});
const mobileChecked = computed(() => {
  if (isMobile.value) {
    return "ml-0";
  } else {
    return isSidebarVisible.value ? "ml-[300px]" : "ml-0";
  }
});
const showSidebar = (value) => {
  isSidebarVisible.value = value;
};
</script>

<style>
body {
  font-family: "Poppins", sans-serif !important;
}
main {
  min-height: calc(100vh - 56px - 56px);
}
.btn-manment {
  padding: 6px 12px 6px 12px;
  cursor: pointer;
  font-size: 16px;
  border-radius: 0.25rem;
}
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
  will-change: transform;
}

.slide-enter-from {
  transform: translateX(-100%);
}

.slide-enter-to {
  transform: translateX(0);
}

.slide-leave-from {
  transform: translateX(0);
}

.slide-leave-to {
  transform: translateX(-100%);
}
</style>
