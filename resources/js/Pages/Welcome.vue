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

const features = [
  {
    title: 'Atomic Precision',
    description: 'Nanosecond-accurate time tracking engine built for high-performance workflows. Zero overhead, maximum output.',
    icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`
  },
  {
    title: 'Architectural Reports',
    description: 'Surgical data visualization. Break down every billable second with heatmaps, charts, and granular export options.',
    icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>`
  },
  {
    title: 'Flow-State Focus',
    description: 'Clean, distraction-free interface designed to keep you in the zone. Aesthetic utility at its peak.',
    icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`
  }
];

const steps = [
  {
    id: '01',
    title: 'Initialize Session',
    description: 'Define your objective. One click starts the high-resolution timer.'
  },
  {
    id: '02',
    title: 'Maintain Flow',
    description: 'Work without interruptions. FTU_CORE handles the telemetry in the background.'
  },
  {
    id: '03',
    title: 'Analyze & Optimize',
    description: 'Review your performance metrics. Refine your process based on hard data.'
  }
];
</script>

<template>
  <Head>
    <title>FlowTimeUp - Track Your Time | Modern Time Tracking Application</title>
    <meta name="description" content="FlowTimeUp - Modern time tracking application. Track your time across tasks and projects with an intuitive interface. Boost productivity with precise time tracking and insightful reports." />
    <meta name="keywords" content="time tracking, task management, productivity, project management, time tracker" />
    <link rel="canonical" :href="canonicalUrl" />
  </Head>
  
  <div class="min-h-screen bg-surface transition-colors overflow-hidden relative selection:bg-accent/30">
    <!-- Technical Background Grid -->
    <div class="fixed inset-0 pointer-events-none opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 40px 40px;"></div>
    
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-accent/10 rounded-full blur-[120px]" />
      <div class="absolute top-1/2 -left-40 w-96 h-96 bg-accent/10 rounded-full blur-[120px]" />
      <div class="absolute bottom-0 left-0 right-0 h-96 bg-gradient-to-t from-surface via-surface/80 to-transparent" />
    </div>

    <nav class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 border-2 border-accent flex items-center justify-center relative group">
            <div class="absolute inset-0 bg-accent/10 group-hover:bg-accent/20 transition-colors"></div>
            <svg class="w-6 h-6 text-accent relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-xl font-black text-primary uppercase tracking-[-0.05em]">FlowTimeUp<span class="text-accent">_CORE</span></span>
        </div>

        <div v-if="canLogin" class="hidden sm:flex items-center gap-8">
          <Link
            v-if="$page.props.auth.user"
            :href="route('home')"
            class="text-[10px] font-bold text-secondary hover:text-accent uppercase tracking-widest transition-colors border-b-2 border-transparent hover:border-accent"
          >
            Terminal
          </Link>
          <template v-else>
            <Link
              :href="route('login')"
              class="text-[10px] font-bold text-secondary hover:text-accent uppercase tracking-widest transition-colors"
            >
              Sign In
            </Link>
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="px-6 py-2 bg-accent text-accent-text text-[10px] font-black uppercase tracking-widest hover:bg-accent/90 transition-all border border-accent"
            >
              Access System
            </Link>
          </template>
        </div>

        <button
          v-if="canLogin && !$page.props.auth.user"
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="sm:hidden p-2 text-secondary hover:text-accent transition-colors"
        >
          <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </nav>

    <main class="relative z-10">
      <!-- Hero Section -->
      <section class="container mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-24 pb-20">
        <div class="max-w-5xl mx-auto">
          <div class="inline-flex items-center gap-2 px-3 py-1 border border-accent/30 text-accent text-[9px] font-bold uppercase tracking-[0.3em] mb-12 bg-accent/5">
            <span class="w-1 h-1 bg-accent" />
            System Protocol v2.4.0
          </div>

          <h1 class="text-5xl sm:text-7xl lg:text-9xl font-black text-primary mb-8 leading-[0.85] tracking-tighter uppercase italic">
            Precision<br />
            <span class="text-accent">Telemetry.</span>
          </h1>

          <div class="grid sm:grid-cols-2 gap-12 items-end">
            <p class="text-lg text-secondary font-medium border-l-2 border-accent/20 pl-6 max-w-md">
              High-performance time architecture for technical minds. Engineered to measure, analyze, and optimize human output.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
              <Link
                :href="canRegister ? route('register') : route('login')"
                class="px-10 py-4 bg-accent text-accent-text text-[11px] font-black uppercase tracking-widest hover:invert transition-all flex items-center justify-center gap-3"
              >
                Initialize Account
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
              </Link>
              <a
                href="#features"
                class="px-10 py-4 border border-border text-primary text-[11px] font-black uppercase tracking-widest hover:bg-primary hover:text-surface transition-all flex items-center justify-center"
              >
                View Specs
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- Dashboard Preview -->
      <section class="container mx-auto px-4 mb-32">
        <div class="relative max-w-5xl mx-auto">
          <div class="absolute -inset-4 bg-accent/5 blur-3xl rounded-[40px] pointer-events-none"></div>
          <div class="relative bg-surface-raised border border-border p-4 sm:p-8 shadow-[0_0_50px_rgba(0,0,0,0.1)]">
            <div class="flex items-center justify-between mb-8 border-b border-border pb-4">
              <div class="flex gap-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-border"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-border"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-border"></div>
              </div>
              <div class="text-[9px] font-mono text-muted uppercase tracking-widest">ftu_session_manager.exe</div>
            </div>
            
            <div class="grid lg:grid-cols-3 gap-8">
              <div class="lg:col-span-2 space-y-6">
                <div class="p-6 border border-accent/20 bg-accent/5">
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                      <p class="text-[9px] font-bold text-accent uppercase tracking-widest mb-1">Active Objective</p>
                      <h3 class="text-2xl font-black text-primary tracking-tight uppercase">System Refactor: Core_API</h3>
                    </div>
                    <div class="font-mono text-4xl font-black text-primary">
                      03:12:45<span class="text-accent animate-pulse">.88</span>
                    </div>
                  </div>
                  <div class="h-1 bg-border w-full relative">
                    <div class="absolute inset-y-0 left-0 bg-accent w-3/4"></div>
                  </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                  <div class="p-4 border border-border bg-surface">
                    <p class="text-[9px] font-bold text-muted uppercase tracking-widest mb-2">Total Efficiency</p>
                    <p class="text-xl font-black text-primary">94.2%</p>
                  </div>
                  <div class="p-4 border border-border bg-surface">
                    <p class="text-[9px] font-bold text-muted uppercase tracking-widest mb-2">Billable Delta</p>
                    <p class="text-xl font-black text-primary">+$1,240.00</p>
                  </div>
                </div>
              </div>
              <div class="space-y-4">
                <div class="text-[10px] font-black text-primary uppercase border-b border-border pb-2">Recent Logs</div>
                <div v-for="i in 4" :key="i" class="flex justify-between items-center text-[10px] font-mono text-secondary">
                  <span>LOG_SESSION_{{ 1000 + i }}</span>
                  <span class="text-accent">COMPLETED</span>
                </div>
                <button class="w-full py-3 mt-4 bg-danger text-white text-[10px] font-black uppercase tracking-widest hover:bg-danger/90 transition-colors">
                  Terminate Session
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section id="features" class="container mx-auto px-4 py-24 border-t border-border">
        <div class="max-w-5xl mx-auto">
          <div class="grid lg:grid-cols-3 gap-12">
            <div v-for="feature in features" :key="feature.title" class="group">
              <div class="w-12 h-12 border border-accent flex items-center justify-center text-accent mb-6 group-hover:bg-accent group-hover:text-accent-text transition-all duration-300">
                <div v-html="feature.icon"></div>
              </div>
              <h3 class="text-lg font-black text-primary uppercase tracking-tight mb-4">{{ feature.title }}</h3>
              <p class="text-sm text-secondary leading-relaxed">
                {{ feature.description }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- How It Works (Steps) -->
      <section class="bg-primary text-surface py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
          <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tighter mb-16 italic">Deployment Workflow</h2>
            <div class="grid sm:grid-cols-3 gap-12">
              <div v-for="step in steps" :key="step.id" class="relative">
                <div class="text-6xl font-black text-surface/10 absolute -top-10 -left-4 pointer-events-none">{{ step.id }}</div>
                <h3 class="text-xl font-black uppercase mb-4 relative z-10">{{ step.title }}</h3>
                <p class="text-surface/60 text-sm leading-relaxed">
                  {{ step.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Final CTA -->
      <section class="container mx-auto px-4 py-32 text-center">
        <div class="max-w-3xl mx-auto">
          <h2 class="text-4xl sm:text-6xl font-black text-primary uppercase tracking-tighter mb-8 leading-none">Ready to upgrade your<br /><span class="text-accent">temporal stack?</span></h2>
          <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <Link
              :href="canRegister ? route('register') : route('login')"
              class="w-full sm:w-auto px-12 py-5 bg-accent text-accent-text text-xs font-black uppercase tracking-widest hover:scale-105 transition-transform"
            >
              Start Deployment
            </Link>
          </div>
        </div>
      </section>
    </main>

    <footer class="relative z-10 border-t border-border bg-surface py-12">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-8">
          <div class="flex items-center gap-3">
            <div class="w-6 h-6 border-2 border-accent flex items-center justify-center">
              <div class="w-2 h-2 bg-accent"></div>
            </div>
            <span class="text-sm font-black text-primary uppercase tracking-tight">FlowTimeUp_CORE</span>
          </div>
          
          <div class="flex items-center gap-10">
            <Link :href="route('pages.privacy-policy')" class="text-[9px] font-bold text-muted hover:text-accent uppercase tracking-widest transition-colors">Security_Protocol</Link>
            <Link :href="route('pages.terms-of-service')" class="text-[9px] font-bold text-muted hover:text-accent uppercase tracking-widest transition-colors">Terms_of_Service</Link>
            <span class="text-[9px] font-mono text-muted uppercase tracking-widest opacity-50">© 2026 CLAW_SYSTEMS</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.selection\:bg-accent\/30 ::selection {
  background-color: rgba(var(--accent-rgb), 0.3);
}
</style>
