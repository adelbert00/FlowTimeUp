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
    <div class="space-y-6 animate-fade-up">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-black text-primary tracking-tighter uppercase">Time Tracker</h1>
          <p class="text-secondary text-xs font-bold uppercase tracking-widest mt-1">Focus & Productivity Hub</p>
        </div>
        
        <button
          @click="showMobileForm = !showMobileForm"
          class="xl:hidden flex items-center gap-2 px-5 py-2.5 bg-accent text-accent-text rounded-xl text-xs font-black uppercase tracking-widest hover:bg-accent-hover transition-all shadow-lg shadow-accent/20 active:scale-95"
        >
          <svg v-if="!showMobileForm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          <span>{{ showMobileForm ? 'Close' : 'New Task' }}</span>
        </button>
      </div>

      <div
        v-if="showMobileForm"
        class="xl:hidden animate-scale-in"
      >
        <div class="bg-surface-raised rounded-2xl border border-border p-6 shadow-sm">
          <TaskForm :projects="projects || []" :tags="tags || []" @submit="showMobileForm = false" />
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <div class="hidden xl:block xl:col-span-3">
          <div class="xl:sticky xl:top-20 space-y-6">
            <div class="bg-surface-raised rounded-2xl border border-border p-6 shadow-sm relative overflow-hidden">
              <div class="absolute top-0 right-0 w-24 h-24 bg-accent/5 rounded-full -mr-12 -mt-12 blur-2xl"></div>
              <h3 class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] mb-6">Create Session</h3>
              <TaskForm :projects="projects || []" :tags="tags || []" :collapsible="false" />
            </div>
          </div>
        </div>

        <div class="xl:col-span-9">
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
