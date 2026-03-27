<script setup lang="ts">
// import { Button } from "@/Components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/Components/ui/dialog";
import { format, parseISO } from "date-fns";
import { toZonedTime } from "date-fns-tz";
import { id } from "date-fns/locale";
// import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { computed, onMounted, ref, watch, watchEffect } from "vue";
import { router } from "@inertiajs/vue3";
import { useClipboard } from "@vueuse/core";
import { useToast } from "@/Components/ui/toast/use-toast";
import Button from "./ui/button/Button.vue";

const input = ref("");

const { text, isSupported, copy, copied } = useClipboard();

const formatDate = (dateString: string) => {
    const date = parseISO(dateString);
    const timeZone = "Asia/Singapore";
    const zonedDate = toZonedTime(date, timeZone);
    return format(zonedDate, "d MMMM yyyy, hh:mm aa 'Singapore'", {
        locale: id,
    });
};

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

const formatTanggal = (dateString: string) => {
    const date = parseISO(dateString);
    const timeZone = "Asia/Singapore";
    const zonedDate = toZonedTime(date, timeZone);
    return format(zonedDate, "d MMMM yyyy", { locale: id });
};

const formatMeetingId = (id: number) => {
    const idString = String(id);
    return idString.replace(/(\d{3})(\d{4})(\d{4})/, "$1 $2 $3");
};

const emits = defineEmits(["closeDialog"]);

const props = defineProps({
    meeting: {
        type: Object,
        default: () => ({}),
    },
    invitation: {
        type: Object,
        default: () => ({}),
    },
});

const dialogOpen = ref(false);

watchEffect(() => {
    dialogOpen.value = props.meeting !== null;
});

watch(
    () => dialogOpen.value,
    (newVal, oldVal) => {
        if (oldVal === true && newVal === false) {
            closeDialog();
        }
    }
);

function closeDialog() {
    // Call your custom function here
    router.get(route("zoom.index"), {
        preserveState: true,
        preserveScroll: true,
    });

    // Then emit the 'closeDialog' event to the parent component
    emits("closeDialog");

    // Close the dialog
    dialogOpen.value = false;
}
</script>

<template>
    <Dialog v-model:open="dialogOpen">
        <DialogContent class="lg:max-w-[800px] sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Detail Meeting</DialogTitle>
                <DialogDescription>
                    Detail meeting yang telah Anda buat.
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4">
                <div class="flex">
                    <Label class="min-w-[150px]">Topik Meeting</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ meeting.data.topic }}
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Waktu</Label>
                    <div class="text-sm font-md min-w-full">
                        <p
                            v-if="
                                meeting.data.occurrences &&
                                meeting.data.occurrences.length > 0
                            "
                        >
                            {{
                                formatDate(
                                    meeting.data.occurrences[0].start_time
                                )
                            }}
                        </p>
                        <p
                            v-if="
                                meeting.data.occurrences &&
                                meeting.data.occurrences.length > 0
                            "
                        >
                            s.d.
                            {{
                                formatTanggal(
                                    meeting.data.occurrences[
                                        meeting.data.occurrences.length - 1
                                    ].start_time
                                )
                            }}
                            ({{ meeting.data.occurrences.length }} occurrences)
                        </p>
                        <p v-else>
                            {{ formatDate(meeting.data.start_time) }}
                        </p>
                    </div>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Meeting ID</Label>
                    <p class="text-sm font-md min-w-full">
                        {{ formatMeetingId(meeting.data.id) }}
                    </p>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Passcode</Label>
                    <div class="flex items-center">
                        <p class="text-sm font-md mr-2 min-w[150px]">
                            {{
                                isPasswordVisible
                                    ? meeting.data.password
                                    : "••••••"
                            }}
                        </p>
                        <button @click="isPasswordVisible = !isPasswordVisible">
                            <svg
                                v-if="isPasswordVisible"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-muted-foreground"
                            >
                                <path
                                    d="M2.99902 3L20.999 21M9.8433 9.91364C9.32066 10.4536 8.99902 11.1892 8.99902 12C8.99902 13.6569 10.3422 15 11.999 15C12.8215 15 13.5667 14.669 14.1086 14.133M6.49902 6.64715C4.59972 7.90034 3.15305 9.78394 2.45703 12C3.73128 16.0571 7.52159 19 11.9992 19C13.9881 19 15.8414 18.4194 17.3988 17.4184M10.999 5.04939C11.328 5.01673 11.6617 5 11.9992 5C16.4769 5 20.2672 7.94291 21.5414 12C21.2607 12.894 20.8577 13.7338 20.3522 14.5"
                                    stroke="#000000"
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
                                class="h-5 w-5 text-muted-foreground"
                            >
                                <path
                                    d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z"
                                    stroke="#000000"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z"
                                    stroke="#000000"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex">
                    <Label class="min-w-[150px]">Invite Link</Label>
                    <div class="text-sm font-md min-w-full flex items-center">
                        <p>
                            {{ meeting.data.join_url }}
                        </p>
                        <button
                            v-if="isSupported"
                            @click="copy(meeting.data.join_url)"
                            class="focus:outline-none"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 500 500"
                                stroke="currentColor"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                class="h-4 w-4 text-muted-foreground"
                            >
                                <path
                                    d="M447.168,134.56c-0.535-1.288-1.318-2.459-2.304-3.445l-128-128c-2.003-1.988-4.709-3.107-7.531-3.115H138.667
			C132.776,0,128,4.776,128,10.667V64H74.667C68.776,64,64,68.776,64,74.667v426.667C64,507.224,68.776,512,74.667,512h298.667
			c5.891,0,10.667-4.776,10.667-10.667V448h53.333c5.891,0,10.667-4.776,10.667-10.667V138.667
			C447.997,137.256,447.714,135.86,447.168,134.56z M320,36.416L411.584,128H320V36.416z M362.667,490.667H85.333V85.333H128v352
			c0,5.891,4.776,10.667,10.667,10.667h224V490.667z M426.667,426.667H149.333V21.333h149.333v117.333
			c0,5.891,4.776,10.667,10.667,10.667h117.333V426.667z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex">
                    <Button @click="copy(invitation.data.invitation)"
                        >Copy Invitation</Button
                    >
                </div>
            </div>
            <!-- <DialogFooter>
                <Button type="submit"> Save changes </Button>
            </DialogFooter> -->
        </DialogContent>
    </Dialog>
</template>
