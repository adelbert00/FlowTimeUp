<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Main_Sidebar from '@/Components/Main_Sidebar.vue';
import TopNavbar from '@/Components/TopNavbar.vue';
import { useThemeStore } from '@/stores/theme';

const sidebarOpen = ref(false);
const themeStore = useThemeStore();

onMounted(() => {
  themeStore.updateTheme();
});
</script>

<template>
  <div class="flex min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors">
    <button
      @click="sidebarOpen = true"
      class="xl:hidden fixed z-40 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors shadow-lg top-4 left-4 sm:top-6 sm:left-6 md:top-8 md:left-8"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <div v-if="sidebarOpen" class="xl:hidden fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" @click="sidebarOpen = false" />

    <Main_Sidebar
      :class="['fixed xl:static inset-y-0 left-0 z-50 transform transition-transform duration-300 ease-in-out', sidebarOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0']"
      @close="sidebarOpen = false"
    />

    <TopNavbar />

    <main class="flex-1 overflow-auto xl:ml-0 xl:pt-14 w-full">
      <slot />
    </main>
  </div>
</template>
