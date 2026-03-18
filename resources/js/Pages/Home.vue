<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, computed } from 'vue';

interface ProjectBreakdown {
  id: number;
    name: string;
    color: string;
  seconds: number;
  formatted_time: string;
  percentage: number;
}

interface TimeEntry {
  id: number;
  task_title: string;
  project_name: string | null;
  project_color: string;
  start_time: string;
  end_time: string;
  duration: string;
}

interface DayActivity {
  date: string;
  formatted_date: string;
  entries: TimeEntry[];
  total_seconds: number;
  formatted_total: string;
}

interface Props {
  stats: {
    total_time: string;
    total_seconds: number;
    today_time: string;
    today_seconds: number;
    weekly_time: string;
    weekly_seconds: number;
    completed_today: number;
    active_projects: number;
    pending_tasks: number;
    total_tasks: number;
  };
  top_project: { name: string; time: string } | null;
  project_breakdown: ProjectBreakdown[];
  team_activities: DayActivity[];
  period: string;
  user: { name: string; first_name: string };
}

const props = defineProps<Props>();

const periodOptions = [
  { value: 'week', label: 'This week' },
  { value: 'month', label: 'This month' },
  { value: 'year', label: 'Last year' },
  { value: 'all', label: 'All time' },
];

const selectedPeriod = ref(props.period);
const showMoreProjects = ref(false);

const visibleProjects = computed(() => {
  if (showMoreProjects.value) {
    return props.project_breakdown;
  }
  return props.project_breakdown.slice(0, 10);
});

const remainingProjects = computed(() => {
  return Math.max(0, props.project_breakdown.length - 10);
});

function changePeriod(period: string) {
  selectedPeriod.value = period;
  router.get('/home', { period }, { preserveState: true, preserveScroll: true });
}

function getStrokeOffset(index: number): number {
  let offset = 0;
  for (let i = 0; i < index; i++) {
    offset += (props.project_breakdown[i]?.percentage || 0) * 2.51;
  }
  return -offset;
}
</script>

<template>
  <Head title="Dashboard" />

  <MainLayout>
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold font-sans text-primary">Dashboard</h1>
        
        <div class="flex items-center gap-3">
          <div class="relative">
            <select
              v-model="selectedPeriod"
              @change="changePeriod(selectedPeriod)"
              class="px-4 py-2 pr-10 bg-surface-raised border border-border rounded-lg text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent appearance-none cursor-pointer"
            >
              <option v-for="option in periodOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-surface-raised rounded-2xl p-8 border border-border shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-accent/5 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-accent/10 transition-colors duration-700"></div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-12 text-center relative z-10">
          <div class="space-y-2">
            <p class="text-secondary text-xs uppercase tracking-[0.2em] font-bold">Total time</p>
            <p class="text-primary text-4xl font-black font-mono tracking-tighter">{{ stats.total_time }}</p>
          </div>

          <div class="space-y-2">
            <p class="text-secondary text-xs uppercase tracking-[0.2em] font-bold">Top Project</p>
            <p class="text-primary text-2xl font-bold truncate px-4">
              {{ top_project?.name || '--' }}
            </p>
          </div>

          <div class="space-y-2">
            <p class="text-secondary text-xs uppercase tracking-[0.2em] font-bold">Today Focus</p>
            <p class="text-primary text-2xl font-bold font-mono tracking-tight">{{ stats.today_time }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-surface-raised rounded-xl border border-border p-6 shadow-sm">
          <div class="flex flex-col sm:flex-row items-center gap-8">
            <div class="relative w-[180px] h-[180px] flex-shrink-0">
              <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
                <circle
                  v-for="(project, index) in project_breakdown"
                  :key="project.id"
                  cx="50"
                  cy="50"
                  r="40"
                  fill="none"
                  :stroke="project.color"
                  stroke-width="12"
                  :stroke-dasharray="`${project.percentage * 2.51} ${251 - project.percentage * 2.51}`"
                  :stroke-dashoffset="getStrokeOffset(index)"
                  class="transition-all duration-500"
                />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <p class="text-lg font-bold text-primary font-mono">{{ stats.total_time }}</p>
              </div>
            </div>
            
            <div class="flex-1 w-full">
              <div class="space-y-3 max-h-[280px] overflow-y-auto pr-2 custom-scrollbar">
                <div 
                  v-for="project in visibleProjects" 
                  :key="project.id"
                  class="flex items-center gap-3"
                >
                  <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: project.color }"></span>
                  <span class="text-sm text-secondary truncate flex-1">{{ project.name }}</span>
                  <span class="text-sm text-primary font-mono font-medium whitespace-nowrap">{{ project.formatted_time }}</span>
                  <span class="text-xs text-muted text-right min-w-[45px]">{{ project.percentage.toFixed(1) }}%</span>
                </div>
              </div>
              
              <button
                v-if="remainingProjects > 0"
                @click="showMoreProjects = !showMoreProjects"
                class="mt-4 text-xs font-semibold text-accent hover:text-accent-hover transition-colors uppercase tracking-wider"
              >
                {{ showMoreProjects ? 'Show less' : `+ ${remainingProjects} more projects` }}
              </button>
            </div>
          </div>
        </div>

        <div class="bg-surface-raised rounded-xl border border-border p-6 shadow-sm">
          <h3 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Quick Stats
          </h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-surface-overlay rounded-xl border border-border">
              <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-2">Today</p>
              <p class="text-xl font-bold text-primary font-mono">{{ stats.today_time }}</p>
            </div>
            <div class="p-4 bg-surface-overlay rounded-xl border border-border">
              <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-2">This Week</p>
              <p class="text-xl font-bold text-primary font-mono">{{ stats.weekly_time }}</p>
            </div>
            <div class="p-4 bg-surface-overlay rounded-xl border border-border">
              <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-2">Done Today</p>
              <p class="text-xl font-bold text-primary">{{ stats.completed_today }}</p>
            </div>
            <div class="p-4 bg-surface-overlay rounded-xl border border-border">
              <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-2">Pending</p>
              <p class="text-xl font-bold text-primary">{{ stats.pending_tasks }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-surface-raised rounded-xl border border-border shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border bg-surface-overlay/30">
          <h3 class="text-lg font-bold text-primary">Team activities</h3>
        </div>
        
        <div v-if="team_activities.length > 0" class="p-6 space-y-8">
          <div v-for="day in team_activities" :key="day.date" class="relative pl-6 before:absolute before:left-0 before:top-2 before:bottom-0 before:w-px before:bg-border last:before:bottom-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="absolute left-[-4px] top-1.5 w-2 h-2 rounded-full bg-accent ring-4 ring-surface-raised"></div>
                <p class="text-sm font-bold text-primary uppercase tracking-wider">{{ day.formatted_date }}</p>
              </div>
              <p class="text-sm font-mono font-bold text-accent">{{ day.formatted_total }}</p>
            </div>
            
            <div class="space-y-3">
              <div 
                v-for="entry in day.entries" 
                :key="entry.id"
                class="flex items-center gap-4 py-3 px-4 bg-surface-overlay/50 rounded-xl border border-border/50 hover:border-accent/30 transition-colors"
              >
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-primary truncate">{{ entry.task_title }}</p>
                  <div class="flex items-center gap-2 mt-1">
                    <span 
                      v-if="entry.project_name"
                      class="inline-flex items-center gap-1.5 text-xs"
                    >
                      <span 
                        class="w-2 h-2 rounded-full"
                        :style="{ backgroundColor: entry.project_color }"
                      />
                      <span class="text-secondary font-medium">{{ entry.project_name }}</span>
                    </span>
                  </div>
                </div>
                
                <div class="flex items-center gap-4 text-xs">
                  <span class="font-mono text-muted">{{ entry.start_time }} - {{ entry.end_time }}</span>
                  <span class="font-mono font-bold text-primary bg-surface-raised px-2 py-1 rounded border border-border">{{ entry.duration }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-16">
          <div class="w-16 h-16 bg-surface-overlay rounded-full flex items-center justify-center mx-auto mb-4 border border-border">
            <svg class="w-8 h-8 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-secondary font-medium">No time entries yet</p>
          <Link href="/tasks" class="text-accent hover:text-accent-hover text-sm font-bold mt-2 inline-block">
            Start tracking time →
          </Link>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
