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
  totalSummary: {
    total_time: string;
    total_seconds: number;
    total_sessions: number;
    total_earnings: number;
    [key: string]: any;
  };
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
  
  if (filterState.value.start_date) params.append('start_date', filterState.value.start_date);
  if (filterState.value.end_date) params.append('end_date', filterState.value.end_date);
  
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
    <div class="space-y-8 animate-fade-up">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-primary">Reports</h1>
          <p class="text-secondary mt-1">Analyze your time tracking and earnings across projects and tags.</p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="outline" @click="exportReport('csv')" class="bg-surface-raised border-border text-primary hover:bg-surface-overlay">
            <Download class="mr-2 h-4 w-4 text-accent" />
            CSV
          </Button>
          <Button class="bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-900/20 transition-all active:scale-95" @click="exportReport('pdf')">
            <FileText class="mr-2 h-4 w-4" />
            PDF
          </Button>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-raised p-6 rounded-2xl border border-border shadow-sm group hover:border-accent/30 transition-all duration-300">
          <div class="text-xs font-bold text-secondary uppercase tracking-[0.15em] mb-2 group-hover:text-accent transition-colors">Total Time</div>
          <div class="text-3xl font-black font-mono tracking-tighter text-primary">{{ totalSummary?.total_time || '00:00:00' }}</div>
        </div>
        <div class="bg-surface-raised p-6 rounded-2xl border border-border shadow-sm group hover:border-accent/30 transition-all duration-300">
          <div class="text-xs font-bold text-secondary uppercase tracking-[0.15em] mb-2 group-hover:text-accent transition-colors">Total Earnings</div>
          <div class="text-3xl font-black font-mono tracking-tighter text-primary">{{ formatCurrency(totalSummary?.total_earnings || 0) }}</div>
        </div>
        <div class="bg-surface-raised p-6 rounded-2xl border border-border shadow-sm group hover:border-accent/30 transition-all duration-300">
          <div class="text-xs font-bold text-secondary uppercase tracking-[0.15em] mb-2 group-hover:text-accent transition-colors">Sessions Count</div>
          <div class="text-3xl font-black font-mono tracking-tighter text-primary">{{ totalSummary?.total_sessions || 0 }}</div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-surface-raised rounded-2xl border border-border shadow-sm overflow-hidden relative">
        <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
        <div class="p-8 relative z-10">
          <div class="flex items-center gap-2 mb-8">
            <div class="p-2 bg-accent/10 rounded-lg">
              <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            </div>
            <div>
              <h2 class="text-lg font-bold text-primary">Filters</h2>
              <p class="text-xs text-secondary">Adjust parameters to refine results</p>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="space-y-2">
              <label class="text-xs font-bold text-secondary uppercase tracking-wider">Project</label>
              <Select v-model="filterState.project_id">
                <SelectTrigger class="bg-surface-overlay border-border text-primary focus:ring-accent">
                  <SelectValue placeholder="All Projects" />
                </SelectTrigger>
                <SelectContent class="bg-surface-raised border-border text-primary">
                  <SelectItem value="all">All Projects</SelectItem>
                  <SelectItem v-for="p in projects" :key="p.id" :value="p.id.toString()">
                    {{ p.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="space-y-2">
              <label class="text-xs font-bold text-secondary uppercase tracking-wider">Tag</label>
              <Select v-model="filterState.tag_id">
                <SelectTrigger class="bg-surface-overlay border-border text-primary focus:ring-accent">
                  <SelectValue placeholder="All Tags" />
                </SelectTrigger>
                <SelectContent class="bg-surface-raised border-border text-primary">
                  <SelectItem value="all">All Tags</SelectItem>
                  <SelectItem v-for="t in tags" :key="t.id" :value="t.id.toString()">
                    #{{ t.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="space-y-2">
              <label class="text-xs font-bold text-secondary uppercase tracking-wider">Start Date</label>
              <CustomDatePicker v-model="filterState.start_date" />
            </div>

            <div class="space-y-2">
              <label class="text-xs font-bold text-secondary uppercase tracking-wider">End Date</label>
              <CustomDatePicker v-model="filterState.end_date" />
            </div>
          </div>

          <div class="flex items-center gap-8 mt-10 pt-8 border-t border-border/50">
            <label class="flex items-center gap-3 cursor-pointer group">
              <div class="relative flex items-center">
                <input type="checkbox" v-model="filterState.include_billable" class="peer sr-only" />
                <div class="w-10 h-5 bg-border rounded-full transition-colors peer-checked:bg-accent"></div>
                <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
              </div>
              <span class="text-xs font-bold text-secondary uppercase tracking-wider group-hover:text-primary transition-colors">Billable</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer group">
              <div class="relative flex items-center">
                <input type="checkbox" v-model="filterState.include_non_billable" class="peer sr-only" />
                <div class="w-10 h-5 bg-border rounded-full transition-colors peer-checked:bg-accent"></div>
                <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
              </div>
              <span class="text-xs font-bold text-secondary uppercase tracking-wider group-hover:text-primary transition-colors">Non-billable</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Content Tabs & Tables -->
      <div class="space-y-6">
        <div class="flex bg-surface-raised p-1 rounded-xl border border-border w-fit shadow-sm">
          <button 
            @click="activeTab = 'projects'"
            :class="['flex items-center gap-2 px-6 py-2 text-xs font-bold uppercase tracking-widest rounded-lg transition-all', activeTab === 'projects' ? 'bg-accent text-accent-text shadow-lg shadow-accent/20' : 'text-secondary hover:text-primary hover:bg-surface-overlay']"
          >
            <Folder class="h-3.5 w-3.5" />
            By Projects
          </button>
          <button 
            @click="activeTab = 'tags'"
            :class="['flex items-center gap-2 px-6 py-2 text-xs font-bold uppercase tracking-widest rounded-lg transition-all', activeTab === 'tags' ? 'bg-accent text-accent-text shadow-lg shadow-accent/20' : 'text-secondary hover:text-primary hover:bg-surface-overlay']"
          >
            <TagIcon class="h-3.5 w-3.5" />
            By Tags
          </button>
        </div>

        <!-- Table Card -->
        <div class="bg-surface-raised rounded-2xl border border-border shadow-sm overflow-hidden animate-scale-in">
          <div class="p-6 border-b border-border bg-surface-overlay/30 flex items-center justify-between">
            <h3 class="text-lg font-bold text-primary flex items-center gap-2">
              <BarChart3 v-if="activeTab === 'projects'" class="h-5 w-5 text-accent" />
              <TagIcon v-else class="h-5 w-5 text-accent" />
              {{ activeTab === 'projects' ? 'Project Breakdown' : 'Tag Breakdown' }}
            </h3>
            <span class="text-[10px] font-bold text-muted uppercase tracking-widest bg-surface-overlay px-2 py-1 rounded border border-border">Live Data</span>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-surface-overlay/50 text-[10px] font-black text-secondary uppercase tracking-[0.2em]">
                  <th class="px-8 py-5 border-b border-border">{{ activeTab === 'projects' ? 'Project Name' : 'Tag Name' }}</th>
                  <th class="px-8 py-5 border-b border-border text-right">Total Duration</th>
                  <th class="px-8 py-5 border-b border-border text-right">Estimated Earnings</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border">
                <template v-if="activeTab === 'projects'">
                  <tr v-for="item in projectSummary" :key="item.id" class="hover:bg-surface-overlay/30 transition-colors group">
                    <td class="px-8 py-5 font-bold text-primary text-sm">
                      <div class="flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full shadow-sm ring-2 ring-surface-raised" :style="{ backgroundColor: item.color || '#94a3b8' }"></div>
                        <span class="group-hover:text-accent transition-colors">{{ item.name }}</span>
                      </div>
                    </td>
                    <td class="px-8 py-5 text-right font-mono text-sm text-secondary group-hover:text-primary transition-colors">{{ formatDuration(item.duration) }}</td>
                    <td class="px-8 py-5 text-right font-mono font-bold text-sm text-primary group-hover:text-accent transition-colors">{{ formatCurrency(item.earnings) }}</td>
                  </tr>
                </template>
                <template v-else>
                  <tr v-for="item in tagSummary" :key="item.id" class="hover:bg-surface-overlay/30 transition-colors group">
                    <td class="px-8 py-5 font-bold text-primary text-sm">
                      <span class="text-accent mr-1">#</span>
                      <span class="group-hover:text-accent transition-colors">{{ item.name }}</span>
                    </td>
                    <td class="px-8 py-5 text-right font-mono text-sm text-secondary group-hover:text-primary transition-colors">{{ formatDuration(item.duration) }}</td>
                    <td class="px-8 py-5 text-right font-mono font-bold text-sm text-primary group-hover:text-accent transition-colors">{{ formatCurrency(item.earnings) }}</td>
                  </tr>
                </template>
                <tr v-if="(activeTab === 'projects' && projectSummary.length === 0) || (activeTab === 'tags' && tagSummary.length === 0)">
                  <td colspan="3" class="px-8 py-20 text-center">
                    <div class="flex flex-col items-center gap-3 opacity-50">
                      <div class="w-12 h-12 bg-surface-overlay rounded-full flex items-center justify-center border border-border">
                        <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      </div>
                      <p class="text-secondary font-bold text-xs uppercase tracking-widest">No data found</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
