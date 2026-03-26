<script setup lang="ts">
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
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/Components/ui/tooltip";
import { Button } from "@/Components/ui/button";
import { Separator } from "@/Components/ui/separator";
import moment from "moment";
import { ref } from "vue";
import { computed } from "vue";

const props = defineProps({
    previous_meetings: {
        type: Object,
        default: () => ({}),
    },
    paginatedMeetings: {
        type: Array,
        default: () => [],
    },
    meetings: {
        type: Array,
        default: () => [],
    },
});

const formatDate = (date: string): string => {
    return moment(date).format("DD-MM-YYYY, h:mm a");
};
const formatTime = (duration: number): number => {
    return duration / 60;
};
const currentPage = ref(1);
const itemsPerPage = ref(10);

const paginatedMeetings = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return props.previous_meetings.data.meetings.slice(start, end);
});

const findMeeting = (id: string) => {
    return props.meetings.find(
        (meeting: any) => meeting.meeting_id === id.toString()
    );
};
</script>

<template>
    <div class="space-y-8">
        <div v-for="meeting in paginatedMeetings" class="flex items-center">
            <div class="ml-4 space-y-1">
                <p class="text-sm font-medium leading-none">
                    {{ meeting.topic }}
                </p>
                <div class="flex h-5 items-center space-x-4 text-sm">
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Waktu : {{ formatDate(meeting.start_time) }}
                        </p>
                    </div>
                    <Separator orientation="vertical" />
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Durasi :
                            {{ formatTime(meeting.duration) }}
                            Jam
                        </p>
                    </div>
                    <Separator
                        v-if="findMeeting(meeting.id)"
                        orientation="vertical"
                    />
                    <div v-if="findMeeting(meeting.id)">
                        <p class="text-sm text-muted-foreground">
                            Jumlah Peserta :
                            {{
                                (findMeeting(meeting.id) as any).jumlah_peserta
                            }}
                        </p>
                    </div>
                    <Separator
                        v-if="findMeeting(meeting.id)"
                        orientation="vertical"
                    />
                    <div v-if="findMeeting(meeting.id)">
                        <p
                            v-if="findMeeting(meeting.id)"
                            class="text-sm text-muted-foreground"
                        >
                            Fungsi/Bagian :
                            {{ (findMeeting(meeting.id) as any).bidang }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="ml-auto text-sm font-medium">
                <div class="text-blue-500">
                    <TooltipProvider>
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="5"
                                    class="h-5 w-5 text-blue-500"
                                >
                                    <path
                                        d="M10.0464 14C8.54044 12.4882 8.67609 9.90087 10.3494 8.22108L15.197 3.35462C16.8703 1.67483 19.4476 1.53865 20.9536 3.05046C22.4596 4.56228 22.3239 7.14956 20.6506 8.82935L18.2268 11.2626"
                                        stroke="#1C274C"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M13.9536 10C15.4596 11.5118 15.3239 14.0991 13.6506 15.7789L11.2268 18.2121L8.80299 20.6454C7.12969 22.3252 4.55237 22.4613 3.0464 20.9495C1.54043 19.4377 1.67609 16.8504 3.34939 15.1706L5.77323 12.7373"
                                        stroke="#1C274C"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>Meeting sudah berakhir</p>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </div>
            </div>
        </div>
        <div class="ml-4">
            <Pagination
                v-if="previous_meetings.data.meetings.length > 6"
                v-slot="{ page }"
                :total="previous_meetings.data.meetings.length"
                :items-per-page="itemsPerPage"
                :sibling-count="1"
                show-edges
                :default-page="1"
                @update:page="currentPage = $event"
            >
                <PaginationList
                    v-slot="{ items }"
                    class="flex items-center gap-1"
                >
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
                                :variant="
                                    item.value === page ? 'default' : 'outline'
                                "
                            >
                                {{ item.value }}
                            </Button>
                        </PaginationListItem>
                        <PaginationEllipsis
                            v-else
                            :key="item.type"
                            :index="index"
                        />
                    </template>

                    <PaginationNext />
                    <PaginationLast />
                </PaginationList>
            </Pagination>
        </div>
    </div>
</template>
