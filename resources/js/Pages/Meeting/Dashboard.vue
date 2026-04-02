<template>
  <Head title="Dashboard" />
  <div class="flex-col md:flex">
    <div class="border-b">
      <div class="flex h-16 items-center px-4">
        <MainNav class="mx-6" />
        <div class="ml-auto flex items-center space-x-4">
          <UserNav />
        </div>
      </div>
    </div>
    <div class="flex-1 space-y-4 p-8 pt-6">
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
          <TabsTrigger value="upcoming"> Upcoming </TabsTrigger>
          <TabsTrigger value="previous"> Previous </TabsTrigger>
          <TabsTrigger value="live"> Live </TabsTrigger>
        </TabsList>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle class="text-sm font-medium"> Total </CardTitle>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                class="h-5 w-5 text-muted-foreground"
              >
                <path
                  d="M16,16H10.41l3.3-3.29a1,1,0,0,0,0-1.42L10.41,8H16a1,1,0,0,0,0-2H8a1,1,0,0,0-.92.62,1,1,0,0,0,.21,1.09L11.59,12l-4.3,4.29a1,1,0,0,0-.21,1.09A1,1,0,0,0,8,18h8a1,1,0,0,0,0-2Z"
                />
              </svg>
            </CardHeader>
            <CardContent>
              <div class="text-2xl font-bold">
                {{ all_meetings.data.total_records }}
              </div>
              <p class="text-xs text-muted-foreground">meetings</p>
            </CardContent>
          </Card>
          <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle class="text-sm font-medium"> Upcoming </CardTitle>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 80 80"
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="8"
                class="h-5 w-5 text-muted-foreground"
              >
                <path
                  d="M43.006,23.916c-0.28-0.282-0.59-0.52-0.912-0.727L20.485,1.581c-2.109-2.107-5.527-2.108-7.637,0.001
		c-2.109,2.108-2.109,5.527,0,7.637l18.611,18.609L12.754,46.535c-2.11,2.107-2.11,5.527,0,7.637c1.055,1.053,2.436,1.58,3.817,1.58
		s2.765-0.527,3.817-1.582l21.706-21.703c0.322-0.207,0.631-0.444,0.912-0.727c1.08-1.08,1.598-2.498,1.574-3.912
		C44.605,26.413,44.086,24.993,43.006,23.916z"
                />
              </svg>
            </CardHeader>
            <CardContent>
              <div class="text-2xl font-bold">
                {{ upcoming_meetings.data.total_records }}
              </div>
              <p class="text-xs text-muted-foreground">meetings</p>
            </CardContent>
          </Card>
          <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle class="text-sm font-medium"> Previous </CardTitle>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 80 80"
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="8"
                class="h-5 w-5 text-muted-foreground"
              >
                <path
                  d="M12.745,23.915c0.283-0.282,0.59-0.52,0.913-0.727L35.266,1.581c2.108-2.107,5.528-2.108,7.637,0.001
		c2.109,2.108,2.109,5.527,0,7.637L24.294,27.828l18.705,18.706c2.109,2.108,2.109,5.526,0,7.637
		c-1.055,1.056-2.438,1.582-3.818,1.582s-2.764-0.526-3.818-1.582L13.658,32.464c-0.323-0.207-0.632-0.445-0.913-0.727
		c-1.078-1.078-1.598-2.498-1.572-3.911C11.147,26.413,11.667,24.994,12.745,23.915z"
                />
              </svg>
            </CardHeader>
            <CardContent>
              <div class="text-2xl font-bold">
                {{ previous_meetings.data.total_records }}
              </div>
              <p class="text-xs text-muted-foreground">meetings</p>
            </CardContent>
          </Card>
          <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle class="text-sm font-medium"> Live Now </CardTitle>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                class="h-5 w-5 text-muted-foreground"
              >
                <!-- <path fill="none" d="M0 0h24v24H0z" /> -->
                <path
                  fill-rule="nonzero"
                  d="M16 4a1 1 0 0 1 1 1v4.2l5.213-3.65a.5.5 0 0 1 .787.41v12.08a.5.5 0 0 1-.787.41L17 14.8V19a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h14zm-1 2H3v12h12V6zM7.4 8.829a.4.4 0 0 1 .215.062l4.355 2.772a.4.4 0 0 1 0 .674L7.615 15.11A.4.4 0 0 1 7 14.77V9.23c0-.221.18-.4.4-.4zM21 8.84l-4 2.8v.718l4 2.8V8.84z"
                />
              </svg>
            </CardHeader>
            <CardContent>
              <div class="text-2xl font-bold">
                {{ live_meetings.data.total_records }}
              </div>
              <p class="text-xs text-muted-foreground">meetings</p>
            </CardContent>
          </Card>
        </div>
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
              <CardHeader>
                <CardTitle>Pengajuan Terbaru</CardTitle>
                <CardDescription>
                  <!-- You made 265 sales this month. -->
                </CardDescription>
              </CardHeader>
              <CardContent>
                <RecentRequest :meetings="meetings" />
              </CardContent>
            </Card>
          </div>
        </TabsContent>
        <TabsContent value="upcoming" class="space-y-4 flex flex-col">
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 flex-grow">
            <Card class="lg:col-span-4 h-[60vh] overflow-auto">
              <CardHeader>
                <CardTitle>Upcoming Meetings</CardTitle>
              </CardHeader>
              <CardContent>
                <Upcoming
                  :upcoming_meetings="upcoming_meetings"
                  :meetings="meetings as unknown[]"
                />
              </CardContent>
            </Card>
            <Card class="lg:col-span-3 h-[60vh] overflow-auto">
              <CardHeader>
                <CardTitle>Pengajuan Terbaru</CardTitle>
                <CardDescription>
                  <!-- You made 265 sales this month. -->
                </CardDescription>
              </CardHeader>
              <CardContent>
                <RecentRequest :meetings="meetings" />
              </CardContent>
            </Card>
          </div>
        </TabsContent>
        <TabsContent value="previous" class="space-y-4 flex flex-col">
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 flex-grow">
            <Card class="lg:col-span-4 h-[60vh] overflow-auto">
              <CardHeader>
                <CardTitle>Previous Meetings</CardTitle>
              </CardHeader>
              <CardContent>
                <Previous
                  :previous_meetings="previous_meetings"
                  :meetings="meetings as unknown[]"
                />
              </CardContent>
            </Card>
            <Card class="lg:col-span-3 h-[60vh] overflow-auto">
              <CardHeader>
                <CardTitle>Pengajuan Terbaru</CardTitle>
                <CardDescription>
                  <!-- You made 265 sales this month. -->
                </CardDescription>
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
                <Live :live_meetings="live_meetings" :meetings="meetings as unknown[]" />
              </CardContent>
            </Card>
            <Card class="lg:col-span-3 h-[60vh] overflow-auto">
              <CardHeader>
                <CardTitle>Pengajuan Terbaru</CardTitle>
                <CardDescription>
                  <!-- You made 265 sales this month. -->
                </CardDescription>
              </CardHeader>
              <CardContent>
                <RecentRequest :meetings="meetings" />
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Overview from "@/Components/Overview.vue";
import Upcoming from "@/Components/Upcoming.vue";
import Previous from "@/Components/Previous.vue";
import Live from "@/Components/Live.vue";
import MainNav from "@/Components/MainNav.vue";
import RecentRequest from "@/Components/RecentRequest.vue";
import UserNav from "@/Components/UserNav.vue";
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
  // add other properties as needed
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
