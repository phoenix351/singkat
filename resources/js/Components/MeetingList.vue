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
import { router } from "@inertiajs/vue3";
import { useToast } from "@/Components/ui/toast/use-toast";
import { Toaster } from "@/Components/ui/toast";

const props = defineProps({
    user_meetings: {
        type: Object,
        default: () => ({}),
    },
});

const { toast } = useToast();

function edit_meeting(id: number) {
    toast({
        title: "Pengumuman",
        description: "Mohon maaf, fitur ini masih dalam pengembangan.",
    });
}

function view_meeting(id: number) {
    router.get(`/pengajuan/` + id, {
        preserveState: true,
        preserveScroll: true,
    });
}

function delete_meeting(id: number) {
    router.delete(`/meeting/` + id, {
        onBefore: () =>
            confirm("Are you sure you want to delete this meeting?"),
        preserveState: false,
        preserveScroll: false,
    });
}
</script>

<template>
    <Toaster />
    <Table>
        <TableCaption>Daftar Pengajuan Meeting Anda.</TableCaption>
        <TableHeader>
            <TableRow>
                <TableHead class="w-[100px]">No</TableHead>
                <TableHead>Topik/Judul Meeting</TableHead>
                <TableHead>Tanggal Mulai</TableHead>
                <TableHead>Tanggal Selesai</TableHead>
                <TableHead>Waktu</TableHead>
                <TableHead>Durasi</TableHead>
                <TableHead>Jumlah Peserta</TableHead>
                <TableHead>Fungsi/Bagian</TableHead>
                <TableHead>Petugas Co-Host</TableHead>
                <TableHead class="text-center">Aksi</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="(meeting, index) in user_meetings" :key="index">
                <TableCell> {{ index + 1 }} </TableCell>
                <TableCell>{{ meeting.topic }}</TableCell>
                <TableCell>{{ meeting.start_date }}</TableCell>
                <TableCell>{{
                    meeting.end_date ? meeting.end_date : meeting.start_date
                }}</TableCell>
                <TableCell>{{
                    meeting.time + " " + meeting.period.toUpperCase()
                }}</TableCell>
                <TableCell>{{ meeting.duration }} Jam</TableCell>
                <TableCell>{{ meeting.jumlah_peserta }}</TableCell>
                <TableCell>{{ meeting.bidang }}</TableCell>
                <TableCell>{{ meeting.co_host }}</TableCell>
                <TableCell class="text-center">
                    <button
                        class="mr-2"
                        @click="edit_meeting(meeting.meeting_id)"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 300 300"
                            stroke="currentColor"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            class="h-4 w-4 text-muted-foreground"
                        >
                            <!-- <path fill="none" d="M0 0h24v24H0z" /> -->
                            <path
                                d="M12.809,238.52L0,306.637l68.118-12.809l184.277-184.277l-55.309-55.309L12.809,238.52z M60.79,279.943l-41.992,7.896
			l7.896-41.992L197.086,75.455l34.096,34.096L60.79,279.943z"
                            />
                            <path
                                d="M251.329,0l-41.507,41.507l55.308,55.308l41.507-41.507L251.329,0z M231.035,41.507l20.294-20.294l34.095,34.095
			L265.13,75.602L231.035,41.507z"
                            />
                        </svg>
                    </button>
                    <button
                        class="mr-2"
                        @click="view_meeting(meeting.meeting_id)"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 32 32"
                            stroke="currentColor"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            class="h-4 w-4 text-muted-foreground"
                        >
                            <!-- <path fill="none" d="M0 0h24v24H0z" /> -->
                            <path
                                d="M0 16q0.064 0.128 0.16 0.352t0.48 0.928 0.832 1.344 1.248 1.536 1.664 1.696 2.144 1.568 2.624 1.344 3.136 0.896 3.712 0.352 3.712-0.352 3.168-0.928 2.592-1.312 2.144-1.6 1.664-1.632 1.248-1.6 0.832-1.312 0.48-0.928l0.16-0.352q-0.032-0.128-0.16-0.352t-0.48-0.896-0.832-1.344-1.248-1.568-1.664-1.664-2.144-1.568-2.624-1.344-3.136-0.896-3.712-0.352-3.712 0.352-3.168 0.896-2.592 1.344-2.144 1.568-1.664 1.664-1.248 1.568-0.832 1.344-0.48 0.928zM10.016 16q0-2.464 1.728-4.224t4.256-1.76 4.256 1.76 1.76 4.224-1.76 4.256-4.256 1.76-4.256-1.76-1.728-4.256zM12 16q0 1.664 1.184 2.848t2.816 1.152 2.816-1.152 1.184-2.848-1.184-2.816-2.816-1.184-2.816 1.184l2.816 2.816h-4z"
                            ></path>
                        </svg>
                    </button>
                    <button @click="delete_meeting(meeting.meeting_id)">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            stroke="currentColor"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            class="h-4 w-4 text-muted-foreground"
                        >
                            <!-- <path fill="none" d="M0 0h24v24H0z" /> -->
                            <
                            <path
                                d="M3 6.52381C3 6.12932 3.32671 5.80952 3.72973 5.80952H8.51787C8.52437 4.9683 8.61554 3.81504 9.45037 3.01668C10.1074 2.38839 11.0081 2 12 2C12.9919 2 13.8926 2.38839 14.5496 3.01668C15.3844 3.81504 15.4756 4.9683 15.4821 5.80952H20.2703C20.6733 5.80952 21 6.12932 21 6.52381C21 6.9183 20.6733 7.2381 20.2703 7.2381H3.72973C3.32671 7.2381 3 6.9183 3 6.52381Z"
                                fill="#1C274C"
                            />
                            <path
                                d="M11.6066 22H12.3935C15.101 22 16.4547 22 17.3349 21.1368C18.2151 20.2736 18.3052 18.8576 18.4853 16.0257L18.7448 11.9452C18.8425 10.4086 18.8913 9.64037 18.4498 9.15352C18.0082 8.66667 17.2625 8.66667 15.7712 8.66667H8.22884C6.7375 8.66667 5.99183 8.66667 5.55026 9.15352C5.1087 9.64037 5.15756 10.4086 5.25528 11.9452L5.51479 16.0257C5.69489 18.8576 5.78494 20.2736 6.66513 21.1368C7.54532 22 8.89906 22 11.6066 22Z"
                                fill="#1C274C"
                            />
                        </svg>
                    </button>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
