<script setup lang="ts">
import { onMounted, reactive, ref, watch, watchEffect } from "vue";
import { router } from "@inertiajs/vue3";
import { Input } from "@/Components/ui/input";
import { DateFormatter, getLocalTimeZone, today } from "@internationalized/date";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/Components/ui/select";
import { Label } from "@/Components/ui/label";
import { Button } from "@/Components/ui/button";
import axios from "axios";

const props = defineProps({
  meeting: {
    type: Object,
    default: () => ({}),
    required: false,
  },
  meeting_zoom: {
    type: Object,
    default: () => ({}),
    required: false,
  },
  isModal: {
    type: Boolean,
    default: false,
  },
});

const emits = defineEmits(["success", "cancel"]);

const df = new DateFormatter("id-ID", {
  dateStyle: "long",
});

const placeholder = ref();

const value = ref({
  start: null,
  end: null,
}) as any;

const currentDate = ref(today(getLocalTimeZone())) as any;
const submitting = ref(false);

const form = reactive({
  _token: null as any,
  meeting_id: props?.meeting?.meeting_id || null,
  topic: props?.meeting?.topic || "",
  datepicker: {
    startDate: props?.meeting?.start_date || "",
    endDate: props?.meeting?.end_date || "",
  },
  start_date: "",
  end_date: "",
  time: props?.meeting?.time || "",
  period: props?.meeting?.period || "",
  duration: props?.meeting?.duration ? String(props.meeting.duration) : "",
  password: props?.meeting_zoom?.password || "",
  participant: props?.meeting?.jumlah_peserta ? String(props.meeting.jumlah_peserta) : "",
  host: props?.meeting?.co_host || "",
  bidang: props?.meeting?.bidang || "",
});

const recurring = ref(false);

// Sinkronkan ketika prop meeting berubah (misal saat membuka modal edit)
watch(
  () => props.meeting,
  (newMeeting) => {
    if (newMeeting && Object.keys(newMeeting).length > 0) {
      form.meeting_id = newMeeting.meeting_id || null;
      form.topic = newMeeting.topic || "";
      form.datepicker = {
        startDate: newMeeting.start_date || "",
        endDate: newMeeting.end_date || "",
      };
      form.time = newMeeting.time || "";
      form.period = (newMeeting.period || "").toLowerCase();
      form.duration = newMeeting.duration ? String(newMeeting.duration) : "";
      form.participant = newMeeting.jumlah_peserta ? String(newMeeting.jumlah_peserta) : "";
      form.host = newMeeting.co_host || "";
      form.bidang = newMeeting.bidang || "";
      recurring.value = Boolean(newMeeting.end_date);
    }
  },
  { immediate: true, deep: true }
);

watch(
  () => props.meeting_zoom,
  (newZoom) => {
    if (newZoom && newZoom.password !== undefined) {
      form.password = newZoom.password || "";
    }
  },
  { immediate: true, deep: true }
);

watchEffect(() => {
  form.start_date = value.value.start;
  form.end_date = value.value.end;
});

const validateNumericInput = (event: any) => {
  let val = event.target.value.replace(/[^0-9]/g, "");
  if (val < 1) val = 1;
  if (val > 100) val = 100;
  event.target.value = val;
};

async function submit() {
  submitting.value = true;
  try {
    const { data } = await axios.get(route("api.token.csrf"));
    form._token = data;
    if (form.meeting_id) {
      router.patch(route("meeting.update-meeting", { id: form.meeting_id }), form, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          emits("success");
        },
        onFinish: () => {
          submitting.value = false;
        },
      });
      return;
    }
    router.post(route("meeting.store"), form, {
      preserveState: false,
      preserveScroll: false,
      onFinish: () => {
        submitting.value = false;
      },
    });
  } catch {
    submitting.value = false;
  }
}

const formatter = ref({
  date: "YYYY-MM-DD",
  month: "MMM",
});

const changeRecurring = () => {
  form.datepicker = { startDate: null, endDate: null } as any;
  recurring.value = !recurring.value;
};

onMounted(() => {
  if (props?.meeting?.end_date) {
    recurring.value = true;
  } else {
    recurring.value = false;
  }
});
</script>

<template>
  <form @submit.prevent="submit">
    <div class="grid w-full max-w-5xl items-center gap-1.5">
      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 mb-4">
        <Label for="topic" class="text-sm min-w-[150px]">Topik Meeting</Label>
        <Input
          id="topic"
          v-model="form.topic"
          type="text"
          placeholder="Topik Meeting"
          class="flex-grow"
          required
        />
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 mb-4">
        <Label for="date" class="text-sm min-w-[150px]">Waktu</Label>
        <div class="flex flex-wrap items-center gap-2 flex-grow">
          <div class="flex flex-row items-center gap-2">
            <vue-tailwind-datepicker
              v-if="!recurring"
              class="w-[240px]"
              :formatter="formatter"
              v-model="form.datepicker"
              as-single
              separator=" s.d "
            />
            <vue-tailwind-datepicker
              v-if="recurring"
              class="w-[260px]"
              :formatter="formatter"
              v-model="form.datepicker"
              separator=" s.d "
            />
            <button
              type="button"
              @click="changeRecurring"
              class="px-3 py-2 rounded-md text-xs font-medium bg-black text-white hover:bg-black/80 transition-colors cursor-pointer"
            >
              {{ recurring ? "OneTime" : "Recurring" }}
            </button>
          </div>

          <div class="flex items-center gap-2">
            <Select id="time" v-model="form.time" required>
              <SelectTrigger class="w-[120px]">
                <SelectValue placeholder="Pilih Waktu" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="12:00"> 12:00 </SelectItem>
                  <SelectItem value="12:30"> 12:30 </SelectItem>
                  <SelectItem value="01:00"> 01:00 </SelectItem>
                  <SelectItem value="01:30"> 01:30 </SelectItem>
                  <SelectItem value="02:00"> 02:00 </SelectItem>
                  <SelectItem value="02:30"> 02:30 </SelectItem>
                  <SelectItem value="03:00"> 03:00 </SelectItem>
                  <SelectItem value="03:30"> 03:30 </SelectItem>
                  <SelectItem value="04:00"> 04:00 </SelectItem>
                  <SelectItem value="04:30"> 04:30 </SelectItem>
                  <SelectItem value="05:00"> 05:00 </SelectItem>
                  <SelectItem value="05:30"> 05:30 </SelectItem>
                  <SelectItem value="06:00"> 06:00 </SelectItem>
                  <SelectItem value="06:30"> 06:30 </SelectItem>
                  <SelectItem value="07:00"> 07:00 </SelectItem>
                  <SelectItem value="07:30"> 07:30 </SelectItem>
                  <SelectItem value="08:00"> 08:00 </SelectItem>
                  <SelectItem value="08:30"> 08:30 </SelectItem>
                  <SelectItem value="09:00"> 09:00 </SelectItem>
                  <SelectItem value="09:30"> 09:30 </SelectItem>
                  <SelectItem value="10:00"> 10:00 </SelectItem>
                  <SelectItem value="10:30"> 10:30 </SelectItem>
                  <SelectItem value="11:00"> 11:00 </SelectItem>
                  <SelectItem value="11:30"> 11:30 </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>

            <Select id="period" v-model="form.period" required>
              <SelectTrigger class="w-[90px]">
                <SelectValue placeholder="AM/PM" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="am"> AM </SelectItem>
                  <SelectItem value="pm"> PM </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 mb-4">
        <Label for="duration" class="text-sm min-w-[150px]">Durasi Meeting (Jam)</Label>
        <Input
          id="duration"
          v-model="form.duration"
          type="number"
          min="1"
          max="24"
          required
          placeholder="Durasi Meeting"
          pattern="[0-9]*"
          class="flex-grow"
          @input="validateNumericInput"
        />
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 mb-4">
        <Label for="password" class="text-sm min-w-[150px]">Password</Label>
        <Input
          id="password"
          v-model="form.password"
          :required="!form.meeting_id"
          type="text"
          placeholder="Password Zoom Meeting"
          class="flex-grow"
          maxlength="10"
        />
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 mb-4">
        <Label for="participant" class="text-sm min-w-[150px]">Jumlah Peserta</Label>
        <Input
          id="participant"
          v-model="form.participant"
          type="number"
          min="1"
          max="100"
          required
          placeholder="Jumlah Peserta"
          pattern="[0-9]*"
          class="flex-grow"
          @input="validateNumericInput"
        />
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 mb-4">
        <Label for="host" class="text-sm min-w-[150px]">Petugas Co-Host</Label>
        <Input
          id="host"
          v-model="form.host"
          type="text"
          required
          placeholder="Petugas Co-Host"
          class="flex-grow"
        />
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 mb-5">
        <Label for="bidang" class="text-sm min-w-[150px]">Fungsi/Bagian</Label>
        <Select id="bidang" v-model="form.bidang" required>
          <SelectTrigger>
            <SelectValue placeholder="Fungsi/Bagian" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem value="Umum"> Umum </SelectItem>
              <SelectItem value="Sosial"> Sosial </SelectItem>
              <SelectItem value="Produksi"> Produksi </SelectItem>
              <SelectItem value="Distribusi"> Distribusi </SelectItem>
              <SelectItem value="Nerwilis"> Nerwilis </SelectItem>
              <SelectItem value="IPDS"> IPDS </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <Button
          v-if="isModal"
          type="button"
          variant="outline"
          :disabled="submitting"
          @click="emits('cancel')"
        >
          Batal
        </Button>
        <Button type="submit" :disabled="submitting" :class="!isModal ? 'w-full' : ''">
          {{ submitting ? "Menyimpan..." : form.meeting_id ? "Simpan Perubahan" : "Submit" }}
        </Button>
      </div>
    </div>
  </form>
</template>
