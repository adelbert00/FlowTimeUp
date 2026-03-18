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
  router.delete(route('bulk.tasks.destroy'), {
    data: { ids: taskIds },
    preserveScroll: true,
  });
};

const handleBulkUpdate = (payload: { ids: number[], project_id?: number | null, completed?: boolean }) => {
  router.patch(route('bulk.tasks.update'), payload, {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Tasks" />

  <MainLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold font-sans text-primary">Time Tracker</h1>
          <p class="text-secondary text-sm">Track time and manage your tasks efficiently</p>
        </div>
        
        <button
          @click="showMobileForm = !showMobileForm"
          class="xl:hidden flex items-center gap-2 px-4 py-2 bg-accent text-white rounded-lg text-sm font-semibold hover:bg-accent-hover transition-colors shadow-sm"
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
        class="xl:hidden animate-fade-up"
      >
        <TaskForm :projects="projects || []" :tags="tags || []" @submit="showMobileForm = false" />
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="hidden xl:block xl:col-span-4 2xl:col-span-3">
          <div class="xl:sticky xl:top-24">
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
            @bulk-update="handleBulkUpdate"
          />
        </div>
      </div>
    </div>
  </MainLayout>
</template>
