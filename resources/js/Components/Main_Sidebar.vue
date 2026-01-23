<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useThemeStore } from '@/stores/theme';

const themeStore = useThemeStore();

defineEmits<{ close: [] }>();

const themeIcon = {
  light: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z',
  dark: 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z',
  system: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
};
</script>

<template>
  <aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 min-h-screen flex flex-col shadow-sm">
    <button @click="$emit('close')" class="xl:hidden absolute top-4 right-4 p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
      <Link href="/home" prefetch="hover" class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <span class="text-xl font-bold font-sans text-gray-900 dark:text-white">FlowTimeUp</span>
      </Link>
    </div>

    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
      <p class="px-3 mb-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Track</p>
      
      <Link href="/tasks" prefetch="hover" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/tasks') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-2 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'" @click="$emit('close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Time Tracker
      </Link>

      <Link href="/calendar" prefetch="hover" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/calendar') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-2 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'" @click="$emit('close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Calendar
      </Link>

      <p class="px-3 mt-6 mb-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Analyze</p>

      <Link href="/home" prefetch="hover" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url === '/home' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-2 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'" @click="$emit('close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
      </Link>

      <Link href="/reports" prefetch="hover" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/reports') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-2 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'" @click="$emit('close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Reports
      </Link>

      <p class="px-3 mt-6 mb-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Manage</p>

      <Link href="/projects" prefetch="hover" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/projects') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-2 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'" @click="$emit('close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        Projects
      </Link>

      <Link href="/tags" prefetch="hover" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/tags') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-2 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'" @click="$emit('close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        Tags
      </Link>

      <Link href="/task-templates" prefetch="hover" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/task-templates') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-2 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'" @click="$emit('close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Templates
      </Link>

      <div class="xl:hidden">
        <p class="px-3 mt-6 mb-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account</p>

        <button @click="themeStore.toggleTheme()" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="themeIcon[themeStore.mode]"/></svg>
          Theme: {{ themeStore.mode }}
        </button>

        <Link href="/profile" prefetch="hover" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors" :class="$page.url.startsWith('/profile') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-2 border-blue-600' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'" @click="$emit('close')">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          Profile
        </Link>

        <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" @click="$emit('close')">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Sign out
        </Link>
      </div>
    </nav>

    <div class="xl:hidden p-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3 px-3 py-2">
        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium">{{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() || 'U' }}</div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $page.props.auth?.user?.name || 'User' }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $page.props.auth?.user?.email || '' }}</p>
        </div>
      </div>
    </div>
  </aside>
</template>
