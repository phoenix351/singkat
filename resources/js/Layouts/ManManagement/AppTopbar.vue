<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppConfigurator from "../AppConfigurator.vue";
import { useLayout } from "./Composables/layout";

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();
const dropdown = ref(false);
const manmentLogout = async () => {
  const { data } = await axios.get(route("api.token.csrf"));
  router.visit(route("logout"), {
    method: "post",
    data: {
      _token: data,
    },
  });
};
const backToHome = () => {
  window.location.href = "/";
};
</script>

<template>
  <div class="layout-topbar">
    <div class="layout-topbar-logo-container">
      <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
        <i class="pi pi-bars"></i>
      </button>
      <a to="/" class="layout-topbar-logo">
        <img
          src="../../../../public/images/logo/Man-Management-Logo.png"
          alt="Logo"
          class="w-10 h-10 rounded-full shadow-lg opacity-80"
        />

        <span>ManMent</span>
      </a>
    </div>

    <div class="layout-topbar-actions">
      <div class="layout-config-menu">
        <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
          <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
        </button>
        <div class="relative">
          <button
            v-styleclass="{
              selector: '@next',
              enterFromClass: 'hidden',
              enterActiveClass: 'p-anchored-overlay-enter-active',
              leaveToClass: 'hidden',
              leaveActiveClass: 'p-anchored-overlay-leave-active',
              hideOnOutsideClick: true,
            }"
            type="button"
            class="layout-topbar-action layout-topbar-action-highlight"
          >
            <i class="pi pi-palette"></i>
          </button>
          <AppConfigurator />
        </div>
      </div>

      <button
        class="layout-topbar-menu-button layout-topbar-action"
        v-styleclass="{
          selector: '@next',
          enterFromClass: 'hidden',
          enterActiveClass: 'p-anchored-overlay-enter-active',
          leaveToClass: 'hidden',
          leaveActiveClass: 'p-anchored-overlay-leave-active',
          hideOnOutsideClick: true,
        }"
      >
        <i class="pi pi-ellipsis-v"></i>
      </button>

      <div class="layout-topbar-menu hidden lg:block">
        <div class="layout-topbar-menu-content">
          <div class="relative">
            <button
              @click="dropdown = !dropdown"
              type="button"
              class="layout-topbar-action"
            >
              <i class="pi pi-user"></i>
              <span>Profile</span>
            </button>

            <template v-if="dropdown">
              <div class="fixed inset-0 z-40" @click="dropdown = false"></div>
              <div
                @click.stop
                class="absolute config-panel p-2 z-50 right-0 mt-2 w-40 bg-surface-0 dark:bg-surface-900 border border-surface rounded-border origin-top shadow-[0px_3px_5px_rgba(0,0,0,0.02),0px_0px_2px_rgba(0,0,0,0.05),0px_1px_4px_rgba(0,0,0,0.08)]"
              >
                <div class="flex flex-col gap-2">
                  <span
                    @click.stop="backToHome"
                    class="text text-black font-semibold cursor-pointer px-2 py-1 rounded hover:bg-red-100 dark:hover:bg-red-800"
                    >Home</span
                  >
                  <span
                    @click.stop="manmentLogout"
                    class="text text-black font-semibold cursor-pointer px-2 py-1 rounded hover:bg-red-100 dark:hover:bg-red-800"
                    >Logout</span
                  >
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
