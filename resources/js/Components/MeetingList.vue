<script setup lang="ts">
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/Components/ui/table";
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
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/Components/ui/dialog";
import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import ViewDialog from "@/Components/ViewDialog.vue";
import MeetingForm from "@/Components/MeetingForm.vue";
import { router } from "@inertiajs/vue3";
import { useToast } from "@/Components/ui/toast/use-toast";
import { Toaster } from "@/Components/ui/toast";
import axios from "axios";
import { computed, ref, watch } from "vue";

const props = defineProps({
  user_meetings: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const { toast } = useToast();

const localFilters = ref({
  topic: props.filters?.topic || "",
  start_date: props.filters?.start_date || "",
  end_date: props.filters?.end_date || "",
  time: props.filters?.time || "",
  duration: props.filters?.duration || "",
  jumlah_peserta: props.filters?.jumlah_peserta || "",
  bidang: props.filters?.bidang || "",
  co_host: props.filters?.co_host || "",
});

watch(
  () => props.filters,
  (newVal) => {
    if (newVal) {
      localFilters.value = {
        topic: newVal.topic || "",
        start_date: newVal.start_date || "",
        end_date: newVal.end_date || "",
        time: newVal.time || "",
        duration: newVal.duration || "",
        jumlah_peserta: newVal.jumlah_peserta || "",
        bidang: newVal.bidang || "",
        co_host: newVal.co_host || "",
      };
    }
  },
  { deep: true }
);

const isFiltered = computed(() => {
  return Object.values(localFilters.value).some(
    (val) => String(val || "").trim() !== ""
  );
});

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

function onFilterInput() {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchData(1);
  }, 400);
}

function fetchData(page: number = 1) {
  const queryFilters: Record<string, string> = {};
  for (const [key, val] of Object.entries(localFilters.value)) {
    if (val && String(val).trim() !== "") {
      queryFilters[key] = String(val).trim();
    }
  }

  router.get(
    route("meeting.index"),
    {
      page: page,
      filters: Object.keys(queryFilters).length > 0 ? queryFilters : undefined,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  );
}

function resetFilters() {
  localFilters.value = {
    topic: "",
    start_date: "",
    end_date: "",
    time: "",
    duration: "",
    jumlah_peserta: "",
    bidang: "",
    co_host: "",
  };
  if (searchTimeout) clearTimeout(searchTimeout);
  fetchData(1);
}

const meetingsList = computed(() => {
  if (Array.isArray(props.user_meetings)) {
    return props.user_meetings;
  }
  return props.user_meetings?.data || [];
});

const totalItems = computed(() => {
  if (props.user_meetings?.total !== undefined) {
    return Number(props.user_meetings.total);
  }
  return meetingsList.value.length;
});

const currentPage = computed(() => {
  return Number(props.user_meetings?.current_page || 1);
});

const itemsPerPage = computed(() => {
  return Number(props.user_meetings?.per_page || 10);
});

const fromIndex = computed(() => {
  if (
    props.user_meetings?.from !== undefined &&
    props.user_meetings?.from !== null
  ) {
    return Number(props.user_meetings.from);
  }
  return totalItems.value > 0 ? 1 : 0;
});

const toIndex = computed(() => {
  if (
    props.user_meetings?.to !== undefined &&
    props.user_meetings?.to !== null
  ) {
    return Number(props.user_meetings.to);
  }
  return totalItems.value;
});

// ========================
// CEK APAKAH MEETING RELEVAN UNTUK DI-START
// ========================
function getTodayStr(): string {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  const day = String(now.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
}

function canStartMeeting(meeting: any): boolean {
  if (!meeting) return false;
  const lastDate = meeting.end_date || meeting.start_date;
  if (!lastDate) return false;
  return String(lastDate) >= getTodayStr();
}

// ========================
// 1. STATE MODAL VIEW DETAIL
// ========================
const viewDialogOpen = ref(false);
const viewLoading = ref(false);
const selectedViewMeeting = ref<any>(null);

async function view_meeting(meeting: any) {
  selectedViewMeeting.value = { local: meeting };
  viewDialogOpen.value = true;
  viewLoading.value = true;
  try {
    const response = await axios.get(
      route("meeting.index", meeting.meeting_id),
      {
        headers: { Accept: "application/json" },
      }
    );
    if (response.data?.meeting) {
      selectedViewMeeting.value = {
        ...response.data.meeting,
        local: response.data.meeting.local || meeting,
      };
    }
  } catch {
    selectedViewMeeting.value = { status: false, local: meeting };
  } finally {
    viewLoading.value = false;
  }
}

// ========================
// 2. STATE MODAL EDIT MEETING
// ========================
const editDialogOpen = ref(false);
const editLoading = ref(false);
const selectedEditMeeting = ref<any>({});
const selectedEditZoom = ref<any>({});

async function edit_meeting(meeting: any) {
  selectedEditMeeting.value = { ...meeting };
  selectedEditZoom.value = {};
  editDialogOpen.value = true;
  editLoading.value = true;
  try {
    const response = await axios.get(
      route("meeting.update", meeting.meeting_id),
      {
        headers: { Accept: "application/json" },
      }
    );
    if (response.data?.meeting) {
      selectedEditMeeting.value = response.data.meeting;
    }
    if (response.data?.meeting_zoom) {
      selectedEditZoom.value = response.data.meeting_zoom;
    }
  } catch {
    // Tetap gunakan data lokal jika Zoom API tidak merespons
  } finally {
    editLoading.value = false;
  }
}

// ========================
// 3. STATE MODAL KONFIRMASI DELETE
// ========================
const deleteDialogOpen = ref(false);
const deleting = ref(false);
const meetingToDelete = ref<any>(null);

function promptDeleteMeeting(meeting: any) {
  meetingToDelete.value = meeting;
  deleteDialogOpen.value = true;
}

async function confirmDeleteMeeting() {
  if (!meetingToDelete.value) return;
  deleting.value = true;
  try {
    const { data } = await axios.get(route("api.token.csrf"));
    router.delete(route("meeting.destroy", meetingToDelete.value.meeting_id), {
      data: {
        _token: data,
      },
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        deleteDialogOpen.value = false;
        meetingToDelete.value = null;
      },
      onFinish: () => {
        deleting.value = false;
      },
    });
  } catch {
    deleting.value = false;
    toast({
      title: "Gagal",
      description: "Terjadi kesalahan saat menghapus meeting.",
      variant: "destructive",
    });
  }
}
</script>

<template>
  <Toaster />
  <div class="space-y-4">
    <div class="overflow-x-auto rounded-md border">
      <Table>
        <TableHeader>
          <!-- Baris Label Kolom -->
          <TableRow>
            <TableHead class="w-[60px] text-center">No</TableHead>
            <TableHead class="min-w-[180px]">Topik/Judul Meeting</TableHead>
            <TableHead class="min-w-[130px]">Tanggal Mulai</TableHead>
            <TableHead class="min-w-[130px]">Tanggal Selesai</TableHead>
            <TableHead class="min-w-[110px]">Waktu</TableHead>
            <TableHead class="min-w-[100px]">Durasi</TableHead>
            <TableHead class="min-w-[110px]">Jumlah Peserta</TableHead>
            <TableHead class="min-w-[140px]">Fungsi/Bagian</TableHead>
            <TableHead class="min-w-[140px]">Petugas Co-Host</TableHead>
            <TableHead class="w-[175px]">Aksi</TableHead>
          </TableRow>
          <!-- Baris Input Search Setiap Kolom (Server-Side) -->
          <TableRow class="bg-muted/40 hover:bg-muted/40">
            <TableHead
              class="p-1 text-center text-xs text-muted-foreground font-normal"
            >
            </TableHead>
            <TableHead class="p-1">
              <Input
                v-model="localFilters.topic"
                placeholder="Cari topik..."
                class="h-7 text-xs bg-background"
                @input="onFilterInput"
              />
            </TableHead>
            <TableHead class="p-1">
              <Input
                v-model="localFilters.start_date"
                placeholder="Cari tgl mulai..."
                class="h-7 text-xs bg-background"
                @input="onFilterInput"
              />
            </TableHead>
            <TableHead class="p-1">
              <Input
                v-model="localFilters.end_date"
                placeholder="Cari tgl selesai..."
                class="h-7 text-xs bg-background"
                @input="onFilterInput"
              />
            </TableHead>
            <TableHead class="p-1">
              <Input
                v-model="localFilters.time"
                placeholder="Cari waktu..."
                class="h-7 text-xs bg-background"
                @input="onFilterInput"
              />
            </TableHead>
            <TableHead class="p-1">
              <Input
                v-model="localFilters.duration"
                placeholder="Cari durasi..."
                class="h-7 text-xs bg-background"
                @input="onFilterInput"
              />
            </TableHead>
            <TableHead class="p-1">
              <Input
                v-model="localFilters.jumlah_peserta"
                placeholder="Cari peserta..."
                class="h-7 text-xs bg-background"
                @input="onFilterInput"
              />
            </TableHead>
            <TableHead class="p-1">
              <Input
                v-model="localFilters.bidang"
                placeholder="Cari bidang..."
                class="h-7 text-xs bg-background"
                @input="onFilterInput"
              />
            </TableHead>
            <TableHead class="p-1">
              <Input
                v-model="localFilters.co_host"
                placeholder="Cari co-host..."
                class="h-7 text-xs bg-background"
                @input="onFilterInput"
              />
            </TableHead>
            <TableHead class="p-1 text-left">
              <Button
                v-if="isFiltered"
                variant="ghost"
                size="sm"
                class="h-7 px-2 text-xs text-red-600 hover:text-red-700 hover:bg-red-50"
                @click="resetFilters"
                title="Reset pencarian"
              >
                Reset
              </Button>
            </TableHead>
          </TableRow>
        </TableHeader>

        <TableBody>
          <TableRow v-if="meetingsList.length === 0">
            <TableCell
              colspan="10"
              class="text-center py-6 text-muted-foreground text-sm"
            >
              {{
                isFiltered
                  ? "Tidak ada pengajuan yang sesuai filter pencarian."
                  : "Belum ada data pengajuan meeting."
              }}
            </TableCell>
          </TableRow>

          <TableRow
            v-for="(meeting, index) in meetingsList"
            :key="meeting.id || meeting.meeting_id || index"
          >
            <TableCell class="text-center font-medium">
              {{ fromIndex + index }}
            </TableCell>
            <TableCell>{{ meeting.topic }}</TableCell>
            <TableCell>{{ meeting.start_date }}</TableCell>
            <TableCell>{{
              meeting.end_date ? meeting.end_date : meeting.start_date
            }}</TableCell>
            <TableCell>{{
              meeting.time + " " + (meeting.period || "").toUpperCase()
            }}</TableCell>
            <TableCell>{{ meeting.duration }} Jam</TableCell>
            <TableCell>{{ meeting.jumlah_peserta }}</TableCell>
            <TableCell>{{ meeting.bidang }}</TableCell>
            <TableCell>{{ meeting.co_host }}</TableCell>
            <TableCell class="whitespace-nowrap">
              <div class="flex items-center justify-start gap-1.5">
                <button
                  type="button"
                  class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-input bg-background text-muted-foreground hover:bg-accent hover:text-foreground transition-colors"
                  title="Edit Meeting"
                  @click="edit_meeting(meeting)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-3.5 w-3.5"
                  >
                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                    <path d="m15 5 4 4" />
                  </svg>
                </button>
                <button
                  type="button"
                  class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-input bg-background text-muted-foreground hover:bg-accent hover:text-foreground transition-colors"
                  title="Detail Meeting"
                  @click="view_meeting(meeting)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-3.5 w-3.5"
                  >
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                </button>
                <button
                  type="button"
                  class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-input bg-background text-muted-foreground hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors"
                  title="Hapus Meeting"
                  @click="promptDeleteMeeting(meeting)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-3.5 w-3.5"
                  >
                    <path d="M3 6h18" />
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                    <line x1="10" x2="10" y1="11" y2="17" />
                    <line x1="14" x2="14" y1="11" y2="17" />
                  </svg>
                </button>
                <a
                  v-if="canStartMeeting(meeting)"
                  :href="route('meeting.start', meeting.meeting_id)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex h-7 items-center gap-1 rounded-md bg-emerald-600 px-2.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700 transition-colors"
                  title="Mulai Zoom Meeting sebagai Host"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="h-3.5 w-3.5"
                  >
                    <path
                      d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"
                    />
                  </svg>
                  Start
                </a>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <!-- Kontrol Pagination Backend -->
    <div
      class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2 px-1"
    >
      <div class="text-xs text-muted-foreground">
        Menampilkan
        <span class="font-medium">
          {{ fromIndex }}
        </span>
        -
        <span class="font-medium">
          {{ toIndex }}
        </span>
        dari
        <span class="font-medium">{{ totalItems }}</span> pengajuan
      </div>

      <Pagination
        v-if="totalItems > itemsPerPage"
        v-slot="{ page }"
        :total="totalItems"
        :items-per-page="itemsPerPage"
        :sibling-count="1"
        show-edges
        :default-page="currentPage"
        :page="currentPage"
        @update:page="fetchData($event)"
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

    <!-- 1. Modal View Detail Meeting -->
    <ViewDialog
      v-model:open="viewDialogOpen"
      :meeting="selectedViewMeeting"
      :loading="viewLoading"
    />

    <!-- 2. Modal Edit Meeting -->
    <Dialog v-model:open="editDialogOpen">
      <DialogContent
        class="sm:max-w-[720px] lg:max-w-[820px] max-h-[90vh] overflow-y-auto"
      >
        <DialogHeader>
          <DialogTitle>Edit Pengajuan Meeting</DialogTitle>
          <DialogDescription>
            Perbarui informasi jadwal dan detail pengajuan Zoom meeting Anda.
          </DialogDescription>
        </DialogHeader>
        <div class="py-2">
          <MeetingForm
            :is-modal="true"
            :meeting="selectedEditMeeting"
            :meeting_zoom="selectedEditZoom"
            @cancel="editDialogOpen = false"
            @success="editDialogOpen = false"
          />
        </div>
      </DialogContent>
    </Dialog>

    <!-- 3. Modal Konfirmasi Hapus Meeting -->
    <Dialog v-model:open="deleteDialogOpen">
      <DialogContent class="sm:max-w-[440px]">
        <DialogHeader>
          <div class="flex items-center gap-3">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="h-5 w-5"
              >
                <path d="M3 6h18" />
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                <line x1="10" x2="10" y1="11" y2="17" />
                <line x1="14" x2="14" y1="11" y2="17" />
              </svg>
            </div>
            <div>
              <DialogTitle>Hapus Pengajuan Meeting?</DialogTitle>
              <DialogDescription class="mt-1">
                Tindakan ini tidak dapat dibatalkan.
              </DialogDescription>
            </div>
          </div>
        </DialogHeader>

        <div
          v-if="meetingToDelete"
          class="my-2 rounded-md border bg-muted/40 p-3 text-xs space-y-1"
        >
          <p class="font-semibold text-sm text-foreground">
            {{ meetingToDelete.topic }}
          </p>
          <p class="text-muted-foreground">
            Tanggal: {{ meetingToDelete.start_date }}
            <span
              v-if="
                meetingToDelete.end_date &&
                meetingToDelete.end_date !== meetingToDelete.start_date
              "
            >
              s.d. {{ meetingToDelete.end_date }}
            </span>
          </p>
          <p class="text-muted-foreground">
            Waktu: {{ meetingToDelete.time }}
            {{ (meetingToDelete.period || "").toUpperCase() }} ({{
              meetingToDelete.duration
            }}
            Jam)
          </p>
        </div>

        <DialogFooter class="flex sm:justify-end gap-2 pt-2">
          <Button
            type="button"
            variant="outline"
            :disabled="deleting"
            @click="deleteDialogOpen = false"
          >
            Batal
          </Button>
          <Button
            type="button"
            variant="destructive"
            :disabled="deleting"
            @click="confirmDeleteMeeting"
          >
            {{ deleting ? "Menghapus..." : "Ya, Hapus Meeting" }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
