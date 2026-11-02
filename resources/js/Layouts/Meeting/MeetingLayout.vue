<template>
  <div class="flex flex-col min-h-screen">
    <div class="border-b bg-white">
      <div class="flex h-16 items-center px-4">
        <MainNav class="mx-6" />
        <div class="ml-auto flex items-center space-x-4">
          <UserNav />
        </div>
      </div>
    </div>

    <main class="flex-1 space-y-4 p-8 pt-6">
      <slot />
    </main>

    <!-- Footer -->
    <footer
      class="border-t bg-white py-4 px-8 text-center md:text-left flex flex-col md:flex-row justify-between items-center text-sm text-gray-500"
    >
      <p class="text-xs text-slate-500 dark:text-slate-400">
        &copy; 2026 MeetSulut versi 2.0 &mdash;
        <a href="https://sulut.bps.go.id" class="font-bold"
          >BPS Provinsi Sulawesi Utara</a
        >
        dibuat oleh:
        <a href="https://github.com/rifqind" class="font-bold">AuraSphere</a>,
        didukung oleh:
        <span class="font-bold">Tim IPDS</span>
      </p>
    </footer>
  </div>
</template>

<script setup lang="ts">
import MainNav from "@/Components/MainNav.vue";
import UserNav from "@/Components/UserNav.vue";
</script>
