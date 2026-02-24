<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import duration from 'dayjs/plugin/duration';
import CustomCheckbox from '@/Components/CustomCheckbox.vue';
import { useTimerStore } from '@/stores/timer';

dayjs.extend(duration);

const timerStore = useTimerStore();

interface TimeSession {
  id: number;
  start_time: string;
  end_time?: string;
  is_billable?: boolean;
  billable_rate?: number | null;
  description?: string;
  duration?: number;
  earnings?: number | null;
}

interface Task {
  id: number;
  title: string;
  description?: string;
  project_id?: number | null;
  due_date?: string;
  priority?: 'low' | 'medium' | 'high';
  hourly_rate?: number | null;
  currency?: string;
  completed: boolean;
  is_recurring?: boolean;
  recurrence_type?: string;
  project?: { id: number; name: string; color?: string };
  tags?: Array<{ id: number; name: string }>;
  time_sessions?: TimeSession[];
  total_time?: string;
  billable_time?: string;
  earnings?: number | null;
}

const props = defineProps<{ task: Task; selected: boolean }>();
const emit = defineEmits<{ select: [id: number]; delete: [id: number]; 'toggle-complete': [id: number] }>();

const showSessions = ref(false);
const showMobileActions = ref(false);
const showManualEntry = ref(false);
const editingSession = ref<TimeSession | null>(null);

const isRunning = computed(() => timerStore.isTaskRunning(props.task.id));

const formattedTime = computed(() => {
  if (isRunning.value) {
    return timerStore.formattedTime;
  }
  return props.task.total_time ? props.task.total_time : '00:00:00';
});

const manualForm = useForm({
  task_id: props.task.id,
  start_time: '',
  end_time: '',
  is_billable: true,
  billable_rate: null as number | null,
  description: '',
});

const editForm = useForm({
  start_time: '',
  end_time: '',
  is_billable: true as boolean,
  billable_rate: null as number | null,
  description: '',
});

const priorityConfig = computed(() => {
  switch (props.task.priority) {
    case 'high': return { bg: 'bg-red-500', text: 'text-white', label: 'High' };
    case 'medium': return { bg: 'bg-amber-500', text: 'text-white', label: 'Medium' };
    case 'low': return { bg: 'bg-emerald-500', text: 'text-white', label: 'Low' };
    default: return { bg: 'bg-gray-400', text: 'text-white', label: 'None' };
  }
});

const projectColor = computed(() => props.task.project?.color || '#6366f1');

function startTimer() {
  timerStore.startTimer(props.task.id);
}

function stopTimer() {
  timerStore.stopTimer();
}

function submitManualEntry() {
  manualForm.task_id = props.task.id;
  manualForm.post(route('time-sessions.store'), {
    preserveScroll: true,
    only: ['tasks'],
    onSuccess: () => { manualForm.reset(); showManualEntry.value = false; },
  });
}

function startEditSession(session: TimeSession) {
  editingSession.value = session;
  editForm.start_time = dayjs(session.start_time).format('YYYY-MM-DDTHH:mm');
  editForm.end_time = session.end_time ? dayjs(session.end_time).format('YYYY-MM-DDTHH:mm') : '';
  editForm.is_billable = session.is_billable ?? true;
  editForm.billable_rate = session.billable_rate ?? null;
  editForm.description = session.description ?? '';
}

function updateSession() {
  if (!editingSession.value) return;
  editForm.put(route('time-sessions.update', editingSession.value.id), {
    preserveScroll: true,
    only: ['tasks'],
    onSuccess: () => { editingSession.value = null; editForm.reset(); },
  });
}

function deleteSession(sessionId: number) {
  if (!confirm('Delete this time entry?')) return;
  router.delete(route('time-sessions.destroy', sessionId), { preserveScroll: true, only: ['tasks'] });
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

function formatSessionTime(date: string) { return dayjs(date).format('HH:mm'); }
function formatSessionDate(date: string) { return dayjs(date).format('MMM D'); }
</script>

<template>
  <div
    class="group relative rounded-xl border shadow-sm overflow-hidden transition-all duration-200 bg-white dark:bg-gray-800"
    :class="{ 
      'ring-2 ring-blue-500 border-blue-500': selected && !task.completed,
      'bg-emerald-50/80 dark:bg-emerald-900/20 border-emerald-300/50': task.completed,
      'border-gray-200 dark:border-gray-700 hover:border-blue-500/50': !selected && !task.completed
    }"
    @click="showMobileActions = false"
  >
    <div v-if="isRunning" class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-cyan-500 to-emerald-500 animate-pulse" />
    <div v-if="task.is_recurring" class="absolute top-0 right-0 px-2 py-0.5 bg-purple-500 text-white text-[10px] font-medium rounded-bl">↻ {{ task.recurrence_type }}</div>

    <div class="p-3 sm:p-4">
      <div class="flex items-start gap-2 sm:gap-3">
        <div class="mt-0.5 sm:mt-1 flex-shrink-0">
          <CustomCheckbox :checked="selected" @change="emit('select', task.id)" />
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-start gap-2 flex-wrap mb-1">
            <h3 class="text-sm sm:text-base font-medium text-gray-900 dark:text-white break-words" :class="{ 'line-through text-gray-500': task.completed }">{{ task.title }}</h3>
          </div>

          <p v-if="task.description" class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ task.description }}</p>

          <div v-if="task.project" class="mb-2">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium" :style="{ backgroundColor: projectColor + '20', color: projectColor }">
              <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: projectColor }"/>
              {{ task.project.name }}
            </span>
          </div>

          <div v-if="task.tags && task.tags.length > 0" class="flex flex-wrap gap-1 mb-2">
            <span v-for="tag in task.tags.slice(0, 3)" :key="tag.id" class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-[10px]">#{{ tag.name }}</span>
            <span v-if="task.tags.length > 3" class="px-1.5 py-0.5 text-gray-500 text-[10px]">+{{ task.tags.length - 3 }}</span>
          </div>

          <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-3 bg-gray-50/50 dark:bg-gray-700/30 rounded-lg border border-gray-200/50 dark:border-gray-600/50">
            <div class="flex-1 min-w-0">
              <div class="font-mono text-base sm:text-xl font-bold tracking-wider" :class="isRunning ? 'text-emerald-500' : 'text-gray-600 dark:text-gray-300'">{{ formattedTime }}</div>
              <div class="flex items-center gap-2 mt-0.5 flex-wrap text-xs">
                <span v-if="isRunning && task.total_time" class="text-gray-500">Total: {{ task.total_time }}</span>
                <span v-if="task.billable_time && task.billable_time !== task.total_time" class="text-blue-500">Billable: {{ task.billable_time }}</span>
                <span v-if="task.earnings" class="text-emerald-600 font-medium">{{ task.currency || 'USD' }} {{ task.earnings.toFixed(2) }}</span>
              </div>
            </div>

            <div class="flex items-center gap-1 flex-shrink-0">
              <button @click.stop="showManualEntry = !showManualEntry" class="p-2 rounded-full text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors" aria-label="Add manual time entry">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              </button>
              <button @click.stop="isRunning ? stopTimer() : startTimer()" class="relative w-10 h-10 rounded-full flex items-center justify-center transition-all" :class="isRunning ? 'bg-red-500 hover:bg-red-600' : 'bg-emerald-500 hover:bg-emerald-600'" :aria-label="isRunning ? 'Stop timer' : 'Start timer'">
                <svg v-if="!isRunning" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                <svg v-else class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/></svg>
                <span v-if="isRunning" class="absolute inset-0 rounded-full animate-ping bg-red-500/30" />
              </button>
            </div>
          </div>

          <div v-if="showManualEntry" class="mt-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Add Time Manually
              </h4>
              <button @click="showManualEntry = false" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" aria-label="Close manual entry form">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Start</label>
                <input v-model="manualForm.start_time" type="datetime-local" class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">End</label>
                <input v-model="manualForm.end_time" type="datetime-local" class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
              </div>
            </div>
            <div class="flex items-center gap-4 mb-3 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="manualForm.is_billable" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span class="text-sm text-gray-700 dark:text-gray-300">Billable</span>
              </label>
              <div v-if="manualForm.is_billable" class="flex-1">
                <input v-model.number="manualForm.billable_rate" type="number" step="0.01" placeholder="Hourly rate (optional)" class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
              </div>
            </div>
            <div class="flex gap-2">
              <button @click="showManualEntry = false" class="flex-1 px-4 py-2 text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancel</button>
              <button @click="submitManualEntry" :disabled="manualForm.processing" class="flex-1 px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors flex items-center justify-center gap-2">
                <svg v-if="manualForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                {{ manualForm.processing ? 'Adding...' : 'Add Entry' }}
              </button>
            </div>
          </div>

          <div v-if="task.priority || task.due_date" class="flex items-center gap-2 mt-2 flex-wrap">
            <span v-if="task.priority" class="inline-flex px-1.5 py-0.5 rounded-full text-[10px] font-medium" :class="[priorityConfig.bg, priorityConfig.text]">{{ priorityConfig.label }}</span>
            <div v-if="task.due_date" class="flex items-center gap-1 text-[10px] text-gray-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              {{ dayjs(task.due_date).format('MMM D, YYYY') }}
            </div>
          </div>
        </div>

        <div class="hidden sm:flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
          <button @click.stop="emit('toggle-complete', task.id)" class="p-1.5 rounded-lg transition-colors" :class="task.completed ? 'text-emerald-400 bg-emerald-500/10' : 'text-gray-600 hover:text-emerald-400 hover:bg-gray-100 dark:hover:bg-gray-700'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </button>
          <button @click.stop="router.visit(route('tasks.edit', task.id))" class="p-1.5 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </button>
          <button @click.stop="emit('delete', task.id)" class="p-1.5 rounded-lg text-gray-600 hover:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>

        <button @click.stop="showMobileActions = !showMobileActions" class="sm:hidden p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
        </button>
      </div>

      <div v-if="showMobileActions" class="sm:hidden mt-2 flex items-center gap-2 justify-end border-t border-gray-100 dark:border-gray-700 pt-2">
        <button @click.stop="emit('toggle-complete', task.id); showMobileActions = false" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium" :class="task.completed ? 'text-amber-600 bg-amber-50' : 'text-emerald-600 bg-emerald-50'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          {{ task.completed ? 'Undo' : 'Done' }}
        </button>
        <button @click.stop="router.visit(route('tasks.edit', task.id))" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-blue-600 bg-blue-50">Edit</button>
        <button @click.stop="emit('delete', task.id); showMobileActions = false" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 bg-red-50">Delete</button>
      </div>

      <div v-if="task.time_sessions && task.time_sessions.length > 0" class="mt-2 border-t border-gray-200/50 dark:border-gray-700/50 pt-2">
        <button @click.stop="showSessions = !showSessions" class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 w-full">
          <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-90': showSessions }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          {{ task.time_sessions.length }} time {{ task.time_sessions.length === 1 ? 'entry' : 'entries' }}
        </button>

        <div v-if="showSessions" class="mt-2 space-y-1">
          <div v-for="session in task.time_sessions" :key="session.id" class="flex items-center justify-between py-1.5 px-2 bg-gray-50/50 dark:bg-gray-700/30 rounded-lg text-xs group/session">
            <div class="flex items-center gap-2 min-w-0">
              <span v-if="!session.is_billable" class="w-1.5 h-1.5 rounded-full bg-gray-400" title="Non-billable" />
              <span v-else class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Billable" />
              <span class="text-gray-500 hidden sm:inline">{{ formatSessionDate(session.start_time) }}</span>
              <span class="text-gray-600 dark:text-gray-300">{{ formatSessionTime(session.start_time) }} - {{ session.end_time ? formatSessionTime(session.end_time) : 'Running' }}</span>
              <span v-if="session.earnings" class="text-emerald-600 font-medium">${{ session.earnings.toFixed(2) }}</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="font-mono text-gray-700 dark:text-gray-300">{{ formatDuration(session.start_time, session.end_time) }}</span>
              <div class="hidden group-hover/session:flex items-center gap-0.5 ml-1">
                <button @click.stop="startEditSession(session)" class="p-1 text-gray-400 hover:text-blue-500">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button @click.stop="deleteSession(session.id)" class="p-1 text-gray-400 hover:text-red-500">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="editingSession" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="editingSession = null">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full p-6">
          <h3 class="text-lg font-semibold font-sans text-gray-900 dark:text-white mb-4">Edit Time Entry</h3>
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Start Time</label>
                <input v-model="editForm.start_time" type="datetime-local" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">End Time</label>
                <input v-model="editForm.end_time" type="datetime-local" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
              </div>
            </div>
            <div class="flex items-center gap-4">
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="editForm.is_billable" class="w-4 h-4 rounded" />
                <span class="text-sm text-gray-700 dark:text-gray-300">Billable</span>
              </label>
              <div v-if="editForm.is_billable" class="flex-1">
                <input v-model.number="editForm.billable_rate" type="number" step="0.01" placeholder="Custom rate" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
              <input v-model="editForm.description" type="text" placeholder="What did you work on?" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
            </div>
          </div>
          <div class="flex gap-3 mt-6">
            <button @click="editingSession = null" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
            <button @click="updateSession" :disabled="editForm.processing" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50">Save</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
