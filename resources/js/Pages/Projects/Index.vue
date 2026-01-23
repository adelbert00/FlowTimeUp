<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, computed } from 'vue';

interface Project {
  id: number;
  name: string;
  description?: string;
  color?: string;
  tasks_count?: number;
  hourly_rate?: number;
  budget?: number;
  currency?: string;
  access?: string;
  tracked_time?: string;
  tracked_seconds?: number;
  total_amount?: number;
}

const props = defineProps<{
  projects: Project[];
}>();

const showModal = ref(false);
const editingProject = ref<Project | null>(null);
const searchQuery = ref('');
const sortField = ref<string>('name');
const sortDirection = ref<'asc' | 'desc'>('asc');

const form = useForm({
  name: '',
  description: '',
  color: '#6366f1',
  hourly_rate: null as number | null,
  budget: null as number | null,
  currency: 'USD',
  access: 'public',
});

const colorPresets = [
  '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6',
  '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899',
];

const filteredProjects = computed(() => {
  let result = [...props.projects];
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) ||
      p.description?.toLowerCase().includes(query)
    );
  }
  
  result.sort((a, b) => {
    let aVal: any = a[sortField.value as keyof Project];
    let bVal: any = b[sortField.value as keyof Project];
    
    if (sortField.value === 'tracked_seconds') {
      aVal = a.tracked_seconds || 0;
      bVal = b.tracked_seconds || 0;
    } else if (sortField.value === 'total_amount') {
      aVal = a.total_amount || 0;
      bVal = b.total_amount || 0;
    }
    
    if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1;
    if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1;
    return 0;
  });
  
  return result;
});

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
}

function formatCurrency(amount: number, currency: string = 'USD'): string {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency,
    minimumFractionDigits: 2,
  }).format(amount);
}

function openCreateModal() {
  editingProject.value = null;
  form.reset();
  form.color = '#6366f1';
  form.currency = 'USD';
  form.access = 'public';
  showModal.value = true;
}

function openEditModal(project: Project) {
  editingProject.value = project;
  form.name = project.name;
  form.description = project.description || '';
  form.color = project.color || '#6366f1';
  form.hourly_rate = project.hourly_rate || null;
  form.budget = project.budget || null;
  form.currency = project.currency || 'USD';
  form.access = project.access || 'public';
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
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-[1600px] mx-auto pt-20 sm:pt-24 md:pt-28 xl:pt-6 xl:py-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <h1 class="text-2xl font-bold font-sans text-gray-900 dark:text-white">Projects</h1>
          <button
            @click="openCreateModal"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
          >
            CREATE NEW PROJECT
          </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4 flex flex-wrap items-center gap-4">
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">FILTER</span>
          
          <div class="relative">
            <select class="w-full px-3 py-1.5 pr-10 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 appearance-none cursor-pointer">
              <option>Active</option>
              <option>Archived</option>
              <option>All</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
          
          <div class="flex-1 min-w-[200px]">
            <div class="relative">
              <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Find by name"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-4 py-3">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Projects</span>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <th class="px-4 py-3 text-left">
                    <button 
                      @click="toggleSort('name')"
                      class="flex items-center gap-1 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white"
                    >
                      Name
                      <svg v-if="sortField === 'name'" class="w-4 h-4" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                      </svg>
                    </button>
                  </th>
                  <th class="px-4 py-3 text-left">
                    <button 
                      @click="toggleSort('tracked_seconds')"
                      class="flex items-center gap-1 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white"
                    >
                      Tracked
                      <svg v-if="sortField === 'tracked_seconds'" class="w-4 h-4" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                      </svg>
                    </button>
                  </th>
                  <th class="px-4 py-3 text-left">
                    <button 
                      @click="toggleSort('total_amount')"
                      class="flex items-center gap-1 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white"
                    >
                      Amount
                      <svg v-if="sortField === 'total_amount'" class="w-4 h-4" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                      </svg>
                    </button>
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Progress
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Access
                  </th>
                  <th class="px-4 py-3 w-20"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr 
                  v-for="project in filteredProjects"
                  :key="project.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group"
                >
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <span 
                        class="w-3 h-3 rounded-full flex-shrink-0"
                        :style="{ backgroundColor: project.color || '#6366f1' }"
                      />
                      <span class="font-medium text-gray-900 dark:text-white">{{ project.name }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-4">
                    <span class="text-gray-700 dark:text-gray-300 font-mono">{{ project.tracked_time || '00:00:00' }}</span>
                  </td>
                  <td class="px-4 py-4">
                    <span class="text-gray-700 dark:text-gray-300">
                      {{ formatCurrency(project.total_amount || 0, project.currency || 'USD') }}
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <span class="text-gray-500 dark:text-gray-400">-</span>
                  </td>
                  <td class="px-4 py-4">
                    <span 
                      class="inline-flex px-2 py-1 text-xs font-medium rounded"
                      :class="project.access === 'private' ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'"
                    >
                      {{ project.access === 'private' ? 'Private' : 'Public' }}
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button
                        @click="openEditModal(project)"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        title="Edit"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button
                        @click="deleteProject(project)"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                        title="Delete"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="filteredProjects.length === 0" class="flex flex-col items-center justify-center py-12">
            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
              <svg class="w-8 h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
              </svg>
            </div>
            <p class="text-gray-600 dark:text-gray-300 text-lg font-medium">No projects yet</p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 mb-4">Create your first project to start tracking</p>
            <button
              @click="openCreateModal"
              class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
            >
              Create Project
            </button>
          </div>
        </div>

        <Teleport to="body">
          <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click.self="showModal = false"
          >
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
            
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200 dark:border-gray-700 overflow-hidden max-h-[90vh] overflow-y-auto">
              <div class="absolute top-0 left-0 right-0 h-1" :style="{ backgroundColor: form.color }" />
              
              <div class="p-5 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold font-sans text-gray-900 dark:text-white mb-4">
                  {{ editingProject ? 'Edit Project' : 'New Project' }}
                </h3>
                
                <form @submit.prevent="submit" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                      Project name
                    </label>
                    <input
                      v-model="form.name"
                      type="text"
                      placeholder="e.g., Work, Personal"
                      class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                      :class="{ 'border-red-500': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-red-400 text-sm mt-1">
                      {{ form.errors.name }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                      Description
                    </label>
                    <textarea
                      v-model="form.description"
                      rows="2"
                      placeholder="Optional description"
                      class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"
                    />
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Hourly Rate
                      </label>
                      <input
                        v-model.number="form.hourly_rate"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Currency
                      </label>
                      <div class="relative">
                        <select
                          v-model="form.currency"
                          class="w-full px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 appearance-none cursor-pointer"
                        >
                          <option value="USD">USD</option>
                          <option value="EUR">EUR</option>
                          <option value="PLN">PLN</option>
                          <option value="GBP">GBP</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                      Access
                    </label>
                    <div class="relative">
                      <select
                        v-model="form.access"
                        class="w-full px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 appearance-none cursor-pointer"
                      >
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                      Color
                    </label>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="color in colorPresets"
                        :key="color"
                        type="button"
                        @click="form.color = color"
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg transition-transform hover:scale-110"
                        :class="form.color === color ? 'ring-2 ring-blue-500 ring-offset-2 ring-offset-white dark:ring-offset-gray-800' : ''"
                        :style="{ backgroundColor: color }"
                      />
                    </div>
                  </div>

                  <div class="flex gap-3 mt-6">
                    <button
                      type="button"
                      @click="showModal = false"
                      class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
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
