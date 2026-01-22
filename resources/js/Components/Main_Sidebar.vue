<template>
  <aside class="w-64 bg-white border-r border-gray-200 min-h-screen flex flex-col shadow-sm">
    <!-- Mobile close button -->
    <button
      @click="$emit('close')"
      class="xl:hidden absolute top-4 right-4 p-2 text-gray-500 hover:text-gray-900 transition-colors"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

    <!-- Logo -->
    <div class="p-6 border-b border-gray-200">
      <Link href="/home" class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <span class="text-xl font-bold text-gray-900">FlowTime</span>
      </Link>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1">
      <p class="px-3 mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu</p>
      
      <Link
        href="/home"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="$page.url === '/home' 
          ? 'bg-blue-50 text-blue-600 border-l-2 border-blue-600' 
          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
      </Link>

      <Link
        href="/tasks"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="$page.url.startsWith('/tasks') 
          ? 'bg-blue-50 text-blue-600 border-l-2 border-blue-600' 
          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Time Tracker
      </Link>

      <Link
        href="/projects"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="$page.url.startsWith('/projects') 
          ? 'bg-blue-50 text-blue-600 border-l-2 border-blue-600' 
          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
        </svg>
        Projects
      </Link>

      <Link
        href="/tags"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="$page.url.startsWith('/tags') 
          ? 'bg-blue-50 text-blue-600 border-l-2 border-blue-600' 
          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        Tags
      </Link>

      <Link
        href="/calendar"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="$page.url.startsWith('/calendar') 
          ? 'bg-blue-50 text-blue-600 border-l-2 border-blue-600' 
          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Calendar
      </Link>

      <Link
        href="/reports"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="$page.url.startsWith('/reports') 
          ? 'bg-blue-50 text-blue-600 border-l-2 border-blue-600' 
          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
      </Link>

      <Link
        href="/task-templates"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="$page.url.startsWith('/task-templates') 
          ? 'bg-blue-50 text-blue-600 border-l-2 border-blue-600' 
          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Templates
      </Link>

      <p class="px-3 mt-6 mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</p>

      <Link
        href="/profile"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="$page.url.startsWith('/profile') 
          ? 'bg-blue-50 text-blue-600 border-l-2 border-blue-600' 
          : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Profile
      </Link>

      <Link
        :href="route('logout')"
        method="post"
        as="button"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:text-red-600 hover:bg-red-50 transition-colors"
        @click="$emit('close')"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Sign out
      </Link>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-200">
      <div class="flex items-center gap-3 px-3 py-2">
        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium">
          {{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate">
            {{ $page.props.auth?.user?.name || 'User' }}
          </p>
          <p class="text-xs text-gray-500 truncate">
            {{ $page.props.auth?.user?.email || '' }}
          </p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineEmits<{
  close: [];
}>();
</script>
