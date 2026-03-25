<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import TaskCard from './Tasks/TaskCard.vue';
import { Checkbox } from '@/Components/ui/checkbox';

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
  total_time_seconds?: number;
}

interface Pagination {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

interface Filters {
  project_id?: number;
  completed?: boolean | string;
  priority?: string;
  search?: string;
  sort_by?: string;
  sort_order?: string;
}

const props = defineProps<{
  tasks: Task[];
  pagination: Pagination;
  filters?: Filters;
  projects?: Array<{ id: number; name: string; color?: string }>;
}>();

const emit = defineEmits<{
  delete: [taskId: number];
  'bulk-delete': [taskIds: number[]];
  'bulk-update': [
    payload: { ids: number[]; project_id?: number | null; completed?: boolean },
  ];
}>();

const selectedTaskIds = ref<number[]>([]);
const showBulkProjectModal = ref(false);
const selectedBulkProjectId = ref<number | null>(null);

function openBulkProjectModal() {
  if (selectedTaskIds.value.length === 0) return;
  showBulkProjectModal.value = true;
}

function applyBulkProject() {
  emit('bulk-update', {
    ids: [...selectedTaskIds.value],
    project_id: selectedBulkProjectId.value,
  });
  showBulkProjectModal.value = false;
  selectedTaskIds.value = [];
}

function bulkMarkCompleted(completed: boolean) {
  emit('bulk-update', {
    ids: [...selectedTaskIds.value],
    completed,
  });
  selectedTaskIds.value = [];
}

function clearSelection() {
  selectedTaskIds.value = [];
}

function handleSelect(id: number) {
  const index = selectedTaskIds.value.indexOf(id);
  if (index > -1) {
    selectedTaskIds.value.splice(index, 1);
  } else {
    selectedTaskIds.value.push(id);
  }
}

const showConfirmModal = ref(false);
const confirmMessage = ref('');
const showFilters = ref(false);
let confirmAction: (() => void) | null = null;

const searchQuery = ref(props.filters?.search || '');
const selectedProject = ref<number | null>(props.filters?.project_id || null);
const selectedPriority = ref<string>(props.filters?.priority || '');
const selectedStatus = ref<string>(
  props.filters?.completed === true
    ? 'completed'
    : props.filters?.completed === false
      ? 'active'
      : ''
);

const canBulkActions = computed(() => selectedTaskIds.value.length >= 1);

let searchTimeout: number | null = null;
watch(searchQuery, (newValue) => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = window.setTimeout(() => {
    applyFilters();
  }, 300);
});

function applyFilters() {
  const filterParams: Record<string, any> = {};

  if (searchQuery.value) filterParams.search = searchQuery.value;
  if (selectedProject.value) filterParams.project_id = selectedProject.value;
  if (selectedPriority.value) filterParams.priority = selectedPriority.value;
  if (selectedStatus.value === 'completed') filterParams.completed = true;
  else if (selectedStatus.value === 'active') filterParams.completed = false;

  router.get(route('tasks.index'), filterParams, {
    preserveState: true,
    preserveScroll: true,
  });
}

function clearFilters() {
  searchQuery.value = '';
  selectedProject.value = null;
  selectedPriority.value = '';
  selectedStatus.value = '';
  router.get(
    route('tasks.index'),
    {},
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
}

function openBulkDeleteModal() {
  if (!canBulkActions.value) return;
  showConfirmModal.value = true;
  confirmMessage.value = `Are you sure you want to delete ${selectedTaskIds.value.length} task${selectedTaskIds.value.length > 1 ? 's' : ''}? This action cannot be undone.`;
  confirmAction = () => {
    emit('bulk-delete', [...selectedTaskIds.value]);
    selectedTaskIds.value = [];
  };
}

function confirmYes() {
  if (confirmAction) confirmAction();
  showConfirmModal.value = false;
  confirmAction = null;
}

function confirmNo() {
  showConfirmModal.value = false;
  confirmAction = null;
}

function handleDelete(taskId: number) {
  emit('delete', taskId);
}

function toggleTaskComplete(taskId: number) {
  router.post(
    route('tasks.toggleComplete', taskId),
    {},
    {
      preserveScroll: true,
      only: ['tasks'],
    }
  );
}

function selectAll(checked?: boolean) {
  const shouldSelect =
    checked !== undefined
      ? checked
      : selectedTaskIds.value.length !== props.tasks.length;
  if (shouldSelect) {
    selectedTaskIds.value = props.tasks.map((t) => t.id);
  } else {
    selectedTaskIds.value = [];
  }
}

const hasActiveFilters = computed(() => {
  return (
    searchQuery.value ||
    selectedProject.value ||
    selectedPriority.value ||
    selectedStatus.value
  );
});

const totalTime = computed(() => {
  let totalSeconds = 0;
  const selected = new Set(selectedTaskIds.value);
  props.tasks.forEach((task) => {
    if (selected.has(task.id) && task.total_time_seconds) {
      totalSeconds += task.total_time_seconds;
    }
  });
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;
  return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
});
</script>

<template>
  <div class="space-y-6">
    <!-- Search & Filter Bar -->
    <div
      class="bg-surface-raised rounded-2xl border border-border shadow-sm p-4 flex flex-col sm:flex-row gap-4 items-center"
    >
      <div class="flex-1 relative w-full group">
        <svg
          class="w-4 h-4 text-muted absolute left-4 top-1/2 -translate-y-1/2 group-focus-within:text-accent transition-colors"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2.5"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search sessions..."
          class="w-full pl-11 pr-4 py-2.5 bg-surface-raised border border-border rounded-xl text-sm text-primary placeholder:text-primary/70 focus:outline-none focus:ring-2 focus:ring-accent transition-all"
        />
      </div>

      <button
        @click="showFilters = !showFilters"
        class="flex items-center justify-center gap-2 px-6 py-2.5 border rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all w-full sm:w-auto"
        :class="
          hasActiveFilters
            ? 'bg-accent/10 border-accent text-accent shadow-lg shadow-accent/10'
            : 'text-secondary bg-surface-raised border-border hover:text-primary'
        "
      >
        <svg
          class="w-3.5 h-3.5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2.5"
            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
          />
        </svg>
        Filters
        <span
          v-if="hasActiveFilters"
          class="w-1.5 h-1.5 bg-accent rounded-full ml-1 animate-pulse"
        ></span>
      </button>
    </div>

    <!-- Collapsible Filters -->
    <div
      v-if="showFilters"
      class="bg-surface-raised rounded-2xl border border-border p-6 shadow-inner grid grid-cols-1 sm:grid-cols-3 gap-6 animate-fade-up"
    >
      <div class="space-y-2">
        <label
          class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]"
          >Status</label
        >
        <div class="relative">
          <select
            v-model="selectedStatus"
            @change="applyFilters"
            class="w-full cursor-pointer appearance-none bg-surface-raised px-4 py-2.5 text-sm text-primary [background-image:none] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent rounded-xl border border-border"
          >
            <option value="">All Tasks</option>
            <option value="active">Active Only</option>
            <option value="completed">Completed</option>
          </select>
          <div
            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
        </div>
      </div>

      <div class="space-y-2">
        <label
          class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]"
          >Priority</label
        >
        <div class="relative">
          <select
            v-model="selectedPriority"
            @change="applyFilters"
            class="w-full cursor-pointer appearance-none bg-surface-raised px-4 py-2.5 text-sm text-primary [background-image:none] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent rounded-xl border border-border"
          >
            <option value="">Any Priority</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
          <div
            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
        </div>
      </div>

      <div class="space-y-2">
        <label
          class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]"
          >Project</label
        >
        <div class="relative">
          <select
            v-model="selectedProject"
            @change="applyFilters"
            class="w-full cursor-pointer appearance-none bg-surface-raised px-4 py-2.5 text-sm text-primary [background-image:none] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent rounded-xl border border-border"
          >
            <option :value="null">All Projects</option>
            <option
              v-for="project in projects || []"
              :key="project.id"
              :value="project.id"
            >
              {{ project.name }}
            </option>
          </select>
          <div
            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Toolbar / Selection info -->
    <div
      class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-surface-raised p-4 px-6 rounded-2xl border border-border shadow-sm"
    >
      <div class="flex items-center gap-6">
        <div
          class="flex items-center gap-3 group cursor-pointer"
          @click="selectAll()"
        >
          <Checkbox
            :checked="
              selectedTaskIds.length === tasks.length && tasks.length > 0
            "
            :indeterminate="
              selectedTaskIds.length > 0 &&
              selectedTaskIds.length < tasks.length
            "
            @change="(checked: boolean) => selectAll(checked)"
          />
          <span
            class="text-[10px] font-black text-primary uppercase tracking-[0.1em] group-hover:text-accent transition-colors"
            >Select all sessions</span
          >
        </div>
      </div>

      <div class="flex items-center gap-10">
        <div
          class="flex items-center gap-2 bg-surface-raised px-3 py-1.5 rounded-lg border border-border group"
        >
          <svg
            class="w-4 h-4 text-accent group-hover:scale-110 transition-transform"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2.5"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <div class="flex items-center gap-2">
            <span
              class="text-[9px] font-black text-muted uppercase tracking-widest"
              >Selected Total:</span
            >
            <span
              class="font-mono font-bold text-sm text-primary tracking-tighter"
              >{{ totalTime }}</span
            >
          </div>
        </div>
        <div
          class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] bg-surface-raised px-3 py-1.5 rounded-lg border border-border/50"
        >
          {{ pagination.total }} Count
        </div>
      </div>
    </div>

    <div v-if="tasks.length > 0" class="space-y-3 pb-24">
      <TaskCard
        v-for="task in tasks"
        :key="task.id"
        :task="task"
        :selected="selectedTaskIds.includes(task.id)"
        @select="handleSelect"
        @delete="handleDelete"
        @toggle-complete="toggleTaskComplete"
      />
    </div>

    <div
      v-else
      class="flex flex-col items-center justify-center py-16 bg-surface-raised rounded-xl border border-border border-dashed shadow-sm"
    >
      <div
        class="w-16 h-16 rounded-full bg-surface-raised flex items-center justify-center mb-4 border border-border"
      >
        <svg
          class="w-8 h-8 text-muted"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          />
        </svg>
      </div>
      <p class="text-primary text-lg font-bold">
        {{ hasActiveFilters ? 'No tasks found' : 'No tasks yet' }}
      </p>
      <p class="text-secondary text-sm mt-1">
        {{
          hasActiveFilters
            ? 'Try adjusting your filters'
            : 'Create your first task to get started'
        }}
      </p>
      <button
        v-if="hasActiveFilters"
        @click="clearFilters"
        class="mt-6 px-6 py-2 bg-accent text-white rounded-xl text-sm font-bold hover:bg-accent-hover transition-colors shadow-sm"
      >
        Clear filters
      </button>
    </div>

    <div
      v-if="pagination.last_page > 1"
      class="flex items-center justify-center gap-2 pt-8"
    >
      <button
        :disabled="pagination.current_page === 1"
        @click="
          router.get(route('tasks.index'), {
            ...(props.filters || {}),
            page: pagination.current_page - 1,
          })
        "
        class="px-4 py-2 rounded-xl text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        :class="
          pagination.current_page === 1
            ? 'bg-surface-raised text-muted border border-border opacity-60'
            : 'bg-surface-raised text-primary border border-border hover:bg-border/15 shadow-sm'
        "
      >
        Previous
      </button>

      <div class="flex items-center gap-2 px-4">
        <span class="text-primary font-bold text-sm">{{
          pagination.current_page
        }}</span>
        <span class="text-muted text-sm font-medium">/</span>
        <span class="text-secondary text-sm font-medium">{{
          pagination.last_page
        }}</span>
      </div>

      <button
        :disabled="pagination.current_page === pagination.last_page"
        @click="
          router.get(route('tasks.index'), {
            ...(props.filters || {}),
            page: pagination.current_page + 1,
          })
        "
        class="px-4 py-2 rounded-xl text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        :class="
          pagination.current_page === pagination.last_page
            ? 'bg-surface-raised text-muted border border-border opacity-60'
            : 'bg-surface-raised text-primary border border-border hover:bg-border/15 shadow-sm'
        "
      >
        Next
      </button>
    </div>

    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-full opacity-0"
      >
        <div
          v-if="selectedTaskIds.length > 0"
          class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] w-[min(calc(100vw-2rem),600px)]"
        >
          <div
            class="bg-[#121218] shadow-2xl rounded-2xl border border-white/10 p-4 flex items-center justify-between gap-4"
          >
            <div class="flex items-center gap-3">
              <div
                class="w-8 h-8 rounded-lg bg-accent text-white flex items-center justify-center font-bold text-sm"
              >
                {{ selectedTaskIds.length }}
              </div>
              <div class="hidden sm:block">
                <p class="text-white text-sm font-bold">Tasks Selected</p>
                <p
                  class="text-white/40 text-[10px] uppercase tracking-wider font-bold"
                >
                  Bulk Actions
                </p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button
                @click="bulkMarkCompleted(true)"
                class="flex items-center gap-2 px-3 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl text-xs font-bold transition-colors border border-emerald-500/10"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
                <span class="hidden md:inline">Complete</span>
              </button>

              <button
                @click="bulkMarkCompleted(false)"
                class="flex items-center gap-2 px-3 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-xl text-xs font-bold transition-colors border border-amber-500/10"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
                <span class="hidden md:inline">Active</span>
              </button>

              <button
                @click="openBulkProjectModal"
                class="flex items-center gap-2 px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded-xl text-xs font-bold transition-colors border border-white/10"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                  />
                </svg>
                <span class="hidden md:inline">Project</span>
              </button>

              <button
                @click="openBulkDeleteModal"
                class="flex items-center gap-2 px-3 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-xl text-xs font-bold transition-colors border border-red-500/10"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  />
                </svg>
                <span class="hidden md:inline">Delete</span>
              </button>

              <div class="w-px h-6 bg-white/10 mx-1"></div>

              <button
                @click="clearSelection"
                class="p-2 text-white/40 hover:text-white transition-colors"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <div
        v-if="showBulkProjectModal"
        class="fixed inset-0 z-[70] flex items-center justify-center p-4"
        @click.self="showBulkProjectModal = false"
      >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
        <div
          class="relative bg-surface-raised rounded-2xl shadow-2xl max-w-sm w-full border border-border p-6 animate-scale-in"
        >
          <h3 class="text-lg font-bold text-primary mb-4">Move to Project</h3>
          <div
            class="space-y-2 mb-6 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar"
          >
            <div
              v-for="project in projects || []"
              :key="project.id"
              @click="selectedBulkProjectId = project.id"
              class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
              :class="
                selectedBulkProjectId === project.id
                  ? 'border-accent bg-accent-subtle shadow-sm'
                  : 'border-border hover:border-accent/30 hover:bg-border/15'
              "
            >
              <div
                class="w-3 h-3 rounded-full"
                :style="{ backgroundColor: project.color || '#6366f1' }"
              ></div>
              <span class="text-sm font-medium text-primary">{{
                project.name
              }}</span>
              <svg
                v-if="selectedBulkProjectId === project.id"
                class="w-4 h-4 text-accent ml-auto"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div
              @click="selectedBulkProjectId = null"
              class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
              :class="
                selectedBulkProjectId === null
                  ? 'border-accent bg-accent-subtle shadow-sm'
                  : 'border-border hover:border-accent/30 hover:bg-border/15'
              "
            >
              <div class="w-3 h-3 rounded-full bg-border-strong"></div>
              <span class="text-sm font-medium text-primary">No Project</span>
              <svg
                v-if="selectedBulkProjectId === null"
                class="w-4 h-4 text-accent ml-auto"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
          <div class="flex gap-3">
            <button
              @click="showBulkProjectModal = false"
              class="flex-1 px-4 py-2 bg-surface-raised text-secondary rounded-xl font-bold text-sm border border-border hover:bg-border transition-colors"
            >
              Cancel
            </button>
            <button
              @click="applyBulkProject"
              class="flex-1 px-4 py-2 bg-accent text-white rounded-xl font-bold text-sm hover:bg-accent-hover transition-colors shadow-sm"
            >
              Apply
            </button>
          </div>
        </div>
      </div>

      <div
        v-if="showConfirmModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="confirmNo"
      >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />

        <div
          class="relative bg-surface-raised rounded-2xl shadow-2xl max-w-md w-full border border-border overflow-hidden animate-scale-in"
        >
          <div class="absolute top-0 left-0 right-0 h-1 bg-danger" />

          <div class="p-6">
            <div
              class="w-12 h-12 rounded-full bg-danger/10 flex items-center justify-center mb-4 mx-auto"
            >
              <svg
                class="w-6 h-6 text-danger"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
            </div>

            <h3 class="text-xl font-bold text-primary text-center mb-2">
              Delete Tasks
            </h3>
            <p class="text-secondary text-center text-sm mb-6">
              {{ confirmMessage }}
            </p>

            <div class="flex gap-3">
              <button
                @click="confirmNo"
                class="flex-1 px-4 py-2 bg-surface-raised text-secondary rounded-xl font-bold text-sm border border-border hover:bg-border transition-colors"
              >
                Cancel
              </button>
              <button
                @click="confirmYes"
                class="flex-1 px-4 py-2 bg-danger text-white rounded-xl font-bold text-sm hover:bg-danger/90 transition-colors shadow-sm"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
