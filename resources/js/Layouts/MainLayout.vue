<script setup lang="ts">
import { ref } from 'vue';
import Main_Sidebar from '@/Components/Main_Sidebar.vue';

const sidebarOpen = ref(false);
</script>

<template>
  <div class="flex min-h-screen bg-gray-50">
    <!-- Mobile menu button - fixed position with tablet-friendly padding -->
    <button
      @click="sidebarOpen = true"
      class="xl:hidden fixed z-40 p-3 bg-white rounded-lg border border-gray-200 text-gray-600 hover:text-gray-900 transition-colors shadow-lg top-4 left-4 sm:top-6 sm:left-6 md:top-8 md:left-8"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <!-- Mobile overlay -->
    <div
      v-if="sidebarOpen"
      class="xl:hidden fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
      @click="sidebarOpen = false"
    />

    <!-- Sidebar -->
    <Main_Sidebar
      :class="[
        'fixed xl:static inset-y-0 left-0 z-50 transform transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'
      ]"
      @close="sidebarOpen = false"
    />

    <!-- Main content -->
    <main class="flex-1 overflow-auto xl:ml-0 w-full">
      <slot />
    </main>
  </div>
</template>
