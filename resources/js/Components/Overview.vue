<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from "@/Components/ui/dialog";
import { Label } from "@/Components/ui/label";
import { Button } from "@/Components/ui/button";
import { ref, watch } from "vue";
import { computed } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import idLocale from '@fullcalendar/core/locales/id';
import { CalendarOptions } from '@fullcalendar/core';
import { format, parseISO } from "date-fns";
import { toZonedTime } from "date-fns-tz";
import { id } from "date-fns/locale";
import { useClipboard } from "@vueuse/core";
import { useToast } from "@/Components/ui/toast/use-toast";
import { Toaster } from "@/Components/ui/toast";

const { toast } = useToast();

const { text, isSupported, copy, copied } = useClipboard();

const props = defineProps({
    all_meetings: {
        type: Object,
        default: () => ({}),
    },
    meetings: Array as () => Meeting[],
});

interface Meeting {
    id: string;
    join_url: string;
    topic: string;
    start_time: string;
    duration: number;
    jumlah_peserta: number;
    bidang: string;
    co_host: string;
    user: {
        name: string;
    };
    // add other properties as needed
}

const dialogOpen = ref(false);
const selectedEvent = ref<Meeting | null>(null);

const calendarEvents = computed(() => {
    const now = new Date();
    return props.all_meetings.data.meetings.map((meeting: any) => {
        let eventColor = '';
        const start = new Date(meeting.start_time);
        const end = new Date(meeting.start_time);
        end.setMinutes(end.getMinutes() + meeting.duration); // add the duration to the start time

        if (start > now) {
            eventColor = '#3a95f0'; // upcoming
        } else if (end < now) {
            eventColor = '#f0be41'; // previous
        } else {
            eventColor = '#f03a3a'; // live
        }

        return {
            id: meeting.id,
            title: meeting.topic,
            start: meeting.start_time,
            extendedProps: meeting,
            eventColor: eventColor,
            borderColor: eventColor, // set the border color
            textColor: eventColor, // set the text color
        };
    });
});

const handleEventClick = (info: any) => {
    const meeting = findMeeting(info.event.id);
    selectedEvent.value = { ...info.event.extendedProps, ...meeting };
    // console.log(selectedEvent.value)  // Get the full meeting data
    dialogOpen.value = true;
}

const calendarOptions: CalendarOptions = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: idLocale,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek'
    },
    events: calendarEvents.value,
    eventDidMount: function (info) {
        info.el.setAttribute('title', info.event.title);
    },
    eventClick: handleEventClick,
    eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        meridiem: false
    },
    eventMouseEnter: function (info) {
        info.el.style.cursor = 'pointer';
    },
    eventMouseLeave: function (info) {
        info.el.style.cursor = '';
    }
};

const formatDate = (dateString: string) => {
    const date = parseISO(dateString);
    const timeZone = "Asia/Singapore";
    const zonedDate = toZonedTime(date, timeZone);
    return format(zonedDate, "d MMMM yyyy, hh:mm aa 'WITA'", {
        locale: id,
    });
};

const formatTime = (duration: number): number => {
    return duration / 60;
};

const findMeeting = (id: string): Meeting | undefined => {
    return props.meetings?.find(
        (meeting: any) => meeting.meeting_id === id.toString()
    );
};

watch(copied, (newValue) => {
    if (newValue) {
        toast({
            title: "Berhasil",
            description: "Berhasil menyalin tautan ke Clipboard",
        });
    }
});
</script>

<template>
    <Toaster />
    <FullCalendar class="h-[55vh] text-sm" :options="calendarOptions" />
    <Dialog v-model:open="dialogOpen">
        <DialogContent class="lg:max-w-[800px] sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Detail Meeting</DialogTitle>
                <!-- <DialogDescription>
                    Detail meeting yang telah Anda buat.
                </DialogDescription> -->
            </DialogHeader>
            <div class="grid gap-4" v-if="selectedEvent">
                <div class="flex">
                    <Label class="min-w-[150px]">Topik Meeting</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ selectedEvent.topic }}
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Waktu</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ formatDate(selectedEvent.start_time) }}
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Durasi</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ selectedEvent.duration }} Jam
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Jumlah Peserta</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ selectedEvent?.jumlah_peserta || '-' }}
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Fungsi/Bidang</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ selectedEvent?.bidang || '-' }}
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Diajukan oleh</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ selectedEvent?.user?.name || '-' }}
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Co-Host</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ selectedEvent?.co_host || '-' }}
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Link Join</Label>
                    <div class="text-sm font-md min-w-full flex items-center">
                        <p>
                            {{ selectedEvent.join_url }}
                        </p>
                        <button v-if="isSupported" @click="copy(selectedEvent.join_url)" class="focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" stroke="currentColor"
                                strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                class="h-5 w-5 text-muted-foreground">
                                <path d="M447.168,134.56c-0.535-1.288-1.318-2.459-2.304-3.445l-128-128c-2.003-1.988-4.709-3.107-7.531-3.115H138.667
			C132.776,0,128,4.776,128,10.667V64H74.667C68.776,64,64,68.776,64,74.667v426.667C64,507.224,68.776,512,74.667,512h298.667
			c5.891,0,10.667-4.776,10.667-10.667V448h53.333c5.891,0,10.667-4.776,10.667-10.667V138.667
			C447.997,137.256,447.714,135.86,447.168,134.56z M320,36.416L411.584,128H320V36.416z M362.667,490.667H85.333V85.333H128v352
			c0,5.891,4.776,10.667,10.667,10.667h224V490.667z M426.667,426.667H149.333V21.333h149.333v117.333
			c0,5.891,4.776,10.667,10.667,10.667h117.333V426.667z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
