<script setup>
import { computed, nextTick, onMounted, watch } from "vue";
import { ConfirmDialog, Toast, useToast } from "primevue";
import { useLayout } from "./Composables/layout";
import AppTopbar from "./AppTopbar.vue";
import AppSidebar from "./AppSidebar.vue";
import AppFooter from "./AppFooter.vue";
import { usePage } from "@inertiajs/vue3";

const { layoutConfig, layoutState, hideMobileMenu, initTheme } = useLayout();

const containerClass = computed(() => {
  return {
    "layout-overlay": layoutConfig.menuMode === "overlay",
    "layout-static": layoutConfig.menuMode === "static",
    "layout-overlay-active": layoutState.overlayMenuActive,
    "layout-mobile-active": layoutState.mobileMenuActive,
    "layout-static-inactive": layoutState.staticMenuInactive,
  };
});
onMounted(() => {
  initTheme();
});
const page = usePage();
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

<template>
  <div class="layout-wrapper" :class="containerClass">
    <AppTopbar />
    <AppSidebar />
    <div class="layout-main-container">
      <div class="layout-main">
        <Toast />
        <ConfirmDialog />
        <slot />
      </div>
      <AppFooter />
    </div>
    <div class="layout-mask animate-fadein" @click="hideMobileMenu" />
  </div>
</template>
<style></style>
