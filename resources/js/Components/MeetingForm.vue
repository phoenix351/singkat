<script setup lang="ts">
import { reactive, ref, watchEffect } from "vue";
import { router } from "@inertiajs/vue3";
import { Input } from "@/Components/ui/input";
import {
  CalendarDate,
  type DateValue,
  DateFormatter,
  getLocalTimeZone,
  today,
} from "@internationalized/date";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/Components/ui/select";
import { CalendarIcon } from "@radix-icons/vue";
import { Label } from "@/Components/ui/label";
import { RangeCalendar } from "@/Components/ui/range-calendar";
import { Button } from "@/Components/ui/button";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/Components/ui/popover";
import { cn } from "@/lib/utils";

const df = new DateFormatter("id-ID", {
  dateStyle: "long",
});

const placeholder = ref();

const value = ref({
  start: null,
  end: null,
}) as any;

const currentDate = ref(today(getLocalTimeZone())) as any;

const form = reactive({
  _token: null,
  topic: "",
  datepicker: {
    startDate: '',
    endDate: '',
  },
  start_date: "",
  end_date: "",
  time: "",
  period: "",
  duration: "",
  password: "",
  participant: "",
  host: "",
  bidang: "",
});

watchEffect(() => {
  form.start_date = value.value.start;
  form.end_date = value.value.end;
});

const validateNumericInput = (event: any) => {
  let value = event.target.value.replace(/[^0-9]/g, "");
  if (value < 1) value = 1;
  if (value > 100) value = 100;
  event.target.value = value;
};

function submit() {
  router.post(route('meeting.store'), form, {
    preserveState: false,
    preserveScroll: false,
  });
}
const formatter = ref({
  date: 'YYYY-MM-DD',
  month: 'MMM',
})
const recurring = ref(true)
const changeRecurring = () => {
  form.datepicker = { startDate: null, endDate: null }
  recurring.value = !recurring.value
}
</script>

<template>
  <form @submit.prevent="submit">
    <div class="grid w-full max-w-5xl items-center gap-1.5">
      <div class="flex items-center gap-6 flex-nowrap mb-5">
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
      <div class="flex items-center gap-6 flex-nowrap mb-5">
        <Label for="date" class="text-sm min-w-[150px]">Waktu</Label>
        <div class="flex flex-row mr-2 items-center min-w-[400px]">
          <vue-tailwind-datepicker
            v-if="!recurring"
            class="w-[300px] mr-2"
            :formatter="formatter"
            v-model="form.datepicker"
            as-single
            separator=" s.d "
          />
          <vue-tailwind-datepicker
            v-if="recurring"
            class="w-[300px] mr-2"
            :formatter="formatter"
            v-model="form.datepicker"
            separator=" s.d "
          />
          <div
            @click="changeRecurring"
            class="btn-fordone w-[100px] text-center bg-black text-white"
          >
            {{ recurring ? "OneTime" : "Recurring" }}
          </div>
        </div>
        <Select id="time" v-model="form.time" required>
          <SelectTrigger>
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
          <SelectTrigger>
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

      <div class="flex items-center gap-6 flex-nowrap mb-5">
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

      <div class="flex items-center gap-6 flex-nowrap mb-5">
        <Label for="password" class="text-sm min-w-[150px]">Password</Label>
        <Input
          id="password"
          v-model="form.password"
          required
          type="text"
          placeholder="Password"
          class="flex-grow"
          maxlength="10"
        />
      </div>

      <div class="flex items-center gap-6 flex-nowrap mb-5">
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

      <div class="flex items-center gap-6 flex-nowrap mb-5">
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

      <div class="flex items-center gap-6 flex-nowrap mb-5">
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
      <Button type="submit">Submit</Button>
    </div>
  </form>
</template>
