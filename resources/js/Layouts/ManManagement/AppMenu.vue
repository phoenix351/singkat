<script setup>
import { ref } from "vue";
import AppMenuItem from "./AppMenuItem.vue";

const menu = ref([
  {
    items: [
      {
        label: "Daftar Pegawai",
        icon: "pi-fw",
        material_icon: "groups",
        material: true,
        route: route("man-management.index"),
        isActive: "man-management.index",
      },
      {
        label: "Tim Kerja",
        icon: "pi-fw",
        material_icon: "diversity_2",
        material: true,
        route: route("man-management.tim-kerja.index"),
        isActive: "man-management.tim-kerja.index",
      },
      {
        label: "Keanggotaan Tim Kerja",
        icon: "pi-fw ",
        material_icon: "manage_accounts",
        material: true,
        route: route("man-management.anggota.index"),
        isActive: "man-management.anggota.index",
      },
      {
        label: "Daftar Aplikasi",
        icon: "pi-fw",
        material_icon: "app_registration",
        material: true,
        route: route("man-management.app-management.index"),
        isActive: "man-management.app-management.index",
      },
    ],
  },
]);
</script>

<template>
  <ul class="layout-menu">
    <app-menu-item v-for="item in menu" :item="item" />
  </ul>
</template>

<style scoped></style>
