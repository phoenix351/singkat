<template>
  <Head title="Dashboard" />
  <MeetingLayout>
    <div class="flex items-center justify-between space-y-2">
      <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>
      <div class="flex items-center space-x-2">
        <!-- <DateRangePicker /> -->
        <!-- <Button>Download</Button> -->
      </div>
    </div>
    <Tabs default-value="overview" class="space-y-4">
      <TabsList>
        <TabsTrigger value="overview"> Overview </TabsTrigger>
        <TabsTrigger value="live"> Live </TabsTrigger>
      </TabsList>
      <TabsContent value="overview" class="space-y-4 flex flex-col">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 flex-grow">
          <Card class="lg:col-span-4 h-[60vh]">
            <CardContent class="mt-5">
              <Overview
                :all_meetings="all_meetings"
                :meetings="meetings as Meeting[]"
              />
            </CardContent>
          </Card>
          <Card class="lg:col-span-3 h-[60vh] overflow-auto">
            <CardHeader class="pb-3">
              <CardTitle>20 Pengajuan Terbaru</CardTitle>
            </CardHeader>
            <CardContent>
              <RecentRequest :meetings="meetings" />
            </CardContent>
          </Card>
        </div>
      </TabsContent>
      <TabsContent value="live" class="space-y-4 flex flex-col">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 flex-grow">
          <Card class="lg:col-span-4 h-[60vh] overflow-auto">
            <CardHeader>
              <CardTitle>Live Meetings</CardTitle>
            </CardHeader>
            <CardContent>
              <Live
                :live_meetings="live_meetings"
                :meetings="meetings as unknown[]"
              />
            </CardContent>
          </Card>
          <Card class="lg:col-span-3 h-[60vh] overflow-auto">
            <CardHeader class="pb-3">
              <CardTitle>20 Pengajuan Terbaru</CardTitle>
            </CardHeader>
            <CardContent>
              <RecentRequest :meetings="meetings" />
            </CardContent>
          </Card>
        </div>
      </TabsContent>
    </Tabs>
  </MeetingLayout>
</template>

<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import MeetingLayout from "@/Layouts/Meeting/MeetingLayout.vue";
import Overview from "@/Components/Overview.vue";
import Live from "@/Components/Live.vue";
import RecentRequest from "@/Components/RecentRequest.vue";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/lib/registry/new-york/ui/card";
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from "@/lib/registry/new-york/ui/tabs";

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
}

const props = defineProps({
  all_meetings: {
    type: Object,
    default: () => ({}),
  },
  upcoming_meetings: {
    type: Object,
    default: () => ({}),
  },
  previous_meetings: {
    type: Object,
    default: () => ({}),
  },
  live_meetings: {
    type: Object,
    default: () => ({}),
  },
  meetings: {
    type: Object,
    default: () => ({}),
  },
});
</script>

<style scoped></style>
