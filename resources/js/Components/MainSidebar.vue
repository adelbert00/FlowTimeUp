<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useThemeStore } from '@/stores/theme';

const themeStore = useThemeStore();
const page = usePage();

defineEmits<{ close: [] }>();

const user = computed(() => page.props.auth?.user);
const initials = computed(() => {
  const name = user.value?.name ?? '';
  return name
    .split(' ')
    .map((n: string) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase() || 'U';
});

const themeLabel = computed(() => {
  const map = { light: 'Light', dark: 'Dark', system: 'System' };
  return map[themeStore.mode as keyof typeof map] ?? 'System';
});

const navGroups = [
  {
    label: 'Track',
    items: [
      {
        href: '/tasks',
        label: 'Time Tracker',
        match: (url: string) => url.startsWith('/tasks'),
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
      },
      {
        href: '/calendar',
        label: 'Calendar',
        match: (url: string) => url.startsWith('/calendar'),
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
      },
    ],
  },
  {
    label: 'Analyze',
    items: [
      {
        href: '/home',
        label: 'Dashboard',
        match: (url: string) => url === '/home',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
      },
      {
        href: '/reports',
        label: 'Reports',
        match: (url: string) => url.startsWith('/reports'),
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
      },
    ],
  },
  {
    label: 'Manage',
    items: [
      {
        href: '/projects',
        label: 'Projects',
        match: (url: string) => url.startsWith('/projects'),
        icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
      },
      {
        href: '/tags',
        label: 'Tags',
        match: (url: string) => url.startsWith('/tags'),
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
      },
      {
        href: '/task-templates',
        label: 'Templates',
        match: (url: string) => url.startsWith('/task-templates'),
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
      },
    ],
  },
];
</script>

<template>
  <aside class="w-64 bg-surface-raised border-r border-border min-h-screen flex flex-col">
    <!-- Logo -->
    <div class="h-14 px-5 border-b border-border flex items-center justify-between">
      <Link href="/home" prefetch="hover" class="flex items-center gap-2.5 group">
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center shrink-0 shadow-sm group-hover:shadow-accent/30 transition-shadow">
          <svg class="w-[18px] h-[18px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-[15px] font-semibold tracking-tight text-primary">FlowTimeUp</span>
      </Link>
      <button
        @click="$emit('close')"
        class="xl:hidden p-1.5 rounded-md text-secondary hover:text-primary hover:bg-surface-overlay transition-colors"
        aria-label="Close sidebar"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-5">
      <div v-for="group in navGroups" :key="group.label">
        <p class="px-2 mb-1.5 text-[10px] font-semibold text-muted uppercase tracking-widest select-none">
          {{ group.label }}
        </p>
        <div class="space-y-0.5">
          <Link
            v-for="item in group.items"
            :key="item.href"
            :href="item.href"
            prefetch="hover"
            @click="$emit('close')"
            class="nav-indicator flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all duration-150"
            :class="item.match($page.url)
              ? 'active bg-accent-subtle text-accent-text'
              : 'text-secondary hover:text-primary hover:bg-surface-overlay'"
          >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon"/>
            </svg>
            {{ item.label }}
          </Link>
        </div>
      </div>

      <!-- Mobile-only Account section -->
      <div class="xl:hidden">
        <p class="px-2 mb-1.5 text-[10px] font-semibold text-muted uppercase tracking-widest select-none">
          Account
        </p>
        <div class="space-y-0.5">
          <button
            @click="themeStore.toggleTheme()"
            class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium text-secondary hover:text-primary hover:bg-surface-overlay transition-colors"
          >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                v-if="themeStore.mode === 'dark'"
              />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                v-else
              />
            </svg>
            Theme: {{ themeLabel }}
          </button>

          <Link
            href="/profile"
            prefetch="hover"
            @click="$emit('close')"
            class="nav-indicator flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all duration-150"
            :class="$page.url.startsWith('/profile')
              ? 'active bg-accent-subtle text-accent-text'
              : 'text-secondary hover:text-primary hover:bg-surface-overlay'"
          >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profile
          </Link>

          <Link
            :href="route('logout')"
            method="post"
            as="button"
            @click="$emit('close')"
            class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium text-secondary hover:text-danger hover:bg-danger-subtle transition-all duration-150"
          >
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Sign out
          </Link>
        </div>
      </div>
    </nav>

    <!-- User footer (mobile + desktop) -->
    <div class="p-3 border-t border-border">
      <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg">
        <div class="w-7 h-7 rounded-full bg-accent/15 flex items-center justify-center text-accent-text text-xs font-semibold shrink-0 ring-1 ring-accent/20">
          {{ initials }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-medium text-primary truncate">{{ user?.name || 'User' }}</p>
          <p class="text-[11px] text-muted truncate">{{ user?.email || '' }}</p>
        </div>
      </div>
    </div>
  </aside>
</template>
