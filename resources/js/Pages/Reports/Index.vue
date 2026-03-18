<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, watch } from 'vue';
import CustomDatePicker from '@/Components/CustomDatePicker.vue';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Button } from '@/Components/ui/button';
import { Download, FileText, BarChart3, Tag as TagIcon, Folder } from 'lucide-vue-next';

interface SummaryData {
  id: number | string;
  name: string;
  duration: number;
  earnings: number;
  color?: string;
}

interface Props {
  projects: any[];
  tags: any[];
  projectSummary: SummaryData[];
  tagSummary: SummaryData[];
  filters: {
    project_id: string | number | null;
    tag_id: string | number | null;
    start_date: string;
    end_date: string;
    include_billable: boolean | string;
    include_non_billable: boolean | string;
  };
}

const props = defineProps<Props>();

const activeTab = ref('projects');

const filterState = ref({
  project_id: props.filters.project_id?.toString() || 'all',
  tag_id: props.filters.tag_id?.toString() || 'all',
  start_date: props.filters.start_date,
  end_date: props.filters.end_date,
  include_billable: String(props.filters.include_billable) === 'true',
  include_non_billable: String(props.filters.include_non_billable) === 'true',
});

const formatDuration = (seconds: number) => {
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  return `${h}h ${m}m`;
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const updateFilters = () => {
  router.get(route('reports.index'), {
    project_id: filterState.value.project_id === 'all' ? null : filterState.value.project_id,
    tag_id: filterState.value.tag_id === 'all' ? null : filterState.value.tag_id,
    start_date: filterState.value.start_date,
    end_date: filterState.value.end_date,
    include_billable: filterState.value.include_billable,
    include_non_billable: filterState.value.include_non_billable,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

watch(() => filterState.value, updateFilters, { deep: true });

const exportReport = (format: 'csv' | 'pdf') => {
  const params = new URLSearchParams();
  if (filterState.value.project_id !== 'all') params.append('project_id', filterState.value.project_id);
  if (filterState.value.tag_id !== 'all') params.append('tag_id', filterState.value.tag_id);
  params.append('start_date', filterState.value.start_date);
  params.append('end_date', filterState.value.end_date);
  params.append('include_billable', filterState.value.include_billable.toString());
  params.append('include_non_billable', filterState.value.include_non_billable.toString());
  params.append('format', format);

  const url = route('reports.export') + '?' + params.toString();
  if (format === 'csv') {
    window.location.href = url;
  } else {
    window.open(url, '_blank');
  }
};
</script>

<template>
  <Head title="Reports" />

  <MainLayout>
    <div class="min-h-screen bg-background p-6 lg:p-8">
      <div class="max-w-7xl mx-auto space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-3xl font-bold tracking-tight">Reports</h1>
            <p class="text-muted-foreground">Analyze your time tracking and earnings across projects and tags.</p>
          </div>
          <div class="flex items-center gap-2">
            <Button variant="outline" @click="exportReport('csv')">
              <Download class="mr-2 h-4 w-4" />
              CSV
            </Button>
            <Button variant="destructive" @click="exportReport('pdf')">
              <FileText class="mr-2 h-4 w-4" />
              PDF
            </Button>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-6">
          <div class="p-6">
            <h2 class="text-lg font-semibold mb-1">Filters</h2>
            <p class="text-sm text-muted-foreground mb-6">Adjust the time range and categories to refine your report.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-medium">Project</label>
                <Select v-model="filterState.project_id">
                  <SelectTrigger>
                    <SelectValue placeholder="All Projects" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Projects</SelectItem>
                    <SelectItem v-for="p in projects" :key="p.id" :value="p.id.toString()">
                      {{ p.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Tag</label>
                <Select v-model="filterState.tag_id">
                  <SelectTrigger>
                    <SelectValue placeholder="All Tags" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Tags</SelectItem>
                    <SelectItem v-for="t in tags" :key="t.id" :value="t.id.toString()">
                      #{{ t.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Start Date</label>
                <CustomDatePicker v-model="filterState.start_date" />
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">End Date</label>
                <CustomDatePicker v-model="filterState.end_date" />
              </div>
            </div>

            <div class="flex items-center gap-6 mt-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="filterState.include_billable" class="rounded border-gray-300 text-primary focus:ring-primary" />
                <span class="text-sm font-medium">Billable</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="filterState.include_non_billable" class="rounded border-gray-300 text-primary focus:ring-primary" />
                <span class="text-sm font-medium">Non-billable</span>
              </label>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="flex bg-muted p-1 rounded-lg w-full max-w-md">
            <button 
              @click="activeTab = 'projects'"
              :class="['flex-1 flex items-center justify-center gap-2 px-3 py-1.5 text-sm font-medium rounded-md transition-all', activeTab === 'projects' ? 'bg-background shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground']"
            >
              <Folder class="h-4 w-4" />
              By Projects
            </button>
            <button 
              @click="activeTab = 'tags'"
              :class="['flex-1 flex items-center justify-center gap-2 px-3 py-1.5 text-sm font-medium rounded-md transition-all', activeTab === 'tags' ? 'bg-background shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground']"
            >
              <TagIcon class="h-4 w-4" />
              By Tags
            </button>
          </div>

          <div v-if="activeTab === 'projects'" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
              <h3 class="text-lg font-semibold flex items-center gap-2">
                <BarChart3 class="h-5 w-5 text-primary" />
                Project Breakdown
              </h3>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                    <th class="px-6 py-4">Project Name</th>
                    <th class="px-6 py-4 text-right">Total Duration</th>
                    <th class="px-6 py-4 text-right">Estimated Earnings</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                  <tr v-for="item in projectSummary" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-sm">
                      <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: item.color || '#94a3b8' }"></div>
                        {{ item.name }}
                      </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm">{{ formatDuration(item.duration) }}</td>
                    <td class="px-6 py-4 text-right text-sm">{{ formatCurrency(item.earnings) }}</td>
                  </tr>
                  <tr v-if="projectSummary.length === 0">
                    <td colspan="3" class="px-6 py-12 text-center text-muted-foreground text-sm">
                      No data available for the selected filters.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="activeTab === 'tags'" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
              <h3 class="text-lg font-semibold flex items-center gap-2">
                <TagIcon class="h-5 w-5 text-primary" />
                Tag Breakdown
              </h3>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                    <th class="px-6 py-4">Tag Name</th>
                    <th class="px-6 py-4 text-right">Total Duration</th>
                    <th class="px-6 py-4 text-right">Estimated Earnings</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                  <tr v-for="item in tagSummary" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-sm">#{{ item.name }}</td>
                    <td class="px-6 py-4 text-right text-sm">{{ formatDuration(item.duration) }}</td>
                    <td class="px-6 py-4 text-right text-sm">{{ formatCurrency(item.earnings) }}</td>
                  </tr>
                  <tr v-if="tagSummary.length === 0">
                    <td colspan="3" class="px-6 py-12 text-center text-muted-foreground text-sm">
                      No data available for the selected filters.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
      </div>
    </div>
  </MainLayout>
</template>
