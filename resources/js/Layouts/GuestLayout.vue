<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="relative min-h-screen bg-surface transition-colors overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-60 sm:w-80 h-60 sm:h-80 bg-accent/10 rounded-full blur-2xl sm:blur-3xl" />
      <div class="absolute -bottom-40 -left-40 w-60 sm:w-80 h-60 sm:h-80 bg-accent/10 rounded-full blur-2xl sm:blur-3xl" />
    </div>

    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 animate-fade-up">
      <div class="relative w-full max-w-md">
        <div class="absolute inset-0 bg-accent/20 rounded-3xl blur-2xl" />
        <div class="relative bg-surface-raised/90 backdrop-blur-2xl rounded-3xl border border-border shadow-2xl p-6 sm:p-10">
          <Link href="/" class="flex items-center gap-3 mb-10 justify-center group">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-accent flex items-center justify-center shadow-lg shadow-accent/25 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 sm:w-7 sm:h-7 text-accent-text" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xl sm:text-2xl font-black text-primary uppercase tracking-tighter">FlowTimeUp</span>
          </Link>

          <main role="main">
            <slot />
          </main>

          <p class="mt-10 text-[10px] font-black text-muted uppercase tracking-[0.2em] text-center pt-6 border-t border-border/50">
            © 2026 FTU_CORE // Authorized Access Only
          </p>
        </div>
      </div>
    </div>
  </div>
</template>