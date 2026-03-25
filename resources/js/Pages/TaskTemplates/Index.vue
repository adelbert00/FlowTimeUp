<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

interface Template {
  id: number;
  name: string;
  title: string;
  description?: string;
  project_id?: number | null;
  project?: {
    name: string;
    color?: string;
  };
  priority: 'low' | 'medium' | 'high';
  tag_ids: number[];
}

const props = defineProps<{
  templates: Template[];
}>();

function useTemplate(templateId: number) {
  router.post(route('task-templates.use', templateId));
}

function deleteTemplate(templateId: number) {
  if (confirm('Are you sure you want to delete this template?')) {
    router.delete(route('task-templates.destroy', templateId));
  }
}
</script>

<template>
  <Head title="Task Templates" />

  <MainLayout>
    <div class="space-y-6 animate-fade-up">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-primary tracking-tighter uppercase">Templates</h1>
          <p class="text-secondary text-xs font-bold uppercase tracking-widest mt-1">Workflow Automation Hub</p>
        </div>
        
        <button
          type="button"
          @click="router.visit(route('task-templates.create'))"
          class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-accent px-6 text-xs font-bold uppercase tracking-widest text-white shadow-lg shadow-accent/20 transition-all hover:bg-accent-hover active:scale-95"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          New Template
        </button>
      </div>

      <div class="bg-surface-raised rounded-2xl border border-border p-6 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
        <div class="flex items-start gap-6 relative z-10">
          <div class="w-12 h-12 rounded-xl bg-surface-raised flex items-center justify-center flex-shrink-0 border border-border">
            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="mb-2 text-base font-bold text-primary">What are Task Templates?</h3>
            <p class="mb-4 text-sm text-secondary">
              Task Templates allow you to create reusable task configurations that you can quickly apply when creating new tasks. 
              This is perfect for recurring tasks, standard workflows, or tasks that follow a similar pattern.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="flex items-center gap-2 p-3 bg-surface-raised rounded-xl border border-border">
                <svg class="h-4 w-4 shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-xs font-medium leading-snug text-secondary"><strong class="font-semibold text-primary">Save time:</strong> Pre-configure everything</span>
              </div>
              <div class="flex items-center gap-2 p-3 bg-surface-raised rounded-xl border border-border">
                <svg class="h-4 w-4 shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-xs font-medium leading-snug text-secondary"><strong class="font-semibold text-primary">Consistency:</strong> Uniform structure</span>
              </div>
              <div class="flex items-center gap-2 p-3 bg-surface-raised rounded-xl border border-border">
                <svg class="h-4 w-4 shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-xs font-medium leading-snug text-secondary"><strong class="font-semibold text-primary">Quick creation:</strong> One-click setup</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="templates.length === 0" class="flex flex-col items-center justify-center py-20 bg-surface-raised border border-border border-dashed rounded-2xl">
        <div class="w-16 h-16 rounded-2xl bg-surface-raised flex items-center justify-center mb-6 border border-border group">
          <svg class="w-8 h-8 text-muted group-hover:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <p class="text-base font-bold text-primary">No templates yet</p>
        <p class="mb-8 text-sm text-secondary">Templates help you automate recurring task creation.</p>
        <button
          type="button"
          @click="router.visit(route('task-templates.create'))"
          class="inline-flex h-10 items-center justify-center rounded-xl bg-accent px-8 text-xs font-bold uppercase tracking-widest text-white shadow-lg shadow-accent/20 transition-all hover:bg-accent-hover"
        >
          Create first template
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="template in templates"
          :key="template.id"
          class="bg-surface-raised rounded-2xl border border-border shadow-sm p-6 hover:border-accent/30 transition-all group flex flex-col"
        >
          <div class="flex items-start justify-between mb-4">
            <h3 class="text-sm font-bold uppercase tracking-tight text-primary transition-colors group-hover:text-accent">{{ template.name }}</h3>
            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <button
                @click="router.visit(route('task-templates.edit', template.id))"
                class="p-2 text-muted hover:text-accent hover:bg-border/15 rounded-lg transition-all"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
              <button
                @click="deleteTemplate(template.id)"
                class="p-2 text-muted hover:text-danger hover:bg-danger/10 rounded-lg transition-all"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
          
          <div class="space-y-2 mb-6 flex-1">
            <p class="text-sm font-medium text-primary">{{ template.title }}</p>
            <p v-if="template.description" class="line-clamp-2 text-xs leading-relaxed text-secondary">{{ template.description }}</p>
          </div>
          
          <div class="flex items-center gap-2 mb-6">
            <span
              v-if="template.project"
              class="rounded border px-2 py-1 text-xs font-semibold uppercase tracking-wide"
              :style="{ backgroundColor: template.project.color + '10', color: template.project.color, borderColor: template.project.color + '30' }"
            >
              {{ template.project.name }}
            </span>
            <span
              class="rounded border px-2 py-1 text-xs font-semibold uppercase tracking-wide"
              :class="template.priority === 'high' ? 'bg-danger/10 text-danger border-danger/20'
                : template.priority === 'medium' ? 'bg-warning/10 text-warning border-warning/20'
                : 'bg-success/10 text-success border-success/20'"
            >
              {{ template.priority }}
            </span>
          </div>
          
          <button
            type="button"
            @click="useTemplate(template.id)"
            class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-accent px-4 text-xs font-bold uppercase tracking-widest text-white shadow-lg shadow-accent/20 transition-all hover:bg-accent-hover active:scale-95"
          >
            Use Template
          </button>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
