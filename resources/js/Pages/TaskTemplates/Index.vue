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
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 max-w-6xl pt-20 sm:pt-24 md:pt-28 xl:pt-8">
        <div class="mb-6 sm:mb-8 flex items-center justify-between">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold font-sans text-gray-900 dark:text-white mb-2">Task Templates</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">Create reusable task templates</p>
          </div>
          
          <button
            @click="router.visit(route('task-templates.create'))"
            class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm sm:text-base"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Template
          </button>
        </div>

        <div class="bg-gradient-to-r from-blue-600/10 to-blue-600/10 dark:from-blue-600/20 dark:to-blue-600/20 rounded-xl border border-blue-200 dark:border-blue-800 p-4 sm:p-6 mb-6">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-600/20 dark:bg-blue-600/30 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-base sm:text-lg font-semibold font-sans text-gray-900 dark:text-white mb-2">What are Task Templates?</h3>
              <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base mb-3">
                Task Templates allow you to create reusable task configurations that you can quickly apply when creating new tasks. 
                This is perfect for recurring tasks, standard workflows, or tasks that follow a similar pattern.
              </p>
              <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span><strong>Save time:</strong> Pre-configure title, description, project, priority, and tags</span>
                </div>
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span><strong>Consistency:</strong> Ensure similar tasks always have the same structure and settings</span>
                </div>
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span><strong>Quick creation:</strong> Click "Use Template" to instantly create a task with all settings pre-filled</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="templates.length === 0" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-12 text-center">
          <p class="text-gray-600 dark:text-gray-400 mb-4">No templates yet</p>
          <button
            @click="router.visit(route('task-templates.create'))"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
          >
            Create your first template
          </button>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="template in templates"
            :key="template.id"
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 sm:p-6 hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
          >
            <div class="flex items-start justify-between mb-3">
              <h3 class="text-lg font-semibold font-sans text-gray-900 dark:text-white">{{ template.name }}</h3>
              <div class="flex gap-2">
                <button
                  @click="router.visit(route('task-templates.edit', template.id))"
                  class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button
                  @click="deleteTemplate(template.id)"
                  class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-red-400 transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ template.title }}</p>
            <p v-if="template.description" class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2">{{ template.description }}</p>
            
            <div class="flex items-center gap-2 mb-4">
              <span
                v-if="template.project"
                class="text-xs px-2 py-1 rounded"
                :style="{ backgroundColor: template.project.color + '20', color: template.project.color, border: '1px solid ' + template.project.color + '50' }"
              >
                {{ template.project.name }}
              </span>
              <span
                class="text-xs px-2 py-1 rounded capitalize"
                :class="template.priority === 'high' ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700'
                  : template.priority === 'medium' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-700'
                  : 'bg-emerald-500/20 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-500/50 dark:border-emerald-700'"
              >
                {{ template.priority }}
              </span>
            </div>
            
            <button
              @click="useTemplate(template.id)"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm"
            >
              Use Template
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
