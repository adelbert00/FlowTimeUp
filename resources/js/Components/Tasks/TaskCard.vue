<script setup lang="ts">
import { ref, computed, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import duration from 'dayjs/plugin/duration';
import CustomCheckbox from '@/Components/CustomCheckbox.vue';

dayjs.extend(duration);

interface Task {
  id: number;
  title: string;
  description?: string;
  project_id?: number | null;
  due_date?: string;
  priority?: 'low' | 'medium' | 'high';
  completed: boolean;
  project?: {
    id: number;
    name: string;
    color?: string;
  };
  tags?: Array<{ id: number; name: string }>;
  time_sessions?: Array<{
    id: number;
    start_time: string;
    end_time?: string;
  }>;
  total_time?: string;
}

const props = defineProps<{
  task: Task;
  selected: boolean;
}>();

const emit = defineEmits<{
  select: [id: number];
  delete: [id: number];
  'toggle-complete': [id: number];
}>();

const isRunning = ref(false);
const startTime = ref<Date | null>(null);
const currentTime = ref<Date>(new Date());
const showSessions = ref(false);

let timerInterval: number | null = null;

const formattedTime = computed(() => {
  if (!isRunning.value || !startTime.value) {
    return '00:00:00.00';
  }

  const diffMs = currentTime.value.getTime() - startTime.value.getTime();
  if (diffMs <= 0) return '00:00:00.00';

  const totalSeconds = Math.floor(diffMs / 1000);
  const centiseconds = Math.floor((diffMs % 1000) / 10);
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;

  return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}.${String(centiseconds).padStart(2, '0')}`;
});

const priorityConfig = computed(() => {
  switch (props.task.priority) {
    case 'high':
      return { bg: 'bg-red-500', text: 'text-white', label: 'High' };
    case 'medium':
      return { bg: 'bg-amber-500', text: 'text-white', label: 'Medium' };
    case 'low':
      return { bg: 'bg-emerald-500', text: 'text-white', label: 'Low' };
    default:
      return { bg: 'bg-gray-400', text: 'text-white', label: 'None' };
  }
});

const projectColor = computed(() => props.task.project!.color);

function startTimer() {
  if (isRunning.value) return;
  
  isRunning.value = true;
  startTime.value = new Date();
  currentTime.value = new Date();
  
  timerInterval = window.setInterval(() => {
    if (isRunning.value && startTime.value) {
      currentTime.value = new Date();
    }
  }, 10);
}

function stopTimer() {
  if (!isRunning.value || !startTime.value) return;

  const endTime = new Date();
  isRunning.value = false;

  if (timerInterval) {
    clearInterval(timerInterval);
    timerInterval = null;
  }

  router.post(
    route('time-sessions.store'),
    {
      task_id: props.task.id,
      start_time: startTime.value.toISOString(),
      end_time: endTime.toISOString(),
    },
    {
      preserveScroll: true,
      only: ['tasks'],
      onSuccess: () => {
        startTime.value = null;
        currentTime.value = new Date();
      },
    }
  );
}

function formatDuration(start: string, end?: string): string {
  const startMs = new Date(start).getTime();
  const endMs = end ? new Date(end).getTime() : Date.now();
  const diffMs = endMs - startMs;
  
  const totalSeconds = Math.floor(diffMs / 1000);
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;
  
  return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

function formatSessionTime(date: string) {
  return dayjs(date).format('HH:mm:ss');
}

function formatSessionDate(date: string) {
  return dayjs(date).format('ddd, MMM D');
}

onBeforeUnmount(() => {
  if (timerInterval) {
    clearInterval(timerInterval);
  }
});
</script>

<template>
  <div
    class="group relative bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-200 hover:border-blue-500/50 hover:shadow-md"
    :class="{ 
      'ring-2 ring-blue-500 border-blue-500': selected,
      'bg-emerald-50/50 border-emerald-200': task.completed
    }"
  >
    <div
      v-if="isRunning"
      class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-cyan-500 to-emerald-500 animate-pulse"
    />

    <div class="p-3 sm:p-4">
      <!-- Top row: checkbox, title, actions -->
      <div class="flex items-start gap-3">
        <div class="mt-1">
          <CustomCheckbox
            :checked="selected"
            @change="emit('select', task.id)"
          />
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap mb-1">
            <h3
              class="text-base font-medium text-gray-900 truncate"
              :class="{ 'line-through text-gray-500': task.completed }"
            >
              {{ task.title }}
            </h3>
            
            <span
              v-if="task.project"
              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
              :style="{ backgroundColor: projectColor + '20', color: projectColor }"
            >
              <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: projectColor }"/>
              {{ task.project.name }}
            </span>
          </div>

          <p v-if="task.description" class="text-sm text-gray-600 mb-2 line-clamp-2">
            {{ task.description }}
          </p>

          <div v-if="task.tags!.length > 0" class="flex flex-wrap gap-1 mb-3">
            <span
              v-for="tag in task.tags"
              :key="tag.id"
              class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs"
            >
              #{{ tag.name }}
            </span>
          </div>

          <div class="flex items-center gap-3 sm:gap-4 p-2 sm:p-3 bg-gray-50/50 rounded-lg border border-gray-200/50">
            <div class="flex-1 min-w-0">
              <div
                class="font-mono text-lg sm:text-2xl font-bold tracking-wider"
                :class="isRunning ? 'text-emerald-400' : 'text-gray-600'"
              >
                {{ formattedTime }}
              </div>
              <div v-if="task.total_time" class="text-sm sm:text-base font-medium text-gray-600 mt-1.5">
                Total tracked: {{ task.total_time }}
              </div>
            </div>

            <button
              @click="isRunning ? stopTimer() : startTimer()"
              class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white flex-shrink-0"
              :class="isRunning 
                ? 'bg-red-500 hover:bg-red-600 focus:ring-red-500' 
                : 'bg-emerald-500 hover:bg-emerald-600 focus:ring-emerald-500'"
            >
              <svg v-if="!isRunning" class="w-6 h-6 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
              </svg>
              <svg v-else class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
              </svg>
              
              <span
                v-if="isRunning"
                class="absolute inset-0 rounded-full animate-ping bg-red-500/30"
              />
            </button>
          </div>

          <div v-if="task.priority || task.due_date" class="flex items-center gap-3 mt-2 flex-wrap">
            <span
              v-if="task.priority"
              class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
              :class="[priorityConfig.bg, priorityConfig.text]"
            >
              {{ priorityConfig.label }}
            </span>
            
            <div v-if="task.due_date" class="flex items-center gap-1 text-xs text-gray-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Due: {{ dayjs(task.due_date).format('MMM D, YYYY') }}
            </div>
          </div>
        </div>

        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
          <button
            @click="emit('toggle-complete', task.id)"
            class="p-2 rounded-lg transition-colors"
            :class="task.completed ? 'text-emerald-400 bg-emerald-500/10' : 'text-gray-600 hover:text-emerald-400 hover:bg-gray-100'"
            :title="task.completed ? 'Mark incomplete' : 'Mark complete'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </button>

          <button
            @click="router.visit(route('tasks.edit', task.id))"
            class="p-2 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-gray-100 transition-colors"
            title="Edit task"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>

          <button
            @click="emit('delete', task.id)"
            class="p-2 rounded-lg text-gray-600 hover:text-red-400 hover:bg-gray-100 transition-colors"
            title="Delete task"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <div v-if="task.time_sessions!.length > 0" class="mt-3 border-t border-gray-200/50 pt-3">
        <button
          @click="showSessions = !showSessions"
          class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-700 transition-colors w-full"
        >
          <svg
            class="w-4 h-4 transition-transform"
            :class="{ 'rotate-90': showSessions }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
          <span>{{ task.time_sessions!.length }} time {{ task.time_sessions!.length === 1 ? 'entry' : 'entries' }}</span>
        </button>

        <div v-if="showSessions" class="mt-2 space-y-1">
          <div
            v-for="session in task.time_sessions!"
            :key="session.id"
            class="flex items-center justify-between py-2 px-3 bg-gray-50/30 rounded-lg text-sm"
          >
            <div class="flex items-center gap-3">
              <span class="text-gray-500">{{ formatSessionDate(session.start_time) }}</span>
              <span class="text-gray-600">
                {{ formatSessionTime(session.start_time) }} - {{ session.end_time ? formatSessionTime(session.end_time) : 'Running' }}
              </span>
            </div>
            <span class="font-mono text-gray-700">
              {{ formatDuration(session.start_time, session.end_time) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
