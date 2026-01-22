<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, computed } from 'vue';
import CustomDatePicker from '@/Components/CustomDatePicker.vue';

interface Project {
  id: number;
  name: string;
  color?: string;
}

interface Tag {
  id: number;
  name: string;
}

const props = defineProps<{
  projects?: Project[];
  tags?: Tag[];
}>();

const projectId = ref<number | null>(null);
const tagId = ref<number | null>(null);
const taskId = ref<number | null>(null);
const startDate = ref<string>('');
const endDate = ref<string>('');

// Set default dates (last 30 days)
const today = new Date();
const thirtyDaysAgo = new Date();
thirtyDaysAgo.setDate(today.getDate() - 30);
startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
endDate.value = today.toISOString().split('T')[0];

const exportCsv = () => {
  const params = new URLSearchParams();
  if (projectId.value) params.append('project_id', projectId.value.toString());
  if (tagId.value) params.append('tag_id', tagId.value.toString());
  if (taskId.value) params.append('task_id', taskId.value.toString());
  if (startDate.value) params.append('start_date', startDate.value);
  if (endDate.value) params.append('end_date', endDate.value);
  params.append('format', 'csv');

  window.location.href = route('reports.export') + '?' + params.toString();
};

const exportPdf = () => {
  const params = new URLSearchParams();
  if (projectId.value) params.append('project_id', projectId.value.toString());
  if (tagId.value) params.append('tag_id', tagId.value.toString());
  if (taskId.value) params.append('task_id', taskId.value.toString());
  if (startDate.value) params.append('start_date', startDate.value);
  if (endDate.value) params.append('end_date', endDate.value);
  params.append('format', 'pdf');

  // Otwórz w nowym oknie dla PDF
  window.open(route('reports.export') + '?' + params.toString(), '_blank');
};
</script>

<template>
  <Head title="Reports" />

  <MainLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 max-w-6xl pt-20 sm:pt-24 md:pt-28 xl:pt-8">
        <div class="mb-6 sm:mb-8">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Reports</h1>
          <p class="text-gray-600 text-sm sm:text-base">Generate and export time tracking reports</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
          <div class="p-4 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Project</label>
                <select
                  v-model="projectId"
                  class="w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors appearance-none cursor-pointer text-sm sm:text-base"
                >
                  <option :value="null">All projects</option>
                  <option
                    v-for="project in projects"
                    :key="project.id"
                    :value="project.id"
                  >
                    {{ project.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tag</label>
                <select
                  v-model="tagId"
                  class="w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors appearance-none cursor-pointer text-sm sm:text-base"
                >
                  <option :value="null">All tags</option>
                  <option
                    v-for="tag in tags"
                    :key="tag.id"
                    :value="tag.id"
                  >
                    #{{ tag.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Start Date</label>
                <CustomDatePicker
                  v-model="startDate"
                  placeholder="Start date"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">End Date</label>
                <CustomDatePicker
                  v-model="endDate"
                  placeholder="End date"
                />
              </div>
            </div>

            <div class="flex gap-3 mt-6">
              <button
                @click="exportCsv"
                class="flex items-center gap-2 px-4 py-2.5 bg-emerald-500 text-white rounded-lg font-medium hover:bg-emerald-600 transition-colors text-sm sm:text-base"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
              </button>
              
              <button
                @click="exportPdf"
                class="flex items-center gap-2 px-4 py-2.5 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-colors text-sm sm:text-base"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF
              </button>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div class="p-4 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Preview</h2>
            <p class="text-gray-600 text-sm">
              Use the filters above and click "Export CSV" or "Export PDF" to generate your report.
              The report will include all time sessions matching your selected filters.
            </p>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
