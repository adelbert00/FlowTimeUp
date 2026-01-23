import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

interface TimerState {
  taskId: number | null;
  startTime: Date | null;
  isRunning: boolean;
}

const STORAGE_KEY = 'flowtimeup_active_timer';

export const useTimerStore = defineStore('timer', () => {
  const taskId = ref<number | null>(null);
  const startTime = ref<Date | null>(null);
  const isRunning = ref(false);
  const currentTime = ref<Date>(new Date());
  let intervalId: number | null = null;
  let autoSaveIntervalId: number | null = null;

  function loadFromStorage() {
    if (typeof window === 'undefined') return;
    
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored) {
        const data = JSON.parse(stored);
        if (data.taskId && data.startTime) {
          const storedStartTime = new Date(data.startTime);
          const hoursSinceStart = (Date.now() - storedStartTime.getTime()) / (1000 * 60 * 60);
          if (hoursSinceStart < 24) {
            taskId.value = data.taskId;
            startTime.value = storedStartTime;
            isRunning.value = true;
            startInterval();
            startAutoSave();
          } else {
            autoSaveTimer(data.taskId, storedStartTime, new Date());
            clearStorage();
          }
        }
      }
    } catch (e) {
      console.error('Failed to load timer from storage:', e);
      clearStorage();
    }
  }

  function saveToStorage() {
    if (typeof window === 'undefined') return;
    
    try {
      if (taskId.value && startTime.value) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
          taskId: taskId.value,
          startTime: startTime.value.toISOString(),
        }));
      } else {
        clearStorage();
      }
    } catch (e) {
      console.error('Failed to save timer to storage:', e);
    }
  }

  function clearStorage() {
    if (typeof window === 'undefined') return;
    localStorage.removeItem(STORAGE_KEY);
  }

  function startInterval() {
    if (intervalId) return;
    intervalId = window.setInterval(() => {
      if (isRunning.value) {
        currentTime.value = new Date();
      }
    }, 10);
  }

  function stopInterval() {
    if (intervalId) {
      clearInterval(intervalId);
      intervalId = null;
    }
  }

  function startAutoSave() {
    if (autoSaveIntervalId) return;
    let lastSaveTime = startTime.value ? startTime.value.getTime() : Date.now();
    
    autoSaveIntervalId = window.setInterval(() => {
      if (taskId.value && startTime.value && isRunning.value) {
        const now = new Date();
        const minutesElapsed = (now.getTime() - lastSaveTime) / (1000 * 60);
        if (minutesElapsed >= 1) {
          const saveStartTime = new Date(lastSaveTime);
          autoSaveTimer(taskId.value, saveStartTime, now);
          lastSaveTime = now.getTime();
          startTime.value = now;
          saveToStorage();
        }
      }
    }, 60000);
  }

  function stopAutoSave() {
    if (autoSaveIntervalId) {
      clearInterval(autoSaveIntervalId);
      autoSaveIntervalId = null;
    }
  }

  async function autoSaveTimer(taskId: number, startTime: Date, endTime: Date) {
    try {
      const routeUrl = (window as any).route('time-sessions.store');
      await router.post(routeUrl, {
        task_id: taskId,
        start_time: startTime.toISOString(),
        end_time: endTime.toISOString(),
        is_billable: true,
      }, {
        preserveState: true,
        preserveScroll: true,
        only: ['tasks'],
        onError: (errors) => {
          console.error('Failed to auto-save timer:', errors);
        },
      });
    } catch (e) {
      console.error('Failed to auto-save timer:', e);
    }
  }

  function startTimer(newTaskId: number) {
    if (isRunning.value && taskId.value) {
      stopTimer();
    }

    taskId.value = newTaskId;
    startTime.value = new Date();
    currentTime.value = new Date();
    isRunning.value = true;
    
    saveToStorage();
    startInterval();
    startAutoSave();
  }

  async function stopTimer() {
    if (!isRunning.value || !taskId.value || !startTime.value) {
      return;
    }

    const endTime = new Date();
    const currentTaskId = taskId.value;
    const currentStartTime = startTime.value;

    isRunning.value = false;
    stopInterval();
    stopAutoSave();

    taskId.value = null;
    startTime.value = null;
    clearStorage();

    try {
      const routeUrl = (window as any).route('time-sessions.store');
      await router.post(routeUrl, {
        task_id: currentTaskId,
        start_time: currentStartTime.toISOString(),
        end_time: endTime.toISOString(),
        is_billable: true,
      }, {
        preserveScroll: true,
        only: ['tasks'],
      });
    } catch (e) {
      console.error('Failed to save timer:', e);
    }
  }

  const elapsedTime = computed(() => {
    if (!isRunning.value || !startTime.value) {
      return 0;
    }
    return currentTime.value.getTime() - startTime.value.getTime();
  });

  const formattedTime = computed(() => {
    if (!isRunning.value || !startTime.value) {
      return '00:00:00.00';
    }

    const diffMs = elapsedTime.value;
    if (diffMs <= 0) {
      return '00:00:00.00';
    }

    const totalSeconds = Math.floor(diffMs / 1000);
    const centiseconds = Math.floor((diffMs % 1000) / 10);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}.${String(centiseconds).padStart(2, '0')}`;
  });

  function isTaskRunning(checkTaskId: number): boolean {
    return isRunning.value && taskId.value === checkTaskId;
  }

  if (typeof window !== 'undefined') {
    loadFromStorage();
  }

  return {
    taskId,
    startTime,
    isRunning,
    elapsedTime,
    formattedTime,
    startTimer,
    stopTimer,
    isTaskRunning,
    loadFromStorage,
  };
});
