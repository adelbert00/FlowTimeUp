<script setup lang="ts">
import { ref, computed } from 'vue';
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
}

interface Pagination {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

interface Filters {
  project_id?: number;
  completed?: boolean;
  priority?: string;
  search?: string;
  sort_by?: string;
  sort_order?: string;
}

const props = defineProps<{
  tasks: Task[];
  pagination: Pagination;
  filters?: Filters;
}>();

const emit = defineEmits<{
  delete: [taskId: number];
  'bulk-delete': [taskIds: number[]];
}>();

const selectedTaskIds = ref<number[]>([]);
const showConfirmModal = ref(false);
const confirmMessage = ref('');
let confirmAction: (() => void) | null = null;

const canBulkDelete = computed(() => selectedTaskIds.value.length >= 1);

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
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between bg-white/50 backdrop-blur-sm p-3 sm:p-4 rounded-xl border border-gray-200/50 sticky top-0 z-10">
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <CustomCheckbox
            :checked="selectedTaskIds.length === tasks.length && tasks.length > 0"
            :indeterminate="selectedTaskIds.length > 0 && selectedTaskIds.length < tasks.length"
            @change="(checked: boolean) => selectAll(checked)"
          />
          <span class="text-sm text-gray-600 cursor-pointer" @click="selectAll()">Select all</span>
        </div>

        <button
          v-if="canBulkDelete"
          @click="openBulkDeleteModal"
          class="flex items-center gap-2 px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-sm font-medium hover:bg-red-500/20 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          Delete ({{ selectedTaskIds.length }})
        </button>
      </div>

      <div class="text-sm text-gray-600">
        {{ pagination.total }} task{{ pagination.total !== 1 ? 's' : '' }}
      </div>
    </div>

    <div v-if="tasks.length > 0" class="space-y-3">
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

    <div v-else class="flex flex-col items-center justify-center py-16 bg-white/30 rounded-xl border border-gray-200/50 border-dashed">
      <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
      </div>
      <p class="text-gray-600 text-lg font-medium">No tasks yet</p>
      <p class="text-gray-500 text-sm mt-1">Create your first task to get started</p>
    </div>

    <div v-if="pagination.last_page > 1" class="flex items-center justify-center gap-2 pt-4">
      <button
        :disabled="pagination.current_page === 1"
        @click="router.get(route('tasks.index'), { ...filters, page: pagination.current_page - 1 })"
        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :class="pagination.current_page === 1 ? 'bg-gray-100 text-gray-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
      >
        Previous
      </button>
      
      <div class="flex items-center gap-1 px-4">
        <span class="text-gray-600 text-sm">Page</span>
        <span class="text-gray-900 font-medium">{{ pagination.current_page }}</span>
        <span class="text-gray-600 text-sm">of</span>
        <span class="text-gray-900 font-medium">{{ pagination.last_page }}</span>
      </div>

      <button
        :disabled="pagination.current_page === pagination.last_page"
        @click="router.get(route('tasks.index'), { ...filters, page: pagination.current_page + 1 })"
        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :class="pagination.current_page === pagination.last_page ? 'bg-gray-100 text-gray-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
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
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1 bg-red-500" />
          
          <div class="p-6">
            <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center mb-4 mx-auto">
              <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>

            <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">
              Delete Tasks
            </h3>
            <p class="text-gray-600 text-center mb-6">
              {{ confirmMessage }}
            </p>

            <div class="flex gap-3">
              <button
                @click="confirmNo"
                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="confirmYes"
                class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-colors"
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
