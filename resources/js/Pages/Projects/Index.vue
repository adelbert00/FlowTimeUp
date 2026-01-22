<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref } from 'vue';

interface Project {
  id: number;
  name: string;
  description?: string;
  color?: string;
  tasks_count?: number;
}

const props = defineProps<{
  projects: Project[];
}>();

const showModal = ref(false);
const editingProject = ref<Project | null>(null);

const form = useForm({
  name: '',
  description: '',
  color: '#6366f1',
});

const colorPresets = [
  '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6',
  '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899',
];

function openCreateModal() {
  editingProject.value = null;
  form.reset();
  showModal.value = true;
}

function openEditModal(project: Project) {
  editingProject.value = project;
  form.name = project.name;
  form.description = project.description || '';
  form.color = project.color || '#6366f1';
  showModal.value = true;
}

function submit() {
  if (editingProject.value) {
    form.put(route('projects.update', editingProject.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showModal.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('projects.store'), {
      preserveScroll: true,
      onSuccess: () => {
        showModal.value = false;
        form.reset();
      },
    });
  }
}

function deleteProject(project: Project) {
  if (confirm(`Are you sure you want to delete "${project.name}"?`)) {
    router.delete(route('projects.destroy', project.id));
  }
}
</script>

<template>
  <Head title="Projects" />

  <MainLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100">
      <div class="container mx-auto px-4 sm:px-6 md:px-10 lg:px-8 py-6 sm:py-8 max-w-4xl pt-20 sm:pt-24 md:pt-28 xl:pt-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Projects</h1>
            <p class="text-gray-600 text-sm sm:text-base">Organize your tasks by project</p>
          </div>
          <button
            @click="openCreateModal"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Project
          </button>
        </div>

        <!-- Projects Grid -->
        <div v-if="projects.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
          <div
            v-for="project in projects"
            :key="project.id"
            class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 hover:border-gray-300 transition-colors group"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <div
                  class="w-3 h-3 sm:w-4 sm:h-4 rounded-full flex-shrink-0"
                  :style="{ backgroundColor: project.color || '#6366f1' }"
                />
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ project.name }}</h3>
              </div>
              <div class="flex items-center gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                <button
                  @click="openEditModal(project)"
                  class="p-1.5 sm:p-2 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-gray-100 transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button
                  @click="deleteProject(project)"
                  class="p-1.5 sm:p-2 rounded-lg text-gray-600 hover:text-red-500 hover:bg-red-50 transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>
            <p v-if="project.description" class="text-gray-600 text-xs sm:text-sm mt-2 line-clamp-2">
              {{ project.description }}
            </p>
            <div class="flex items-center gap-4 mt-3 sm:mt-4 text-xs sm:text-sm text-gray-500">
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ project.tasks_count || 0 }} tasks
              </span>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center py-12 sm:py-16 bg-white rounded-xl border border-gray-200 shadow-sm">
          <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
          </div>
          <p class="text-gray-600 text-base sm:text-lg font-medium">No projects yet</p>
          <p class="text-gray-500 text-xs sm:text-sm mt-1 mb-4 text-center px-4">Create your first project to organize tasks</p>
          <button
            @click="openCreateModal"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Project
          </button>
        </div>

        <!-- Modal -->
        <Teleport to="body">
          <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click.self="showModal = false"
          >
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
            
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden">
              <div class="absolute top-0 left-0 right-0 h-1" :style="{ backgroundColor: form.color }" />
              
              <div class="p-5 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">
                  {{ editingProject ? 'Edit Project' : 'New Project' }}
                </h3>
                
                <form @submit.prevent="submit" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                      Project name
                    </label>
                    <input
                      v-model="form.name"
                      type="text"
                      placeholder="e.g., Work, Personal"
                      class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                      :class="{ 'border-red-500': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-red-400 text-sm mt-1">
                      {{ form.errors.name }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                      Description
                    </label>
                    <textarea
                      v-model="form.description"
                      rows="3"
                      placeholder="Optional description"
                      class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                      Color
                    </label>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="color in colorPresets"
                        :key="color"
                        type="button"
                        @click="form.color = color"
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg transition-transform hover:scale-110"
                        :class="form.color === color ? 'ring-2 ring-blue-500 ring-offset-2 ring-offset-white' : ''"
                        :style="{ backgroundColor: color }"
                      />
                    </div>
                  </div>

                  <div class="flex gap-3 mt-6">
                    <button
                      type="button"
                      @click="showModal = false"
                      class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 border border-gray-200 rounded-lg font-medium hover:bg-gray-200 transition-colors"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      :disabled="form.processing"
                      class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 transition-colors"
                    >
                      {{ editingProject ? 'Save Changes' : 'Create Project' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </Teleport>
      </div>
    </div>
  </MainLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
