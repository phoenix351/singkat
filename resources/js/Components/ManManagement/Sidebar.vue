<template>
  <aside
    class="bg-white shadow-lg h-screen fixed top-0 left-0 w-[300px] z-[1200] overflow-y-auto"
  >
    <div class="px-6 py-4 flex items-center border-b h-[56px]">
      <img
        src="../../../../public/images/logo/Man-Management-Logo.png"
        alt="Logo"
        class="w-10 h-10 rounded-full shadow-lg opacity-80"
      />
      <span class="ml-6 font-bold text-xl">ManMent</span>
    </div>
    <nav class="mt-4 px-3">
      <ul></ul>
      <br />
      <div class="text-center">
        <font-awesome-icon
          @click="pushSidebar"
          data-widget="pushmenu"
          icon="fa-solid fa-circle-chevron-left"
          class="cursor-pointer"
        />
      </div>
    </nav>
  </aside>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const page = usePage();
const currentRoute = page.props.route;
const menuOpenLapus = ref(false);
const menuOpenPeng = ref(false);
const menuOpenFenom = ref(false);
const menuOpenSummary = ref(false);
const menuOpenSekunder = ref(false);

const toggleMenuOpen = (x) => {
  if (x == "summary") menuOpenSummary.value = !menuOpenSummary.value;
  if (x == "lapus") menuOpenLapus.value = !menuOpenLapus.value;
  if (x == "peng") menuOpenPeng.value = !menuOpenPeng.value;
  if (x == "fenom") menuOpenFenom.value = !menuOpenFenom.value;
  if (x == "sekunder") menuOpenSekunder.value = !menuOpenSekunder.value;
};

const emit = defineEmits(["update:updateSidebarValue"]);
const props = defineProps({
  isSidebarVisible: {
    type: Boolean,
    default: true,
    required: false,
  },
});
const pushSidebar = () => {
  emit("update:updateSidebarValue", !props.isSidebarVisible);
};
</script>

<style scoped>
.nav-link {
  padding: 0.5rem 1rem;
}
.maintenance {
  background-color: #a80606;
  color: whitesmoke;
}
</style>
