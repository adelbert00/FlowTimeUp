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
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-[1600px] mx-auto pt-20 sm:pt-24 md:pt-28 xl:pt-6 xl:py-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <h1 class="text-2xl font-bold font-sans text-gray-900 dark:text-white">Dashboard</h1>
          
          <div class="flex items-center gap-3">
            <div class="relative">
              <select
                v-model="selectedPeriod"
                @change="changePeriod(selectedPeriod)"
                class="px-4 py-2 pr-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer"
              >
                <option v-for="option in periodOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
          </div>
              </div>

        <div class="bg-gradient-to-r from-slate-100 via-blue-50 to-slate-100 dark:from-gray-800 dark:via-gray-700 dark:to-gray-800 rounded-lg p-4 sm:p-6 mb-6 border border-slate-200 dark:border-gray-700">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-8 text-center">
            <div>
              <p class="text-slate-500 dark:text-gray-400 text-sm mb-1">Total time</p>
              <p class="text-slate-800 dark:text-white text-2xl sm:text-3xl font-bold font-mono">{{ stats.total_time }}</p>
          </div>

            <div>
              <p class="text-slate-500 dark:text-gray-400 text-sm mb-1">Top Project</p>
              <p class="text-slate-800 dark:text-white text-xl sm:text-2xl font-semibold">
                {{ top_project?.name || '--' }}
              </p>
          </div>

            <div>
              <p class="text-slate-500 dark:text-gray-400 text-sm mb-1">Top Client</p>
              <p class="text-slate-800 dark:text-white text-xl sm:text-2xl font-semibold">--</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row items-center gap-6">
              <div class="relative w-[200px] h-[200px] flex-shrink-0">
                <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
                  <circle
                    v-for="(project, index) in project_breakdown"
                    :key="project.id"
                    cx="50"
                    cy="50"
                    r="40"
                    fill="none"
                    :stroke="project.color"
                    stroke-width="20"
                    :stroke-dasharray="`${project.percentage * 2.51} ${251 - project.percentage * 2.51}`"
                    :stroke-dashoffset="getStrokeOffset(index)"
                  />
                    </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                  <p class="text-xl font-bold text-gray-900 dark:text-white font-mono">{{ stats.total_time }}</p>
                  </div>
                  </div>
              
              <div class="flex-1 w-full">
                <div class="space-y-2 max-h-[300px] overflow-y-auto">
                  <div 
                    v-for="project in visibleProjects" 
                    :key="project.id"
                    class="flex items-center gap-3"
                  >
                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate flex-1">{{ project.name }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-mono whitespace-nowrap">{{ project.formatted_time }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 text-right">{{ project.percentage.toFixed(2) }}%</span>
                  </div>
                  </div>
                
                <button
                  v-if="remainingProjects > 0"
                  @click="showMoreProjects = !showMoreProjects"
                  class="mt-4 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                >
                  {{ showMoreProjects ? 'Show less' : `Load more (${remainingProjects} left)` }}
                </button>
              </div>
              
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
            <h3 class="text-lg font-semibold font-sans text-gray-900 dark:text-white mb-4">Quick Stats</h3>
            <div class="grid grid-cols-2 gap-4">
              <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Today</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white font-mono">{{ stats.today_time }}</p>
              </div>
              <div class="p-4 bg-cyan-50 dark:bg-cyan-900/30 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">This Week</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white font-mono">{{ stats.weekly_time }}</p>
                  </div>
              <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Completed Today</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ stats.completed_today }}</p>
              </div>
              <div class="p-4 bg-amber-50 dark:bg-amber-900/30 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pending Tasks</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ stats.pending_tasks }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold font-sans text-gray-900 dark:text-white">Team activities</h3>
              </div>
          
          <div v-if="team_activities.length > 0" class="space-y-4">
            <div v-for="day in team_activities" :key="day.date" class="border-t border-gray-100 dark:border-gray-700 pt-4 first:border-t-0 first:pt-0">
              <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ day.formatted_date }}</p>
                <p class="text-sm font-mono text-gray-900 dark:text-white">{{ day.formatted_total }}</p>
              </div>
              
              <div class="space-y-2">
                <div 
                  v-for="entry in day.entries" 
                  :key="entry.id"
                  class="flex items-center gap-4 py-2 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                >
                  <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900 dark:text-white truncate">{{ entry.task_title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span 
                        v-if="entry.project_name"
                        class="inline-flex items-center gap-1 text-xs"
                      >
                        <span 
                          class="w-2 h-2 rounded-full"
                          :style="{ backgroundColor: entry.project_color }"
                        />
                        <span class="text-gray-600 dark:text-gray-400">{{ entry.project_name }}</span>
                      </span>
              </div>
              </div>
                  
                  <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-mono">{{ entry.start_time }} - {{ entry.end_time }}</span>
                    <span class="font-mono font-medium text-gray-900 dark:text-white">{{ entry.duration }}</span>
                  </div>
                </div>
              </div>
            </div>
              </div>
          
          <div v-else class="text-center py-8">
            <p class="text-gray-500 dark:text-gray-400">No time entries yet</p>
            <Link href="/tasks" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm mt-2 inline-block">
              Start tracking time
            </Link>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
