<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import TaskCard from './Tasks/TaskCard.vue';
import CustomCheckbox from './CustomCheckbox.vue';

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
}>();

const selectedTaskIds = ref<number[]>([]);
const showConfirmModal = ref(false);
const confirmMessage = ref('');
const showFilters = ref(false);
let confirmAction: (() => void) | null = null;

const searchQuery = ref(props.filters?.search || '');
const selectedProject = ref<number | null>(props.filters?.project_id || null);
const selectedPriority = ref<string>(props.filters?.priority || '');
const selectedStatus = ref<string>(
  props.filters?.completed === true ? 'completed' 
  : props.filters?.completed === false ? 'active' 
  : ''
);

const canBulkDelete = computed(() => selectedTaskIds.value.length >= 1);

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
  router.get(route('tasks.index'), {}, {
    preserveState: true,
    preserveScroll: true,
  });
}

function openBulkDeleteModal() {
  if (!canBulkDelete.value) return;
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
  const shouldSelect = checked !== undefined ? checked : selectedTaskIds.value.length !== props.tasks.length;
  if (shouldSelect) {
    selectedTaskIds.value = props.tasks.map(t => t.id);
  } else {
    selectedTaskIds.value = [];
  }
}

const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedProject.value || selectedPriority.value || selectedStatus.value;
});

// Calculate total time from all tasks using pre-calculated values from API
const totalTime = computed(() => {
  let totalSeconds = 0;
  
  props.tasks.forEach(task => {
    // Use pre-calculated total_time_seconds from API if available
    if (task.total_time_seconds) {
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
  <div class="space-y-3 sm:space-y-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-3 sm:p-4">
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
          <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search tasks..."
            class="w-full pl-9 sm:pl-10 pr-4 py-2 sm:py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        
        <button
          @click="showFilters = !showFilters"
          class="flex items-center justify-center gap-2 px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          :class="{ 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-400': hasActiveFilters }"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
          </svg>
          <span class="hidden sm:inline">Filters</span>
          <span v-if="hasActiveFilters" class="w-2 h-2 bg-blue-500 rounded-full"></span>
        </button>
      </div>
      
      <div v-if="showFilters" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
          <div class="relative">
            <select
              v-model="selectedStatus"
              @change="applyFilters"
              class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer"
            >
              <option value="">All tasks</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Priority</label>
          <div class="relative">
            <select
              v-model="selectedPriority"
              @change="applyFilters"
              class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer"
            >
              <option value="">All priorities</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Project</label>
          <div class="relative">
            <select
              v-model="selectedProject"
              @change="applyFilters"
              class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer"
            >
              <option :value="null">All projects</option>
              <option v-for="project in projects" :key="project.id" :value="project.id">
                {{ project.name }}
              </option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
        
        <div class="sm:col-span-3 flex justify-end">
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline"
          >
            Clear all filters
          </button>
        </div>
      </div>
    </div>
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm p-3 sm:p-4 rounded-xl border border-gray-200/50 dark:border-gray-700/50">
      <div class="flex items-center gap-3 sm:gap-4">
        <div class="flex items-center gap-2">
          <CustomCheckbox
            :checked="selectedTaskIds.length === tasks.length && tasks.length > 0"
            :indeterminate="selectedTaskIds.length > 0 && selectedTaskIds.length < tasks.length"
            @change="(checked: boolean) => selectAll(checked)"
          />
          <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 cursor-pointer" @click="selectAll()">Select all</span>
        </div>

        <button
          v-if="canBulkDelete"
          @click="openBulkDeleteModal"
          class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 sm:py-1.5 bg-red-500/10 text-red-500 rounded-lg text-xs sm:text-sm font-medium hover:bg-red-500/20 transition-colors"
        >
          <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          <span class="hidden sm:inline">Delete</span> ({{ selectedTaskIds.length }})
        </button>
      </div>

      <div class="flex items-center gap-4 sm:gap-6 flex-wrap">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <div class="text-xs sm:text-sm">
            <span class="text-gray-600 dark:text-gray-400">Total time:</span>
            <span class="ml-1 font-mono font-semibold text-gray-900 dark:text-white">{{ totalTime }}</span>
          </div>
        </div>
        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
          {{ pagination.total }} task{{ pagination.total !== 1 ? 's' : '' }}
        </div>
      </div>
    </div>

    <div v-if="tasks.length > 0" class="space-y-2 sm:space-y-3">
      <TaskCard
        v-for="task in tasks"
        :key="task.id"
        :task="task"
        :selected="selectedTaskIds.includes(task.id)"
        @select="(id) => {
          const index = selectedTaskIds.indexOf(id);
          if (index > -1) {
            selectedTaskIds.splice(index, 1);
          } else {
            selectedTaskIds.push(id);
          }
        }"
        @delete="handleDelete"
        @toggle-complete="toggleTaskComplete"
      />
    </div>

    <div v-else class="flex flex-col items-center justify-center py-12 sm:py-16 bg-white/30 dark:bg-gray-800/30 rounded-xl border border-gray-200/50 dark:border-gray-700/50 border-dashed">
      <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
      </div>
      <p class="text-gray-600 dark:text-gray-300 text-base sm:text-lg font-medium">
        {{ hasActiveFilters ? 'No tasks found' : 'No tasks yet' }}
      </p>
      <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-1">
        {{ hasActiveFilters ? 'Try adjusting your filters' : 'Create your first task to get started' }}
      </p>
      <button
        v-if="hasActiveFilters"
        @click="clearFilters"
        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
      >
        Clear filters
      </button>
    </div>

    <div v-if="pagination.last_page > 1" class="flex items-center justify-center gap-2 pt-4">
      <button
        :disabled="pagination.current_page === 1"
        @click="router.get(route('tasks.index'), { ...filters, page: pagination.current_page - 1 })"
        class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :class="pagination.current_page === 1 ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
      >
        Previous
      </button>
      
      <div class="flex items-center gap-1 px-2 sm:px-4">
        <span class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">Page</span>
        <span class="text-gray-900 dark:text-white font-medium text-xs sm:text-sm">{{ pagination.current_page }}</span>
        <span class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">of</span>
        <span class="text-gray-900 dark:text-white font-medium text-xs sm:text-sm">{{ pagination.last_page }}</span>
      </div>

      <button
        :disabled="pagination.current_page === pagination.last_page"
        @click="router.get(route('tasks.index'), { ...filters, page: pagination.current_page + 1 })"
        class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :class="pagination.current_page === pagination.last_page ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
      >
        Next
      </button>
    </div>

    <Teleport to="body">
      <div
        v-if="showConfirmModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="confirmNo"
      >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
        
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1 bg-red-500" />
          
          <div class="p-5 sm:p-6">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-red-500/10 flex items-center justify-center mb-4 mx-auto">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>

            <h3 class="text-lg sm:text-xl font-semibold font-sans text-gray-900 dark:text-white text-center mb-2">
              Delete Tasks
            </h3>
            <p class="text-gray-600 dark:text-gray-400 text-center text-sm sm:text-base mb-6">
              {{ confirmMessage }}
            </p>

            <div class="flex gap-3">
              <button
                @click="confirmNo"
                class="flex-1 px-4 py-2 sm:py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm"
              >
                Cancel
              </button>
              <button
                @click="confirmYes"
                class="flex-1 px-4 py-2 sm:py-2.5 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-colors text-sm"
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
