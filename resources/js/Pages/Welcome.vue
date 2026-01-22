<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
  canLogin?: boolean;
  canRegister?: boolean;
  laravelVersion?: string;
  phpVersion?: string;
}>();

const mobileMenuOpen = ref(false);
</script>

<template>
  <Head title="FlowTimeUp - Track Your Time" />
  
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl" />
      <div class="absolute top-1/2 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl" />
      <div class="absolute -bottom-40 right-1/3 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl" />
    </div>

    <!-- Navigation -->
    <nav class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center gap-2 sm:gap-3">
          <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-500/25">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-lg sm:text-xl font-bold text-gray-900">FlowTimeUp</span>
        </div>

        <!-- Desktop Auth Links -->
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

        <!-- Mobile menu button -->
        <button
          v-if="canLogin && !$page.props.auth.user"
          @click="mobileMenuOpen = !mobileMenuOpen"
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

      <!-- Mobile menu -->
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

    <!-- Hero Section -->
    <main class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-16 pb-16 sm:pb-24">
      <div class="max-w-4xl mx-auto text-center">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 bg-blue-100 border border-blue-200 rounded-full text-blue-600 text-xs sm:text-sm font-medium mb-6 sm:mb-8">
          <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse" />
          Free time tracking for everyone
        </div>

        <!-- Heading -->
        <h1 class="text-3xl sm:text-5xl xl:text-7xl font-bold text-gray-900 mb-4 sm:mb-6 leading-tight">
          Track time.<br />
          <span class="bg-gradient-to-r from-blue-500 via-blue-600 to-cyan-500 bg-clip-text text-transparent">
            Stay productive.
          </span>
        </h1>

        <!-- Subheading -->
        <p class="text-base sm:text-xl text-gray-600 mb-8 sm:mb-10 max-w-2xl mx-auto px-4">
          The simplest way to track your time across tasks and projects. 
          Boost your productivity with precise time tracking and insightful reports.
        </p>

        <!-- CTA Buttons -->
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

        <!-- Timer Demo -->
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
              <button class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-red-500 hover:bg-red-600 flex items-center justify-center transition-colors shadow-lg shadow-red-500/25 flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Features Section -->
      <section id="features" class="mt-20 sm:mt-32 px-4">
        <div class="text-center mb-10 sm:mb-16">
          <h2 class="text-2xl sm:text-3xl xl:text-4xl font-bold text-gray-900 mb-4">
            Everything you need to track time
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto text-sm sm:text-base">
            Simple, powerful features designed to help you stay focused and productive.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
          <!-- Feature 1 -->
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

          <!-- Feature 2 -->
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

          <!-- Feature 3 -->
          <div class="bg-white/50 rounded-2xl border border-gray-200/50 p-5 sm:p-6 hover:border-emerald-500/30 transition-colors sm:col-span-2 xl:col-span-1">
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
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-gray-200">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-2 sm:gap-3">
            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center">
              <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-gray-600 text-xs sm:text-sm">© 2025 FlowTimeUp. All rights reserved.</span>
          </div>
          <div class="flex items-center gap-4 sm:gap-6 text-xs sm:text-sm text-gray-600">
            <a href="#" class="hover:text-gray-900 transition-colors">Privacy</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Terms</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Support</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
