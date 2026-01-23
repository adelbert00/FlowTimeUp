<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import TaskForm from '@/Components/TaskForm.vue';
import TaskList from '@/Components/TaskList.vue';
import { ref } from 'vue';

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

interface Project {
  id: number;
  name: string;
  color?: string;
}

interface Props {
  tasks: {
    data: Task[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  projects?: Project[];
  tags?: Array<{ id: number; name: string }>;
  filters?: {
    project_id?: number;
    completed?: boolean;
    priority?: string;
    search?: string;
    sort_by?: string;
    sort_order?: string;
  };
}

const props = defineProps<Props>();
const showMobileForm = ref(false);

const handleDelete = (taskId: number) => {
  router.delete(route('tasks.destroy', taskId), {
    preserveScroll: true,
  });
};

const handleBulkDelete = (taskIds: number[]) => {
  taskIds.forEach((id, index) => {
    setTimeout(() => {
      router.delete(route('tasks.destroy', id), {
        preserveScroll: true,
        only: index === taskIds.length - 1 ? ['tasks'] : [],
      });
    }, index * 100);
  });
};
</script>

<template>
  <Head title="Tasks" />

  <MainLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
      <div class="container mx-auto px-4 sm:px-6 md:px-10 lg:px-8 py-6 sm:py-8 max-w-7xl pt-20 sm:pt-24 md:pt-28 xl:pt-8">
        <div class="flex items-center justify-between mb-6 sm:mb-8">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold font-sans text-gray-900 dark:text-white mb-1 sm:mb-2">Time Tracker</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">Track time and manage your tasks efficiently</p>
          </div>
          
          <button
            @click="showMobileForm = !showMobileForm"
            class="xl:hidden flex items-center gap-2 px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
          >
            <svg v-if="!showMobileForm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="hidden sm:inline">{{ showMobileForm ? 'Close' : 'New Task' }}</span>
          </button>
        </div>

        <div
          v-if="showMobileForm"
          class="xl:hidden mb-6"
        >
          <TaskForm :projects="projects || []" :tags="tags || []" @submit="showMobileForm = false" />
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6">
          <div class="hidden xl:block xl:col-span-4 2xl:col-span-3">
            <div class="xl:sticky xl:top-8">
              <TaskForm :projects="projects || []" :tags="tags || []" :collapsible="true" />
            </div>
          </div>

          <div class="xl:col-span-8 2xl:col-span-9">
            <TaskList
              :tasks="tasks.data"
              :pagination="{
                current_page: tasks.current_page,
                last_page: tasks.last_page,
                per_page: tasks.per_page,
                total: tasks.total,
              }"
              :filters="filters"
              :projects="projects"
              @delete="handleDelete"
              @bulk-delete="handleBulkDelete"
            />
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
