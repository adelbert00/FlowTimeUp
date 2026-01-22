<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
    <!-- Decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-60 sm:w-80 h-60 sm:h-80 bg-blue-600/10 rounded-full blur-3xl" />
      <div class="absolute -bottom-40 -left-40 w-60 sm:w-80 h-60 sm:h-80 bg-blue-600/10 rounded-full blur-3xl" />
    </div>

    <!-- Logo -->
    <Link href="/" class="relative flex items-center gap-2 sm:gap-3 mb-6 sm:mb-8">
      <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-500/25">
        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <span class="text-xl sm:text-2xl font-bold text-gray-900">FlowTime</span>
    </Link>

    <!-- Card -->
    <div class="relative w-full max-w-md">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-500/20 rounded-2xl blur-xl" />
      <div class="relative bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 shadow-2xl p-5 sm:p-8">
        <slot />
      </div>
    </div>

    <!-- Footer -->
    <p class="relative mt-6 sm:mt-8 text-xs sm:text-sm text-gray-500">
      © 2025 FlowTime. All rights reserved.
    </p>
  </div>
</template>
