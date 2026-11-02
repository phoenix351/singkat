<template>
  <Head title="Pengajuan" />
  <Toaster />

  <MeetingLayout>
    <div class="flex items-center justify-between space-y-2">
      <h2 class="text-3xl font-bold tracking-tight">
        Pengajuan Meeting Anda
      </h2>
      <div class="flex items-center space-x-2">
        <!-- <DateRangePicker /> -->
        <!-- <Button>Download</Button> -->
      </div>
    </div>
    <Tabs default-value="overview" class="space-y-4">
      <TabsList>
        <TabsTrigger value="overview"> Daftar Pengajuan </TabsTrigger>
        <TabsTrigger value="pengajuan"> Buat Pengajuan </TabsTrigger>
      </TabsList>
      <TabsContent value="overview" class="space-y-4 flex flex-col">
        <div class="grid gap-4 flex-grow">
          <Card class="lg:col-span-4 min-h-[75vh] overflow-auto">
            <CardHeader>
              <CardTitle>Daftar Pengajuan Meeting</CardTitle>
            </CardHeader>
            <CardContent>
              <MeetingList
                :user_meetings="user_meetings"
                :filters="filters"
              />
            </CardContent>
          </Card>
        </div>
      </TabsContent>
      <TabsContent value="pengajuan" class="space-y-4 flex flex-col">
        <div class="grid gap-4 flex-grow">
          <Card class="lg:col-span-4 min-h-[75vh] overflow-auto">
            <CardHeader>
              <CardTitle>Buat Pengajuan Meeting</CardTitle>
            </CardHeader>
            <CardContent>
              <MeetingForm />
            </CardContent>
          </Card>
        </div>
      </TabsContent>
    </Tabs>
    <ViewDialog :meeting="meeting" :invitation="invitation" />
  </MeetingLayout>
</template>

<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { watch } from "vue";
import MeetingLayout from "@/Layouts/Meeting/MeetingLayout.vue";
import MeetingList from "@/Components/MeetingList.vue";
import MeetingForm from "@/Components/MeetingForm.vue";
import ViewDialog from "@/Components/ViewDialog.vue";
import { useToast } from "@/Components/ui/toast/use-toast";
import { Toaster } from "@/Components/ui/toast";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/lib/registry/new-york/ui/card";
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from "@/lib/registry/new-york/ui/tabs";

const { toast } = useToast();

const props = defineProps({
  user_meetings: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  flash: {
    type: Object,
    default: () => ({ success: false, error: false }),
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

watch(
  () => props.flash,
  (newFlash) => {
    if (newFlash?.success) {
      toast({
        title: "Berhasil",
        description: newFlash.success,
      });
    } else if (newFlash?.error) {
      toast({
        title: "Uh oh! Gagal.",
        description: newFlash.error,
        variant: "destructive",
      });
    }
  },
  { immediate: true, deep: true }
);
</script>

<style scoped></style>
