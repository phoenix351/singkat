<script setup lang="ts">
import { Avatar, AvatarFallback } from "@/lib/registry/new-york/ui/avatar";
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
import { format, parseISO } from "date-fns";
import { toZonedTime } from "date-fns-tz";
import { id } from "date-fns/locale";
import { computed, ref } from "vue";

const props = defineProps({
  meetings: {
    type: [Array, Object],
    default: () => [],
  },
});

const getUserInitials = (name: string) => {
  if (name) {
    const names = name.trim().split(/\s+/);
    let initials = names[0].charAt(0);
    if (names.length > 1) {
      initials += names[1].charAt(0);
    }
    return initials.toUpperCase();
  }
  return "?";
};

const formatTanggal = (dateString: string) => {
  if (!dateString) return "-";
  try {
    const date = parseISO(dateString);
    const timeZone = "Asia/Singapore";
    const zonedDate = toZonedTime(date, timeZone);
    return format(zonedDate, "d MMMM yyyy", { locale: id });
  } catch {
    return dateString;
  }
};

const currentPage = ref(1);
const itemsPerPage = ref(10);

const meetingsArray = computed(() => {
  const arr = Array.isArray(props.meetings)
    ? props.meetings
    : Object.values(props.meetings || {});
  return arr.slice(0, 20);
});

const paginatedMeetings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return meetingsArray.value.slice(start, end);
});
</script>

<template>
  <div class="space-y-4">
    <!-- Header Kolom -->
    <div class="flex items-center justify-between border-b pb-2.5 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
      <span>Pemohon & Topik Meeting</span>
      <span class="shrink-0 text-right">Tanggal Pengajuan</span>
    </div>

    <!-- Daftar Item -->
    <div class="divide-y">
      <div
        v-for="(meeting, index) in paginatedMeetings"
        :key="meeting.id || index"
        class="flex items-start justify-between gap-4 py-3 first:pt-1 last:pb-1"
      >
        <div class="flex items-start gap-3 min-w-0 flex-1">
          <Avatar class="h-9 w-9 shrink-0 items-center justify-center border bg-muted/40">
            <AvatarFallback class="text-xs font-semibold">
              {{ getUserInitials(meeting.user?.name) }}
            </AvatarFallback>
          </Avatar>
          <div class="space-y-1 min-w-0 flex-1">
            <p class="text-sm font-medium leading-tight text-foreground truncate">
              {{ meeting.user?.name || "-" }}
            </p>
            <p class="text-xs text-muted-foreground line-clamp-2 leading-relaxed" :title="meeting.topic">
              {{ meeting.topic }}
            </p>
          </div>
        </div>

        <div class="shrink-0 text-right">
          <span class="inline-flex items-center rounded-md bg-muted/60 px-2.5 py-1 text-xs font-medium text-foreground whitespace-nowrap">
            {{ formatTanggal(meeting.created_at) }}
          </span>
        </div>
      </div>

      <div v-if="paginatedMeetings.length === 0" class="py-6 text-center text-sm text-muted-foreground">
        Belum ada pengajuan terbaru.
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="meetingsArray.length > itemsPerPage" class="pt-2 flex justify-center">
      <Pagination
        v-slot="{ page }"
        :total="meetingsArray.length"
        :items-per-page="itemsPerPage"
        :sibling-count="1"
        show-edges
        :default-page="1"
        :page="currentPage"
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
                class="w-8 h-8 p-0 text-xs"
                :variant="item.value === page ? 'default' : 'outline'"
                size="sm"
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
