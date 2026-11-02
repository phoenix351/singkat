<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/Components/ui/dialog";
import { format, parseISO } from "date-fns";
import { toZonedTime } from "date-fns-tz";
import { id } from "date-fns/locale";
import { Label } from "@/Components/ui/label";
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { useClipboard } from "@vueuse/core";
import { useToast } from "@/Components/ui/toast/use-toast";
import Button from "./ui/button/Button.vue";

const { copy, copied } = useClipboard();
const { toast } = useToast();

const isPasswordVisible = ref(false);

watch(copied, (newValue) => {
  if (newValue) {
    toast({
      title: "Berhasil",
      description: "Berhasil menyalin tautan ke Clipboard",
    });
  }
});

const formatDate = (dateString: string) => {
  if (!dateString) return "-";
  try {
    const date = parseISO(dateString);
    const timeZone = "Asia/Singapore";
    const zonedDate = toZonedTime(date, timeZone);
    return format(zonedDate, "d MMMM yyyy, hh:mm aa 'WITA'", {
      locale: id,
    });
  } catch {
    return dateString;
  }
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

const formatMeetingId = (meetingId: string | number | undefined) => {
  if (!meetingId) return "-";
  const idString = String(meetingId);
  return idString.replace(/(\d{3})(\d{4})(\d{3,4})/, "$1 $2 $3");
};

const emits = defineEmits(["closeDialog", "update:open"]);

const props = defineProps({
  open: {
    type: Boolean,
    default: undefined,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  meeting: {
    type: Object,
    default: () => null,
  },
  invitation: {
    type: Object,
    default: () => null,
  },
});

const dialogOpen = ref(false);

watch(
  () => [props.open, props.meeting],
  ([newOpen, newMeeting]) => {
    if (newOpen !== undefined) {
      dialogOpen.value = Boolean(newOpen);
    } else {
      dialogOpen.value = newMeeting !== null && Object.keys(newMeeting || {}).length > 0;
    }
    if (!dialogOpen.value) {
      isPasswordVisible.value = false;
    }
  },
  { immediate: true, deep: true }
);

function handleOpenChange(val: boolean) {
  dialogOpen.value = val;
  emits("update:open", val);
  if (!val) {
    isPasswordVisible.value = false;
    emits("closeDialog");
    // Jika dibuka dari URL langsung /meeting/index/{id}, kembalikan URL ke /meeting/index
    if (props.open === undefined && props.meeting) {
      router.get(
        route("meeting.index"),
        {},
        {
          preserveState: true,
          preserveScroll: true,
          replace: true,
        }
      );
    }
  }
}

const zoomData = computed(() => props.meeting?.data || null);
const localData = computed(() => props.meeting?.local || props.meeting || null);

const topicText = computed(() => {
  return zoomData.value?.topic || localData.value?.topic || "-";
});

const meetingIdText = computed(() => {
  return formatMeetingId(zoomData.value?.id || localData.value?.meeting_id);
});

const canStart = computed(() => {
  if (!zoomData.value?.start_url && !localData.value?.meeting_id) return false;
  const lastDate = localData.value?.end_date || localData.value?.start_date;
  if (!lastDate) return Boolean(zoomData.value?.start_url);
  const now = new Date();
  const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
  return String(lastDate) >= todayStr && Boolean(zoomData.value?.start_url);
});
</script>

<template>
  <Dialog :open="dialogOpen" @update:open="handleOpenChange">
    <DialogContent class="lg:max-w-[680px] sm:max-w-[450px]">
      <DialogHeader>
        <DialogTitle>Detail Meeting</DialogTitle>
        <DialogDescription>
          Informasi lengkap pengajuan dan tautan Zoom meeting.
        </DialogDescription>
      </DialogHeader>

      <div v-if="meeting || loading" class="grid gap-3.5 py-2">
        <div class="grid grid-cols-3 items-start gap-4 border-b pb-2.5">
          <Label class="text-muted-foreground font-medium">Topik Meeting</Label>
          <p class="col-span-2 text-sm font-semibold">
            {{ topicText }}
          </p>
        </div>

        <div class="grid grid-cols-3 items-start gap-4 border-b pb-2.5">
          <Label class="text-muted-foreground font-medium">Waktu Pelaksanaan</Label>
          <div class="col-span-2 text-sm">
            <template v-if="zoomData">
              <p v-if="zoomData.occurrences && zoomData.occurrences.length > 0">
                {{ formatDate(zoomData.occurrences[0].start_time) }}
              </p>
              <p v-if="zoomData.occurrences && zoomData.occurrences.length > 0" class="text-muted-foreground text-xs mt-0.5">
                s.d.
                {{
                  formatTanggal(
                    zoomData.occurrences[zoomData.occurrences.length - 1].start_time
                  )
                }}
                ({{ zoomData.occurrences.length }} sesi)
              </p>
              <p v-else-if="zoomData.start_time">
                {{ formatDate(zoomData.start_time) }}
              </p>
            </template>
            <template v-else-if="localData">
              <p>
                {{ localData.start_date }}
                <span v-if="localData.end_date && localData.end_date !== localData.start_date">
                  s.d. {{ localData.end_date }}
                </span>
              </p>
              <p v-if="localData.time" class="text-muted-foreground text-xs mt-0.5">
                Pukul {{ localData.time }} {{ (localData.period || "").toUpperCase() }} WITA
              </p>
            </template>
          </div>
        </div>

        <div v-if="localData?.duration || zoomData?.duration" class="grid grid-cols-3 items-center gap-4 border-b pb-2.5">
          <Label class="text-muted-foreground font-medium">Durasi</Label>
          <p class="col-span-2 text-sm">
            {{ localData?.duration ? `${localData.duration} Jam` : `${(zoomData?.duration || 60) / 60} Jam` }}
          </p>
        </div>

        <div v-if="localData?.bidang" class="grid grid-cols-3 items-center gap-4 border-b pb-2.5">
          <Label class="text-muted-foreground font-medium">Fungsi / Bagian</Label>
          <p class="col-span-2 text-sm">{{ localData.bidang }}</p>
        </div>

        <div v-if="localData?.co_host" class="grid grid-cols-3 items-center gap-4 border-b pb-2.5">
          <Label class="text-muted-foreground font-medium">Petugas Co-Host</Label>
          <p class="col-span-2 text-sm">{{ localData.co_host }}</p>
        </div>

        <div v-if="localData?.jumlah_peserta" class="grid grid-cols-3 items-center gap-4 border-b pb-2.5">
          <Label class="text-muted-foreground font-medium">Jumlah Peserta</Label>
          <p class="col-span-2 text-sm">{{ localData.jumlah_peserta }} Orang</p>
        </div>

        <div class="grid grid-cols-3 items-center gap-4 border-b pb-2.5">
          <Label class="text-muted-foreground font-medium">Meeting ID</Label>
          <p class="col-span-2 text-sm font-mono font-medium">
            {{ meetingIdText }}
          </p>
        </div>

        <!-- Bagian Data Langsung dari Server Zoom -->
        <template v-if="loading">
          <div class="py-3 text-center text-xs text-muted-foreground animate-pulse">
            Memuat informasi tautan & passcode dari server Zoom...
          </div>
        </template>

        <template v-else-if="zoomData">
          <div class="grid grid-cols-3 items-center gap-4 border-b pb-2.5">
            <Label class="text-muted-foreground font-medium">Passcode</Label>
            <div class="col-span-2 flex items-center gap-2">
              <p class="text-sm font-mono">
                {{ isPasswordVisible ? zoomData.password : "••••••" }}
              </p>
              <button
                type="button"
                class="text-muted-foreground hover:text-foreground transition-colors"
                :title="isPasswordVisible ? 'Sembunyikan Passcode' : 'Tampilkan Passcode'"
                @click="isPasswordVisible = !isPasswordVisible"
              >
                <svg
                  v-if="isPasswordVisible"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                >
                  <path
                    d="M2.99902 3L20.999 21M9.8433 9.91364C9.32066 10.4536 8.99902 11.1892 8.99902 12C8.99902 13.6569 10.3422 15 11.999 15C12.8215 15 13.5667 14.669 14.1086 14.133M6.49902 6.64715C4.59972 7.90034 3.15305 9.78394 2.45703 12C3.73128 16.0571 7.52159 19 11.9992 19C13.9881 19 15.8414 18.4194 17.3988 17.4184M10.999 5.04939C11.328 5.01673 11.6617 5 11.9992 5C16.4769 5 20.2672 7.94291 21.5414 12C21.2607 12.894 20.8577 13.7338 20.3522 14.5"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  class="h-4 w-4"
                >
                  <path
                    d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-3 items-start gap-4">
            <Label class="text-muted-foreground font-medium">Invite Link</Label>
            <div class="col-span-2 text-sm break-all text-blue-600">
              <a
                :href="zoomData.join_url"
                target="_blank"
                rel="noopener noreferrer"
                class="hover:underline"
              >
                {{ zoomData.join_url }}
              </a>
            </div>
          </div>
        </template>

        <template v-else>
          <div class="rounded-md bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800">
            Tautan undangan dan passcode Zoom sudah tidak aktif di server Zoom (jadwal meeting mungkin sudah berlalu atau kedaluwarsa).
          </div>
        </template>
      </div>

      <DialogFooter class="flex sm:justify-between items-center gap-2 pt-2">
        <div class="flex flex-wrap items-center gap-2">
          <a
            v-if="canStart"
            :href="route('meeting.start', zoomData?.id || localData?.meeting_id)"
            target="_blank"
            rel="noopener noreferrer"
          >
            <Button
              type="button"
              class="bg-emerald-600 hover:bg-emerald-700 text-white"
            >
              Mulai Zoom (Host)
            </Button>
          </a>
          <Button
            v-if="zoomData?.join_url"
            type="button"
            variant="default"
            @click="copy(zoomData.join_url)"
          >
            Salin Tautan Undangan
          </Button>
        </div>
        <Button
          type="button"
          variant="outline"
          @click="handleOpenChange(false)"
        >
          Tutup
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
