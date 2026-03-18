<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

interface Session {
  id: number;
  task: string;
  project: string;
  tags: string;
  start_time: string;
  end_time: string;
  duration: string;
  duration_seconds: number;
  notes: string;
}

interface Summary {
  total_time: string;
  total_seconds: number;
  total_sessions: number;
  by_project: Array<{ name: string; duration: string; seconds: number }>;
  by_task: Array<{ name: string; duration: string; seconds: number }>;
  by_date: Array<{ date: string; duration: string; seconds: number }>;
}

const props = defineProps<{
  sessions: Session[];
  summary: Summary;
  filters: {
    project_id?: number;
    tag_id?: number;
    start_date?: string;
    end_date?: string;
    task_id?: number;
  };
}>();

const handlePrint = () => {
  window.print();
};

onMounted(() => {
  setTimeout(() => {
    handlePrint();
  }, 500);
});
</script>

<template>
  <Head title="Time Tracking Report" />

  <div class="min-h-screen bg-white p-8 print:p-4">
    <div class="max-w-4xl mx-auto text-black">
      <div class="mb-8 print:mb-6">
        <h1 class="text-3xl font-bold mb-2">Time Tracking Report</h1>
        <p class="text-gray-600">
          Generated on {{ new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
        </p>
        <div v-if="filters.start_date || filters.end_date" class="mt-2 text-sm text-gray-500">
          Period: {{ filters.start_date || 'Start' }} to {{ filters.end_date || 'End' }}
        </div>
      </div>

      <div class="bg-gray-50 rounded-lg p-6 mb-8 print:mb-6 print:break-inside-avoid">
        <h2 class="text-xl font-semibold mb-4">Summary</h2>
        <div class="grid grid-cols-3 gap-4">
          <div>
            <p class="text-sm text-gray-600">Total Time</p>
            <p class="text-2xl font-bold">{{ summary.total_time }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Sessions</p>
            <p class="text-2xl font-bold">{{ summary.total_sessions }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Average per Session</p>
            <p class="text-2xl font-bold">
              {{ summary.total_sessions > 0 
                ? Math.floor(summary.total_seconds / summary.total_sessions / 3600) + ':' + 
                  String(Math.floor((summary.total_seconds / summary.total_sessions % 3600) / 60)).padStart(2, '0') + ':' + 
                  String(Math.floor((summary.total_seconds / summary.total_sessions) % 60)).padStart(2, '0')
                : '00:00:00' }}
            </p>
          </div>
        </div>
      </div>

      <div v-if="summary.by_project.length > 0" class="mb-8 print:mb-6 print:break-inside-avoid">
        <h2 class="text-xl font-semibold mb-4">Time by Project</h2>
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Project</th>
              <th class="border border-gray-300 px-4 py-2 text-right text-sm font-semibold">Time</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in summary.by_project" :key="item.name" class="hover:bg-gray-50">
              <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ item.name }}</td>
              <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700 text-right font-mono">{{ item.duration }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mb-8 print:mb-6 print:break-inside-avoid">
        <h2 class="text-xl font-semibold mb-4">Time Sessions</h2>
        <table class="w-full border-collapse text-sm">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold">Date</th>
              <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold">Task</th>
              <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold">Project</th>
              <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold">Tags</th>
              <th class="border border-gray-300 px-3 py-2 text-right text-xs font-semibold">Duration</th>
              <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold">Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="session in sessions" :key="session.id" class="hover:bg-gray-50 print:hover:bg-transparent">
              <td class="border border-gray-300 px-3 py-2 text-xs text-gray-700">{{ session.start_time.split(' ')[0] }}</td>
              <td class="border border-gray-300 px-3 py-2 text-xs text-gray-700 font-medium">{{ session.task }}</td>
              <td class="border border-gray-300 px-3 py-2 text-xs text-gray-700">{{ session.project }}</td>
              <td class="border border-gray-300 px-3 py-2 text-xs text-gray-700">{{ session.tags || '-' }}</td>
              <td class="border border-gray-300 px-3 py-2 text-xs text-gray-700 text-right font-mono">{{ session.duration }}</td>
              <td class="border border-gray-300 px-3 py-2 text-xs text-gray-700">{{ session.notes || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="text-center print:hidden">
        <button
          @click="handlePrint"
          class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
        >
          Print / Save as PDF
        </button>
      </div>
    </div>
  </div>
</template>

<style>
@media print {
  @page {
    margin: 1cm;
  }
  body {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
}
</style>
