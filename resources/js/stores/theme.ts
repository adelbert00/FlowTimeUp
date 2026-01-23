import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

export type ThemeMode = 'light' | 'dark' | 'system';

export const useThemeStore = defineStore('theme', () => {
  const mode = ref<ThemeMode>((localStorage.getItem('theme') as ThemeMode) || 'light');
  const isDark = ref(false);

  function updateTheme() {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (mode.value === 'dark' || (mode.value === 'system' && prefersDark)) {
      document.documentElement.classList.add('dark');
      isDark.value = true;
    } else {
      document.documentElement.classList.remove('dark');
      isDark.value = false;
    }
  }

  function setMode(newMode: ThemeMode) {
    mode.value = newMode;
    localStorage.setItem('theme', newMode);
    updateTheme();
  }

  function toggleTheme() {
    if (mode.value === 'light') {
      setMode('dark');
    } else if (mode.value === 'dark') {
      setMode('system');
    } else {
      setMode('light');
    }
  }

  watch(mode, updateTheme, { immediate: true });

  if (typeof window !== 'undefined') {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateTheme);
  }

  return { mode, isDark, setMode, toggleTheme, updateTheme };
});
