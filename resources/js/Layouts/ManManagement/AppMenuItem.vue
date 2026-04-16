<script setup>
import { computed, ref } from "vue";
import { useLayout } from "./Composables/layout";
import { Link, router, usePage } from "@inertiajs/vue3";

const { layoutState, isDesktop } = useLayout();

const props = defineProps({
  item: {
    type: Object,
    default: () => ({}),
  },
  root: {
    type: Boolean,
    default: true,
  },
  parentPath: {
    type: String,
    default: null,
  },
  child: {
    type: Boolean,
    default: true,
  },
});

const fullPath = computed(() =>
  props.item.path
    ? props.parentPath
      ? props.parentPath + props.item.path
      : props.item.path
    : null
);
const page = usePage();

const itemClick = (event, item) => {
  if (item.disabled) {
    event.preventDefault();
    return;
  }
};
</script>

<template>
  <li :class="{ 'layout-root-menuitem': root }">
    <div v-if="root && item.visible !== false" class="layout-menuitem-root-text">
      {{ item.label }}
    </div>
    <Link
      :href="item.route"
      v-if="props.child && item.visible !== false"
      @click="itemClick($event, item)"
      :class="{ 'active-route': page.props.route == item.isActive }"
      tabindex="0"
    >
      <i :class="item.icon" class="layout-menuitem-icon" />
      <span class="layout-menuitem-text">{{ item.label }}</span>
      <i class="pi pi-fw pi-angle-down layout-submenu-toggler" v-if="item.items" />
    </Link>
    <Transition v-if="item.items && item.visible !== false" name="layout-submenu">
      <ul v-show="root ? true : isActive" class="layout-submenu">
        <app-menu-item
          v-for="child in item.items"
          :key="child.label + '_' + (child.to || child.path)"
          :item="child"
          :root="false"
          :parentPath="fullPath"
        />
      </ul>
    </Transition>
  </li>
</template>
<style lang="css" scoped></style>
