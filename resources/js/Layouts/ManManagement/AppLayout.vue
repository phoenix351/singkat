<script setup>
import { computed } from "vue";
import { useLayout } from "./Composables/layout";
import AppTopbar from "./AppTopbar.vue";
import AppSidebar from "./AppSidebar.vue";
import AppFooter from "./AppFooter.vue";

const { layoutConfig, layoutState, hideMobileMenu } = useLayout();

const containerClass = computed(() => {
  return {
    "layout-overlay": layoutConfig.menuMode === "overlay",
    "layout-static": layoutConfig.menuMode === "static",
    "layout-overlay-active": layoutState.overlayMenuActive,
    "layout-mobile-active": layoutState.mobileMenuActive,
    "layout-static-inactive": layoutState.staticMenuInactive,
  };
});
</script>

<template>
  <div class="layout-wrapper" :class="containerClass">
    <AppTopbar />
    <AppSidebar />
    <div class="layout-main-container">
      <div class="layout-main">
        <slot />
      </div>
      <AppFooter />
    </div>
    <div class="layout-mask animate-fadein" @click="hideMobileMenu" />
  </div>
</template>
<style></style>
