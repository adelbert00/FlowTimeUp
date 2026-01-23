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
          } else {
            clearStorage();
          }
        }
      }
    } catch (e) {
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
