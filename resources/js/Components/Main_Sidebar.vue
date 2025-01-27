<template>
    <SidebarProvider>
      <Sidebar>
        <SidebarHeader>
          <div class="flex items-center px-4 py-2 ">
            <span class="text-lg font-bold">MyToDoApp</span>
          </div>
        </SidebarHeader>
        <SidebarContent>
          <SidebarGroup>
            <SidebarGroupLabel>Navigation</SidebarGroupLabel>
            <SidebarGroupContent>
              <SidebarMenu>
                <SidebarMenuItem>
                  <SidebarMenuButton asChild>
                    <Link href="/home">
                      <span>Home</span>
                    </Link>
                  </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                  <SidebarMenuButton asChild>
                    <Link href="/tasks" >
                      <span >Tasks</span>
                    </Link>
                  </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                  <SidebarMenuButton asChild>
                    <Link href="/profile">
                      <span>Profile</span>
                    </Link>
                  </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                  <form method="POST" action="/logout">
                    <SidebarMenuButton asChild>
                      <button type="submit">
                        <span>Logout</span>
                      </button>
                    </SidebarMenuButton>
                  </form>
                </SidebarMenuItem>
              </SidebarMenu>
            </SidebarGroupContent>
          </SidebarGroup>
        </SidebarContent>
  
        <SidebarFooter>
          <div class="px-4 py-2">
            <span class="text-sm">© 2025 MyToDoApp</span>
          </div>
        </SidebarFooter>
      </Sidebar>
    </SidebarProvider>
  </template>
  
  <script lang="ts">
  import { defineComponent } from 'vue';
  import {
    SidebarProvider,
    Sidebar,
    SidebarHeader,
    SidebarContent,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuItem,
    SidebarMenuButton,
    SidebarFooter,
  } from './ui/sidebar';
  import { Link } from '@inertiajs/vue3';
  import Button from './ui/button/Button.vue';
  
  export default defineComponent({
    name: 'AppSidebar',
    components: {
      SidebarProvider,
      Sidebar,
      SidebarHeader,
      SidebarContent,
      SidebarGroup,
      SidebarGroupLabel,
      SidebarGroupContent,
      SidebarMenu,
      SidebarMenuItem,
      SidebarMenuButton,
      SidebarFooter,
      Link,
      Button,
    },
  });
  </script>
  