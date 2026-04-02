<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from "@/lib/registry/new-york/ui/avatar";
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev,
} from "@/Components/ui/pagination";
import { Button } from "@/Components/ui/button";
import { usePage } from "@inertiajs/vue3";
import { format, parseISO } from "date-fns";
import { toZonedTime } from "date-fns-tz";
import { id } from "date-fns/locale";
import { computed, ref } from "vue";

const props = defineProps({
  meetings: {
    type: Object,
    default: () => ({}),
  },
});

const $page = usePage();

const getUserInitials = (name: string) => {
  if (name) {
    const names = name.split(" ");
    let initials = names[0].charAt(0);
    if (names.length > 1) {
      initials += names[1].charAt(0);
    }
    return initials.toUpperCase();
  }
  return "";
};

const formatTanggal = (dateString: string) => {
  const date = parseISO(dateString);
  const timeZone = "Asia/Singapore";
  const zonedDate = toZonedTime(date, timeZone);
  return format(zonedDate, "d MMMM yyyy", { locale: id });
};

const currentPage = ref(1);
const itemsPerPage = ref(10);

const paginatedMeetings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return props.meetings.slice(start, end);
});
</script>

<template>
  <div class="space-y-8">
    <div v-for="meeting in paginatedMeetings" class="flex items-center">
      <Avatar class="h-9 w-9 items-center justify-center space-y-0 border">
        <AvatarFallback>{{ getUserInitials(meeting.user.name) }}</AvatarFallback>
      </Avatar>
      <div class="ml-4 space-y-1">
        <p class="text-sm font-medium leading-none">
          {{ meeting.user.name }}
        </p>
        <p class="text-sm text-muted-foreground">
          {{ meeting.topic }}
        </p>
      </div>
      <div class="text-sm ml-auto font-medium">
        {{ formatTanggal(meeting.created_at) }}
      </div>
    </div>
    <div class="ml-4">
      <Pagination
        v-if="meetings.length > 6"
        v-slot="{ page }"
        :total="meetings.length"
        :items-per-page="itemsPerPage"
        :sibling-count="1"
        show-edges
        :default-page="1"
        @update:page="currentPage = $event"
      >
        <PaginationList v-slot="{ items }" class="flex items-center gap-1">
          <PaginationFirst />
          <PaginationPrev />

          <template v-for="(item, index) in items">
            <PaginationListItem
              v-if="item.type === 'page'"
              :key="index"
              :value="item.value"
              as-child
            >
              <Button
                class="w-10 h-10 p-0"
                :variant="item.value === page ? 'default' : 'outline'"
              >
                {{ item.value }}
              </Button>
            </PaginationListItem>
            <PaginationEllipsis v-else :key="item.type" :index="index" />
          </template>

          <PaginationNext />
          <PaginationLast />
        </PaginationList>
      </Pagination>
    </div>
  </div>
</template>
