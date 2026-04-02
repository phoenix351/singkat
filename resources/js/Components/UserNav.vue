<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from "@/lib/registry/new-york/ui/avatar";
import { Button } from "@/lib/registry/new-york/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuTrigger,
} from "@/lib/registry/new-york/ui/dropdown-menu";
import { computed, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import axios from "axios";
const page = usePage();

const user = ref(page.props.auth.user);

const userInitials = computed(() => {
  if (user.value && user.value.name) {
    const names = user.value.name.split(" ");
    let initials = names[0].charAt(0);
    if (names.length > 1) {
      initials += names[1].charAt(0);
    }
    return initials;
  }
  return "";
});
const meetingLogout = async () => {
  const { data } = await axios.get(route("api.token.csrf"));
  router.visit(route("logout"), {
    method: "post",
    data: {
      _token: data,
    },
  });
};
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="relative h-8 w-8 rounded-full">
        <Avatar class="h-8 w-8">
          <!-- <AvatarImage src="/avatars/01.png" alt="@shadcn" /> -->
          <AvatarFallback>{{ userInitials }}</AvatarFallback>
        </Avatar>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent class="w-56" align="end">
      <DropdownMenuLabel class="font-normal flex">
        <div class="flex flex-col space-y-1">
          <p class="text-sm font-medium leading-none">
            {{ page.props.auth.user.name }}
          </p>
          <p class="text-xs leading-none text-muted-foreground">
            {{ page.props.auth.user.email }}
          </p>
        </div>
      </DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuGroup>
        <DropdownMenuItem>
          Profile
          <!-- <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut> -->
        </DropdownMenuItem>
        <!-- <DropdownMenuItem>
                    Billing
                    <DropdownMenuShortcut>⌘B</DropdownMenuShortcut>
                </DropdownMenuItem>
                <DropdownMenuItem>
                    Settings
                    <DropdownMenuShortcut>⌘S</DropdownMenuShortcut>
                </DropdownMenuItem>
                <DropdownMenuItem>New Team</DropdownMenuItem> -->
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
      <DropdownMenuItem>
        <div @click="meetingLogout" as="button">Log out</div>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
