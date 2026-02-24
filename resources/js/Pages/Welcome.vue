<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
  canLogin?: boolean;
  canRegister?: boolean;
  laravelVersion?: string;
  phpVersion?: string;
}>();

const mobileMenuOpen = ref(false);
const canonicalUrl = typeof window !== 'undefined' ? window.location.origin + (window.location.pathname || '/') : '';
</script>

<template>
  <Head>
    <title>FlowTimeUp - Track Your Time | Modern Time Tracking Application</title>
    <meta name="description" content="FlowTimeUp - Modern time tracking application. Track your time across tasks and projects with an intuitive interface. Boost productivity with precise time tracking and insightful reports." />
    <meta name="keywords" content="time tracking, task management, productivity, project management, time tracker" />
    <link rel="canonical" :href="canonicalUrl" />
  </Head>
  
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 overflow-hidden relative">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-50/0 via-gray-50 to-gray-100 pointer-events-none" />
    
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl" />
      <div class="absolute top-1/2 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl" />
      <div class="absolute -bottom-40 right-1/3 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl" />
      <div class="absolute bottom-0 left-0 right-0 h-96 bg-gradient-to-t from-gray-100 to-transparent" />
    </div>

    <nav class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 sm:gap-3">
          <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-500/25">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-lg sm:text-xl font-bold text-gray-900">FlowTimeUp</span>
        </div>

        <div v-if="canLogin" class="hidden sm:flex items-center gap-3">
          <Link
            v-if="$page.props.auth.user"
            :href="route('home')"
            class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors"
          >
            Dashboard
          </Link>
          <template v-else>
            <Link
              :href="route('login')"
              class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors"
            >
              Sign in
            </Link>
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/25"
            >
              Get Started
            </Link>
          </template>
        </div>

        <button
          v-if="canLogin && !$page.props.auth.user"
          @click="mobileMenuOpen = !mobileMenuOpen"
          :aria-label="mobileMenuOpen ? 'Close menu' : 'Open menu'"
          :aria-expanded="mobileMenuOpen"
          class="sm:hidden p-2 text-gray-600 hover:text-gray-900 transition-colors"
        >
          <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div
        v-if="mobileMenuOpen && canLogin && !$page.props.auth.user"
        class="sm:hidden mt-4 p-4 bg-white/90 backdrop-blur-lg rounded-xl border border-gray-200"
      >
        <div class="flex flex-col gap-2">
          <Link
            :href="route('login')"
            class="px-4 py-3 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors text-center"
          >
            Sign in
          </Link>
          <Link
            v-if="canRegister"
            :href="route('register')"
            class="px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium text-center"
          >
            Get Started
          </Link>
        </div>
      </div>
    </nav>

    <main class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-16 pb-16 sm:pb-24">
      <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 bg-blue-600/10 border border-blue-600/30 rounded-full text-blue-700 text-xs sm:text-sm font-medium mb-6 sm:mb-8">
          <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse" />
          Free time tracking for everyone
        </div>

        <h1 class="text-3xl sm:text-5xl xl:text-7xl font-bold font-sans text-gray-900 mb-4 sm:mb-6 leading-tight">
          Track time.<br />
          <span class="bg-gradient-to-r from-blue-500 via-blue-600 to-cyan-500 bg-clip-text text-transparent">
            Stay productive.
          </span>
        </h1>

        <p class="text-base sm:text-xl text-gray-600 mb-8 sm:mb-10 max-w-2xl mx-auto px-4">
          The simplest way to track your time across tasks and projects. 
          Boost your productivity with precise time tracking and insightful reports.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 mb-12 sm:mb-16 px-4">
          <Link
            :href="canRegister ? route('register') : route('login')"
            class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2"
          >
            Start Tracking Free
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </Link>
          <a
            href="#features"
            class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-colors flex items-center justify-center gap-2"
          >
            Learn More
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </a>
        </div>

        <div class="relative max-w-xl mx-auto px-4">
          <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-500/20 rounded-2xl blur-xl" />
          <div class="relative bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 p-4 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 sm:mb-6">
              <div class="text-left">
                <p class="text-gray-600 text-xs sm:text-sm mb-1">Currently tracking</p>
                <p class="text-gray-900 font-semibold text-sm sm:text-base">Building landing page</p>
              </div>
              <span class="self-start sm:self-center px-3 py-1 bg-blue-600/10 text-blue-600 text-xs sm:text-sm rounded-lg">Work</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="font-mono text-2xl sm:text-4xl font-bold text-emerald-400">
                00:45:23<span class="text-emerald-400/50">.42</span>
              </div>
              <button aria-label="Stop timer" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-red-500 hover:bg-red-600 flex items-center justify-center transition-colors shadow-lg shadow-red-500/25 flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <section id="features" class="mt-20 sm:mt-32 px-4 [content-visibility:auto] [contain-intrinsic-size:0_800px]">
        <div class="text-center mb-10 sm:mb-16">
          <h2 class="text-2xl sm:text-3xl xl:text-4xl font-bold text-gray-900 mb-4">
            Everything you need to track time
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto text-sm sm:text-base">
            Simple, powerful features designed to help you stay focused and productive.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-blue-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-600/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Precise Timer</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Track time down to the centisecond. Start and stop with a single click.
            </p>
          </div>

          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-blue-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-600/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Projects & Tags</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Organize your work with projects and tags. Filter and analyze your time.
            </p>
          </div>

          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-emerald-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Task Management</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Create tasks, set priorities, and due dates. Mark complete when done.
            </p>
          </div>

          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-purple-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-500/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Manual Time Entry</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Add time entries manually with start and end times. Perfect for retroactive tracking.
            </p>
          </div>

          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-amber-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-500/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Billable Hours</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Track billable vs non-billable time. Set hourly rates and calculate earnings automatically.
            </p>
          </div>

          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-cyan-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-cyan-500/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Recurring Tasks</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Create recurring tasks that automatically generate new instances daily, weekly, or monthly.
            </p>
          </div>

          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-indigo-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Calendar View</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Visualize your tasks and time entries in a calendar format. See your schedule at a glance.
            </p>
          </div>

          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-rose-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-rose-500/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Reports & Export</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Generate detailed reports with filters. Export your data as CSV for analysis and invoicing.
            </p>
          </div>

          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-slate-500/30 transition-colors">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-500/10 flex items-center justify-center mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Dark Mode</h3>
            <p class="text-gray-600 text-xs sm:text-sm">
              Work comfortably in low-light environments with our beautiful dark mode theme.
            </p>
          </div>
        </div>
      </section>

      <section class="mt-20 sm:mt-32 px-4 [content-visibility:auto] [contain-intrinsic-size:0_400px]">
        <div class="text-center mb-10 sm:mb-16">
          <h2 class="text-2xl sm:text-3xl xl:text-4xl font-bold text-gray-900 mb-4">
            How it works
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto text-sm sm:text-base">
            Get started in minutes. Track your time effortlessly.
          </p>
        </div>

        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
          <div class="text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl font-bold text-blue-600">1</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Create Tasks</h3>
            <p class="text-gray-600 text-sm sm:text-base">
              Add tasks with titles, projects, and priorities. Organize your work efficiently.
            </p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl font-bold text-blue-600">2</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Start Tracking</h3>
            <p class="text-gray-600 text-sm sm:text-base">
              Click start to begin tracking time. The timer runs in the background automatically.
            </p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl font-bold text-blue-600">3</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Analyze & Export</h3>
            <p class="text-gray-600 text-sm sm:text-base">
              View insights on your dashboard and export reports for clients or analysis.
            </p>
          </div>
        </div>
      </section>

      <section class="mt-20 sm:mt-32 px-4 [content-visibility:auto] [contain-intrinsic-size:0_300px]">
        <div class="max-w-6xl mx-auto bg-gradient-to-r from-blue-600 to-blue-700 rounded-3xl p-8 sm:p-12 text-white relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-r from-blue-600/50 to-purple-600/50 opacity-50" />
          <div class="relative z-10">
            <div class="text-center mb-8 sm:mb-12">
              <h2 class="text-2xl sm:text-3xl xl:text-4xl font-bold mb-4">
                Why choose FlowTimeUp?
              </h2>
              <p class="text-blue-100 text-sm sm:text-base max-w-2xl mx-auto">
                Everything you need to track time and boost productivity, all in one place.
              </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
              <div class="text-center">
                <div class="text-3xl sm:text-4xl font-bold mb-2">100%</div>
                <div class="text-blue-100 text-sm sm:text-base">Free Forever</div>
              </div>
              <div class="text-center">
                <div class="text-3xl sm:text-4xl font-bold mb-2">∞</div>
                <div class="text-blue-100 text-sm sm:text-base">Unlimited Projects</div>
              </div>
              <div class="text-center">
                <div class="text-3xl sm:text-4xl font-bold mb-2">24/7</div>
                <div class="text-blue-100 text-sm sm:text-base">Always Available</div>
              </div>
              <div class="text-center">
                <div class="text-3xl sm:text-4xl font-bold mb-2">100%</div>
                <div class="text-blue-100 text-sm sm:text-base">Your Data</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="mt-20 sm:mt-32 px-4 [content-visibility:auto] [contain-intrinsic-size:0_350px]">
        <div class="max-w-4xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
            <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-6 sm:p-8">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">Task Templates</h3>
                  <p class="text-gray-600 text-sm">
                    Save time with reusable task templates. Create common tasks once and use them repeatedly.
                  </p>
                </div>
              </div>
            </div>

            <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-6 sm:p-8">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">Dashboard Analytics</h3>
                  <p class="text-gray-600 text-sm">
                    Get insights into your productivity with visual charts and time breakdowns by project.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="mt-20 sm:mt-32 px-4 [content-visibility:auto] [contain-intrinsic-size:0_250px]">
        <div class="max-w-4xl mx-auto text-center bg-gradient-to-r from-blue-600 to-blue-700 rounded-3xl p-8 sm:p-12 text-white">
          <h2 class="text-2xl sm:text-3xl xl:text-4xl font-bold mb-4">
            Ready to boost your productivity?
          </h2>
          <p class="text-blue-100 text-base sm:text-lg mb-8 max-w-2xl mx-auto">
            Join thousands of users who are already tracking their time with FlowTimeUp. Get started in seconds.
          </p>
          <Link
            :href="canRegister ? route('register') : route('login')"
            class="inline-flex items-center gap-2 px-8 py-4 bg-white text-blue-600 rounded-xl font-semibold hover:bg-blue-50 transition-colors shadow-lg"
          >
            Start Tracking Free
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </Link>
        </div>
      </section>
    </main>

    <footer class="relative z-10 border-t border-gray-200 bg-gradient-to-b from-gray-50 to-gray-100 mt-20 sm:mt-32">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-8 sm:mb-12">
          <div>
            <div class="flex items-center gap-2 sm:gap-3 mb-4">
              <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <span class="text-lg sm:text-xl font-bold text-gray-900">FlowTimeUp</span>
            </div>
            <p class="text-gray-600 text-sm">
              Modern time tracking application designed to boost your productivity.
            </p>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Features</h3>
            <ul class="space-y-2 text-sm text-gray-600">
              <li><a href="#features" class="hover:text-gray-900 transition-colors">Time Tracking</a></li>
              <li><a href="#features" class="hover:text-gray-900 transition-colors">Projects & Tags</a></li>
              <li><a href="#features" class="hover:text-gray-900 transition-colors">Reports</a></li>
              <li><a href="#features" class="hover:text-gray-900 transition-colors">Calendar View</a></li>
            </ul>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Resources</h3>
            <ul class="space-y-2 text-sm text-gray-600">
              <li><Link :href="route('pages.documentation')" class="hover:text-gray-900 transition-colors">Documentation</Link></li>
              <li><Link :href="route('pages.api-reference')" class="hover:text-gray-900 transition-colors">API Reference</Link></li>
              <li><Link :href="route('pages.help-center')" class="hover:text-gray-900 transition-colors">Help Center</Link></li>
              <li><Link :href="route('pages.blog')" class="hover:text-gray-900 transition-colors">Blog</Link></li>
            </ul>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Legal</h3>
            <ul class="space-y-2 text-sm text-gray-600">
              <li><Link :href="route('pages.privacy-policy')" class="hover:text-gray-900 transition-colors">Privacy Policy</Link></li>
              <li><Link :href="route('pages.terms-of-service')" class="hover:text-gray-900 transition-colors">Terms of Service</Link></li>
              <li><Link :href="route('pages.cookie-policy')" class="hover:text-gray-900 transition-colors">Cookie Policy</Link></li>
              <li><Link :href="route('pages.gdpr')" class="hover:text-gray-900 transition-colors">GDPR</Link></li>
            </ul>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-8 mt-8">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 sm:gap-3">
              <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <span class="text-gray-600 text-xs sm:text-sm">© 2026 FlowTimeUp. All rights reserved.</span>
            </div>
            <div class="flex items-center gap-4 sm:gap-6 text-xs sm:text-sm text-gray-600">
              <Link :href="route('pages.support')" class="hover:text-gray-900 transition-colors">Support</Link>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
