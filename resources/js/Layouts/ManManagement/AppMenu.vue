<script setup>
import { ref } from "vue";
import AppMenuItem from "./AppMenuItem.vue";

const menu = ref([
  {
    items: [
      {
        label: "Daftar Pegawai",
        icon: "pi pi-fw pi-users",
        route: route("man-management.index"),
        isActive: "man-management.index",
      },
      {
        label: "Tim Kerja",
        icon: "pi pi-fw pi-slack",
        route: route("man-management.tim-kerja.index"),
        isActive: "man-management.tim-kerja.index",
      },
      {
        label: "Keanggotaan Tim Kerja",
        icon: "pi pi-fw pi-list",
        route: route("man-management.anggota.index"),
        isActive: "man-management.anggota.index",
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
