<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, watch } from 'vue';
import CustomDatePicker from '@/Components/CustomDatePicker.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

interface Project {
  id: number;
  name: string;
  color?: string;
}

interface Tag {
  id: number;
  name: string;
}

interface SummaryItem {
  id?: number;
  name: string;
  duration: string;
  seconds: number;
  earnings?: number;
  currency?: string;
  date?: string;
}

interface Summary {
  total_time: string;
  total_seconds: number;
  total_sessions: number;
  billable_time: string;
  billable_seconds: number;
  non_billable_time: string;
  non_billable_seconds: number;
  total_earnings: number;
  by_project: SummaryItem[];
  by_tag: SummaryItem[];
  by_task: SummaryItem[];
  by_date: SummaryItem[];
}

const props = defineProps<{
  projects: Project[];
  tags: Tag[];
  summary: Summary;
  filters: {
    project_id?: string;
    tag_id?: string;
    start_date?: string;
    end_date?: string;
    include_billable?: string;
    include_non_billable?: string;
  };
}>();

const projectId = ref<number | null>(props.filters.project_id ? parseInt(props.filters.project_id) : null);
const tagId = ref<number | null>(props.filters.tag_id ? parseInt(props.filters.tag_id) : null);
const startDate = ref<string>(props.filters.start_date || '');
const endDate = ref<string>(props.filters.end_date || '');
const includeBillable = ref(props.filters.include_billable !== 'false');
const includeNonBillable = ref(props.filters.include_non_billable !== 'false');
const activeTab = ref('projects');

if (!startDate.value || !endDate.value) {
  const today = new Date();
  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(today.getDate() - 30);
  if (!startDate.value) startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
  if (!endDate.value) endDate.value = today.toISOString().split('T')[0];
}

const updateFilters = () => {
  router.get(route('reports.index'), {
    project_id: projectId.value,
    tag_id: tagId.value,
    start_date: startDate.value,
    end_date: endDate.value,
    include_billable: includeBillable.value,
    include_non_billable: includeNonBillable.value,
  }, {
    preserveState: true,
    replace: true,
  });
};

watch([projectId, tagId, startDate, endDate, includeBillable, includeNonBillable], () => {
  updateFilters();
});

const exportCsv = () => {
  const params = new URLSearchParams();
  if (projectId.value) params.append('project_id', projectId.value.toString());
  if (tagId.value) params.append('tag_id', tagId.value.toString());
  if (startDate.value) params.append('start_date', startDate.value);
  if (endDate.value) params.append('end_date', endDate.value);
  params.append('include_billable', includeBillable.value.toString());
  params.append('include_non_billable', includeNonBillable.value.toString());
  params.append('format', 'csv');

  window.location.href = route('reports.export') + '?' + params.toString();
};

const exportPdf = () => {
  const params = new URLSearchParams();
  if (projectId.value) params.append('project_id', projectId.value.toString());
  if (tagId.value) params.append('tag_id', tagId.value.toString());
  if (startDate.value) params.append('start_date', startDate.value);
  if (endDate.value) params.append('end_date', endDate.value);
  params.append('include_billable', includeBillable.value.toString());
  params.append('include_non_billable', includeNonBillable.value.toString());
  params.append('format', 'pdf');

  window.open(route('reports.export') + '?' + params.toString(), '_blank');
};
</script>

<template>
  <Head title="Reports" />

  <MainLayout>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">Reports</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Insight into your productivity and earnings.</p>
          </div>
          <div class="flex items-center gap-3">
            <Button variant="outline" @click="exportCsv" class="bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800">
              Export CSV
            </Button>
            <Button @click="exportPdf" class="bg-blue-600 hover:bg-blue-700 text-white">
              Export PDF
            </Button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
          <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
              <h3 class="font-semibold text-slate-900 dark:text-slate-50 mb-4">Filters</h3>
              <div class="space-y-4">
                <div class="space-y-2">
                  <Label>Project</Label>
                  <select
                    v-model="projectId"
                    class="w-full flex h-10 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm ring-offset-white focus:outline-none focus:ring-2 focus:ring-slate-950 focus:ring-offset-2 dark:border-slate-800 dark:bg-slate-950 dark:ring-offset-slate-950 dark:focus:ring-slate-300"
                  >
                    <option :value="null">All Projects</option>
                    <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                  </select>
                </div>

                <div class="space-y-2">
                  <Label>Tag</Label>
                  <select
                    v-model="tagId"
                    class="w-full flex h-10 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm ring-offset-white focus:outline-none focus:ring-2 focus:ring-slate-950 focus:ring-offset-2 dark:border-slate-800 dark:bg-slate-950 dark:ring-offset-slate-950 dark:focus:ring-slate-300"
                  >
                    <option :value="null">All Tags</option>
                    <option v-for="t in tags" :key="t.id" :value="t.id">#{{ t.name }}</option>
                  </select>
                </div>

                <div class="space-y-2">
                  <Label>Start Date</Label>
                  <CustomDatePicker v-model="startDate" />
                </div>

                <div class="space-y-2">
                  <Label>End Date</Label>
                  <CustomDatePicker v-model="endDate" />
                </div>

                <div class="pt-2 space-y-3">
                  <div class="flex items-center space-x-2">
                    <input type="checkbox" id="billable" v-model="includeBillable" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 dark:bg-slate-950 dark:border-slate-800" />
                    <Label for="billable" class="cursor-pointer">Include Billable</Label>
                  </div>
                  <div class="flex items-center space-x-2">
                    <input type="checkbox" id="non-billable" v-model="includeNonBillable" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 dark:bg-slate-950 dark:border-slate-800" />
                    <Label for="non-billable" class="cursor-pointer">Include Non-Billable</Label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="lg:col-span-3 space-y-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
              <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Time</p>
                <h4 class="text-2xl font-bold mt-2">{{ summary.total_time }}</h4>
              </div>
              <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Billable Time</p>
                <h4 class="text-2xl font-bold mt-2 text-blue-600">{{ summary.billable_time }}</h4>
              </div>
              <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Sessions</p>
                <h4 class="text-2xl font-bold mt-2">{{ summary.total_sessions }}</h4>
              </div>
              <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Earnings</p>
                <h4 class="text-2xl font-bold mt-2 text-emerald-600">{{ summary.total_earnings }} {{ summary.by_project[0]?.currency || 'USD' }}</h4>
              </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
              <div class="border-b border-slate-200 dark:border-slate-800">
                <nav class="flex -mb-px px-6 gap-6">
                  <button 
                    @click="activeTab = 'projects'"
                    :class="[activeTab === 'projects' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300']"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                  >
                    Projects
                  </button>
                  <button 
                    @click="activeTab = 'tags'"
                    :class="[activeTab === 'tags' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300']"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                  >
                    Tags
                  </button>
                  <button 
                    @click="activeTab = 'tasks'"
                    :class="[activeTab === 'tasks' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300']"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                  >
                    Tasks
                  </button>
                </nav>
              </div>

              <div class="p-6">
                <div v-if="activeTab === 'projects'" class="overflow-x-auto">
                  <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                      <tr>
                        <th class="py-3 font-semibold">Project</th>
                        <th class="py-3 font-semibold">Duration</th>
                        <th class="py-3 font-semibold text-right">Earnings</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                      <tr v-for="p in summary.by_project" :key="p.id || p.name">
                        <td class="py-4 font-medium text-slate-900 dark:text-slate-50">{{ p.name }}</td>
                        <td class="py-4">{{ p.duration }}</td>
                        <td class="py-4 text-right font-semibold text-emerald-600">{{ p.earnings }} {{ p.currency }}</td>
                      </tr>
                      <tr v-if="summary.by_project.length === 0">
                        <td colspan="3" class="py-8 text-center text-slate-500">No project data for this period.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div v-if="activeTab === 'tags'" class="overflow-x-auto">
                  <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                      <tr>
                        <th class="py-3 font-semibold">Tag</th>
                        <th class="py-3 font-semibold">Duration</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                      <tr v-for="t in summary.by_tag" :key="t.id || t.name">
                        <td class="py-4 font-medium text-slate-900 dark:text-slate-50">
                          <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-400">
                            #{{ t.name }}
                          </span>
                        </td>
                        <td class="py-4">{{ t.duration }}</td>
                      </tr>
                      <tr v-if="summary.by_tag.length === 0">
                        <td colspan="2" class="py-8 text-center text-slate-500">No tag data for this period.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div v-if="activeTab === 'tasks'" class="overflow-x-auto">
                  <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                      <tr>
                        <th class="py-3 font-semibold">Task</th>
                        <th class="py-3 font-semibold">Duration</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                      <tr v-for="task in summary.by_task" :key="task.id || task.name">
                        <td class="py-4 font-medium text-slate-900 dark:text-slate-50">{{ task.name }}</td>
                        <td class="py-4">{{ task.duration }}</td>
                      </tr>
                      <tr v-if="summary.by_task.length === 0">
                        <td colspan="2" class="py-8 text-center text-slate-500">No task data for this period.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
