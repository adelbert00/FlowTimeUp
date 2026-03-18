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
  
  <div class="min-h-screen bg-surface transition-colors overflow-hidden relative">
    <div class="absolute inset-0 bg-gradient-to-b from-surface/0 via-surface to-surface pointer-events-none" />
    
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-accent/20 rounded-full blur-3xl" />
      <div class="absolute top-1/2 -left-40 w-96 h-96 bg-accent/20 rounded-full blur-3xl" />
      <div class="absolute bottom-0 left-0 right-0 h-96 bg-gradient-to-t from-surface to-transparent" />
    </div>

    <nav class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 sm:gap-3">
          <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-accent flex items-center justify-center shadow-lg shadow-accent/25">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-accent-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-lg sm:text-xl font-black text-primary uppercase tracking-tighter">FlowTimeUp</span>
        </div>

        <div v-if="canLogin" class="hidden sm:flex items-center gap-6">
          <Link
            v-if="$page.props.auth.user"
            :href="route('home')"
            class="text-[10px] font-black text-secondary hover:text-primary uppercase tracking-widest transition-colors"
          >
            Dashboard
          </Link>
          <template v-else>
            <Link
              :href="route('login')"
              class="text-[10px] font-black text-secondary hover:text-primary uppercase tracking-widest transition-colors"
            >
              Sign in
            </Link>
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="px-6 py-2.5 bg-accent text-accent-text rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-accent-hover transition-all shadow-lg shadow-accent/20"
            >
              Get Started
            </Link>
          </template>
        </div>

        <button
          v-if="canLogin && !$page.props.auth.user"
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="sm:hidden p-2 text-secondary hover:text-primary transition-colors"
        >
          <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </nav>

    <main class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-16 pb-16 sm:pb-24">
      <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-accent/10 border border-accent/20 rounded-full text-accent text-[10px] font-black uppercase tracking-widest mb-10">
          <span class="w-1.5 h-1.5 bg-accent rounded-full animate-pulse" />
          Modern Time Intelligence
        </div>

        <h1 class="text-4xl sm:text-6xl xl:text-8xl font-black text-primary mb-6 leading-[0.9] tracking-tighter uppercase">
          Track time.<br />
          <span class="text-accent">
            Master Flow.
          </span>
        </h1>

        <p class="text-base sm:text-xl text-secondary mb-12 max-w-2xl mx-auto px-4 font-medium">
          The ultimate interface for professional time tracking. 
          Analyze your performance with surgical precision and modern aesthetics.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20 px-4">
          <Link
            :href="canRegister ? route('register') : route('login')"
            class="w-full sm:w-auto px-10 py-4 bg-accent text-accent-text rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-accent-hover transition-all shadow-xl shadow-accent/20 flex items-center justify-center gap-3 active:scale-95"
          >
            Start Tracking Free
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </Link>
          <a
            href="#features"
            class="w-full sm:w-auto px-10 py-4 bg-surface-raised border border-border text-primary rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-surface-overlay transition-all flex items-center justify-center gap-3"
          >
            Documentation
          </a>
        </div>

        <div class="relative max-w-xl mx-auto px-4 animate-fade-up">
          <div class="absolute inset-0 bg-accent/20 rounded-3xl blur-2xl" />
          <div class="relative bg-surface-raised/80 backdrop-blur-2xl rounded-3xl border border-border p-6 sm:p-10 shadow-2xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 mb-8">
              <div class="text-left">
                <p class="text-[10px] font-black text-muted uppercase tracking-[0.2em] mb-2">Live Session</p>
                <p class="text-primary font-black text-xl tracking-tight uppercase">Architecting v2 Core</p>
              </div>
              <span class="self-start sm:self-center px-3 py-1 bg-accent/10 border border-accent/20 text-accent text-[10px] font-black uppercase tracking-widest rounded-lg">Development</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="font-mono text-4xl sm:text-6xl font-black text-primary tracking-tighter">
                02:45:23<span class="text-accent">.42</span>
              </div>
              <button class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-danger hover:bg-danger/90 flex items-center justify-center transition-all shadow-xl shadow-danger/20 flex-shrink-0 active:scale-90">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="relative z-10 border-t border-border bg-surface-raised/30 mt-20 sm:mt-32">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-8">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-accent flex items-center justify-center">
              <svg class="w-4 h-4 text-accent-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-sm font-black text-primary uppercase tracking-tighter">FlowTimeUp</span>
          </div>
          
          <div class="flex items-center gap-8">
            <Link :href="route('pages.privacy-policy')" class="text-[10px] font-black text-muted hover:text-primary uppercase tracking-widest transition-colors">Privacy</Link>
            <Link :href="route('pages.terms-of-service')" class="text-[10px] font-black text-muted hover:text-primary uppercase tracking-widest transition-colors">Terms</Link>
            <span class="text-[10px] font-black text-muted uppercase tracking-widest">© 2026 FTU_CORE</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>