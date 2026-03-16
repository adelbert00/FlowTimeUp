<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { useThemeStore } from '@/stores/theme';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const themeStore = useThemeStore();

const showHelpMenu = ref(false);
const showNotifications = ref(false);

const initials = computed(() => {
  const name = user.value?.name ?? '';
  return name
    .split(' ')
    .map((n: string) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase() || 'U';
});

const themeIcon = {
  light: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z',
  dark: 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z',
  system: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
};
</script>

<template>
  <header class="hidden xl:flex fixed top-0 right-0 left-64 z-30 h-14 bg-surface-raised/90 backdrop-blur-sm border-b border-border items-center justify-between px-6">
    <div class="flex items-center gap-4" />

    <div class="flex items-center gap-1">
      <!-- Theme toggle -->
      <button
        @click="themeStore.toggleTheme()"
        class="p-2 text-secondary hover:text-primary hover:bg-surface-overlay rounded-lg transition-colors"
        :title="`Theme: ${themeStore.mode}`"
      >
        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" :d="themeIcon[themeStore.mode as keyof typeof themeIcon]"/>
        </svg>
      </button>

      <!-- Notifications -->
      <div class="relative">
        <div v-if="showNotifications" class="fixed inset-0 z-40" @click="showNotifications = false" />
        <button
          @click="showNotifications = !showNotifications"
          class="p-2 text-secondary hover:text-primary hover:bg-surface-overlay rounded-lg transition-colors"
          title="Notifications"
        >
          <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
        </button>

        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="opacity-0 scale-95 -translate-y-1"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 -translate-y-1"
        >
          <div
            v-if="showNotifications"
            class="absolute right-0 mt-2 w-72 bg-surface-raised rounded-xl shadow-lg border border-border py-1 z-50 origin-top-right"
          >
            <div class="px-4 py-2.5 border-b border-border">
              <h3 class="text-sm font-semibold text-primary">Notifications</h3>
            </div>
            <div class="px-4 py-8 text-center text-muted text-sm">No new notifications</div>
          </div>
        </Transition>
      </div>

      <!-- Help -->
      <div class="relative">
        <div v-if="showHelpMenu" class="fixed inset-0 z-40" @click="showHelpMenu = false" />
        <button
          @click="showHelpMenu = !showHelpMenu"
          class="p-2 text-secondary hover:text-primary hover:bg-surface-overlay rounded-lg transition-colors"
          title="Help"
        >
          <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </button>

        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="opacity-0 scale-95 -translate-y-1"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 -translate-y-1"
        >
          <div
            v-if="showHelpMenu"
            class="absolute right-0 mt-2 w-52 bg-surface-raised rounded-xl shadow-lg border border-border py-1 z-50 origin-top-right"
          >
            <Link href="/help-center" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-secondary hover:text-primary hover:bg-surface-overlay transition-colors">
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Help center
            </Link>
            <Link href="/documentation" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-secondary hover:text-primary hover:bg-surface-overlay transition-colors">
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Documentation
            </Link>
            <div class="border-t border-border my-1" />
            <Link href="/support" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-secondary hover:text-primary hover:bg-surface-overlay transition-colors">
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              Contact support
            </Link>
          </div>
        </Transition>
      </div>

      <div class="w-px h-5 bg-border mx-1" />

      <!-- User dropdown -->
      <Dropdown align="right" width="48">
        <template #trigger>
          <button
            type="button"
            class="flex items-center gap-2 px-2 py-1.5 hover:bg-surface-overlay rounded-lg transition-colors"
          >
            <div class="w-7 h-7 rounded-full bg-accent/15 flex items-center justify-center text-accent-text text-xs font-semibold ring-1 ring-accent/20">
              {{ initials }}
            </div>
            <span class="text-sm font-medium text-primary max-w-[120px] truncate hidden 2xl:block">{{ user?.name }}</span>
            <svg class="w-3.5 h-3.5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
        </template>

        <template #content>
          <div class="px-4 py-3 border-b border-border">
            <p class="text-sm font-medium text-primary">{{ user?.name || 'User' }}</p>
            <p class="text-xs text-muted truncate">{{ user?.email || '' }}</p>
          </div>

          <DropdownLink :href="route('profile.edit')">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              My Profile
            </div>
          </DropdownLink>

          <div class="border-t border-border my-1" />

          <DropdownLink :href="route('logout')" method="post" as="button">
            <div class="flex items-center gap-2 text-danger">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              Sign out
            </div>
          </DropdownLink>
        </template>
      </Dropdown>
    </div>
  </header>
</template>
