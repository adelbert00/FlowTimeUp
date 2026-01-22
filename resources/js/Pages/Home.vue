<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

interface RecentTask {
  id: number;
  title: string;
  completed: boolean;
  project?: {
    name: string;
    color: string;
  };
}

interface Props {
  stats?: {
    today_time: string;
    today_seconds: number;
    completed_today: number;
    active_projects: number;
    pending_tasks: number;
    total_tasks: number;
    weekly_time: string;
    weekly_seconds: number;
  };
  recent_tasks?: RecentTask[];
}

const props = defineProps<Props>();
const page = usePage();
const user = page.props.auth?.user;

const stats = props.stats || {
  today_time: '00:00:00',
  today_seconds: 0,
  completed_today: 0,
  active_projects: 0,
  pending_tasks: 0,
  total_tasks: 0,
  weekly_time: '00:00:00',
  weekly_seconds: 0,
};

const recentTasks = props.recent_tasks || [];
</script>

<template>
  <Head title="Dashboard" />

  <MainLayout>
    <div class="min-h-screen bg-gray-50">
      <div class="container mx-auto px-4 sm:px-6 md:px-10 lg:px-8 py-6 sm:py-8 max-w-6xl pt-20 sm:pt-24 md:pt-28 xl:pt-8">
        <!-- Welcome Section -->
        <div class="mb-6 sm:mb-8">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
            Welcome back, {{ user?.name || 'User' }}!
          </h1>
          <p class="text-gray-600 text-sm sm:text-base">Here's an overview of your productivity</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
          <!-- Today's Time -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 hover:border-blue-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-600/10 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
            <p class="text-gray-600 text-xs sm:text-sm mb-1">Today's tracked time</p>
            <p class="text-xl sm:text-3xl font-bold text-gray-900 font-mono">{{ stats.today_time }}</p>
          </div>

          <!-- Weekly Time -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 hover:border-cyan-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-cyan-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
            </div>
            <p class="text-gray-600 text-xs sm:text-sm mb-1">This week</p>
            <p class="text-xl sm:text-3xl font-bold text-gray-900 font-mono">{{ stats.weekly_time }}</p>
          </div>

          <!-- Tasks Completed -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 hover:border-emerald-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
            </div>
            <p class="text-gray-600 text-xs sm:text-sm mb-1">Completed today</p>
            <p class="text-xl sm:text-3xl font-bold text-gray-900">{{ stats.completed_today }}</p>
          </div>

          <!-- Pending Tasks -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 hover:border-amber-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
              </div>
            </div>
            <p class="text-gray-600 text-xs sm:text-sm mb-1">Pending tasks</p>
            <p class="text-xl sm:text-3xl font-bold text-gray-900">{{ stats.pending_tasks }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
          <!-- Quick Actions -->
          <div class="xl:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
              <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
              <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <Link
                  href="/tasks"
                  prefetch="hover"
                  class="flex items-center gap-2 sm:gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl border border-gray-200 shadow-sm hover:border-blue-500/50 hover:bg-blue-600/5 transition-all group"
                >
                  <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-blue-600/10 flex items-center justify-center group-hover:bg-blue-600/20 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">Start Tracking</p>
                    <p class="text-xs text-gray-500 hidden sm:block">Track time on tasks</p>
                  </div>
                </Link>

                <Link
                  href="/projects"
                  prefetch="hover"
                  class="flex items-center gap-2 sm:gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl border border-gray-200 shadow-sm hover:border-blue-500/50 hover:bg-blue-600/5 transition-all group"
                >
                  <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-blue-600/10 flex items-center justify-center group-hover:bg-blue-600/20 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">Projects</p>
                    <p class="text-xs text-gray-500 hidden sm:block">{{ stats.active_projects }} active</p>
                  </div>
                </Link>

                <Link
                  href="/tags"
                  prefetch="hover"
                  class="flex items-center gap-2 sm:gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl border border-gray-200 shadow-sm hover:border-emerald-500/50 hover:bg-emerald-500/5 transition-all group"
                >
                  <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">Manage Tags</p>
                    <p class="text-xs text-gray-500 hidden sm:block">Organize with tags</p>
                  </div>
                </Link>

                <Link
                  href="/profile"
                  prefetch="hover"
                  class="flex items-center gap-2 sm:gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl border border-gray-200 shadow-sm hover:border-amber-500/50 hover:bg-amber-500/5 transition-all group"
                >
                  <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-amber-500/10 flex items-center justify-center group-hover:bg-amber-500/20 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">Settings</p>
                    <p class="text-xs text-gray-500 hidden sm:block">Account settings</p>
                  </div>
                </Link>
              </div>
            </div>
          </div>

          <!-- Recent Tasks -->
          <div class="xl:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Recent Tasks</h2>
                <Link href="/tasks" prefetch="hover" class="text-xs sm:text-sm text-blue-600 hover:text-blue-600">View all</Link>
              </div>
              
              <div v-if="recentTasks.length > 0" class="space-y-2 sm:space-y-3">
                <div
                  v-for="task in recentTasks"
                  :key="task.id"
                  class="flex items-center gap-2 sm:gap-3 p-2 sm:p-3 bg-gray-50 rounded-lg"
                >
                  <div
                    class="w-2 h-2 rounded-full flex-shrink-0"
                    :class="task.completed ? 'bg-emerald-500' : 'bg-gray-400'"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm text-gray-900 truncate" :class="{ 'line-through text-gray-500': task.completed }">
                      {{ task.title }}
                    </p>
                    <p v-if="task.project" class="text-xs text-gray-500 truncate">
                      {{ task.project.name }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-6 sm:py-8">
                <p class="text-gray-500 text-xs sm:text-sm">No tasks yet</p>
                <Link href="/tasks" prefetch="hover" class="text-blue-600 text-xs sm:text-sm hover:text-blue-600 mt-2 inline-block">
                  Create your first task
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- New Features -->
        <div class="mt-4 sm:mt-6 bg-gradient-to-r from-blue-50 via-blue-50 to-cyan-50 rounded-xl border border-blue-200 p-4 sm:p-6">
          <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">New Features</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Link
              href="/reports"
              prefetch="hover"
              class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-emerald-500/50 hover:bg-emerald-500/5 transition-all group"
            >
              <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 mb-1">Reports & Export</p>
                <p class="text-xs text-gray-500">Generate detailed reports and export your time tracking data as CSV or PDF</p>
              </div>
            </Link>

            <Link
              href="/calendar"
              prefetch="hover"
              class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-cyan-500/50 hover:bg-cyan-500/5 transition-all group"
            >
              <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center group-hover:bg-cyan-500/20 transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 mb-1">Calendar View</p>
                <p class="text-xs text-gray-500">View your tasks and time sessions in a calendar format</p>
              </div>
            </Link>

            <Link
              href="/task-templates"
              prefetch="hover"
              class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-500/50 hover:bg-blue-600/5 transition-all group"
            >
              <div class="w-10 h-10 rounded-lg bg-blue-600/10 flex items-center justify-center group-hover:bg-blue-600/20 transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 mb-1">Task Templates</p>
                <p class="text-xs text-gray-500">Create reusable task templates to speed up your workflow</p>
              </div>
            </Link>
          </div>
        </div>

        <!-- Getting Started (show if no tasks) -->
        <div v-if="stats.total_tasks === 0" class="mt-4 sm:mt-6 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200 p-4 sm:p-6">
          <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-600/20 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">Get Started</h3>
              <p class="text-gray-600 text-xs sm:text-sm mb-3 sm:mb-4">
                FlowTimeUp helps you track time spent on tasks and projects. Start by creating a task and hit the play button to begin tracking!
              </p>
              <Link
                href="/tasks"
                prefetch="hover"
                class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg text-xs sm:text-sm font-medium hover:bg-blue-700 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Start Tracking Time
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
