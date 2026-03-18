<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import MainSidebar from '@/Components/MainSidebar.vue';
import TopNavbar from '@/Components/TopNavbar.vue';
import { useThemeStore } from '@/stores/theme';

const sidebarOpen = ref(false);
const themeStore = useThemeStore();
const pageLoading = ref(false);

onMounted(() => {
  themeStore.updateTheme();

  router.on('start', () => { pageLoading.value = true; });
  router.on('finish', () => { pageLoading.value = false; });
});
</script>

<template>
  <div class="flex min-h-screen bg-surface transition-colors">
    <!-- Page transition bar -->
    <div
      class="fixed top-0 left-0 z-[100] h-[2px] bg-accent transition-all duration-300"
      :class="pageLoading ? 'w-3/4 opacity-100' : 'w-0 opacity-0'"
    />

    <!-- Mobile hamburger -->
    <button
      @click="sidebarOpen = true"
      class="xl:hidden fixed z-40 p-2.5 bg-surface-raised rounded-lg border border-border text-secondary hover:text-primary transition-colors shadow-sm top-4 left-4 sm:top-5 sm:left-5"
      aria-label="Open sidebar"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <!-- Backdrop -->
    <Transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="sidebarOpen"
        class="xl:hidden fixed inset-0 z-40 bg-black/30 backdrop-blur-[2px]"
        @click="sidebarOpen = false"
      />
    </Transition>

    <MainSidebar
      :class="[
        'fixed xl:static inset-y-0 left-0 z-50 transform transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'
      ]"
      @close="sidebarOpen = false"
    />

    <TopNavbar />

    <main class="flex-1 overflow-auto xl:ml-0 xl:pt-14 w-full animate-fade-up">
      <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto">
        <slot />
      </div>
    </main>
  </div>
</template>
