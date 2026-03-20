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
    <div class="space-y-6 animate-fade-up">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold font-sans text-primary">Projects</h1>
        <button
          @click="openCreateModal"
          class="flex items-center justify-center gap-2 px-6 py-2.5 bg-accent text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-accent-hover transition-all shadow-lg shadow-accent/20 active:scale-95"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          Create Project
        </button>
      </div>

      <!-- Filter Bar -->
      <div class="bg-surface-raised rounded-2xl border border-border p-4 shadow-sm flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-surface-overlay rounded-lg border border-border">
          <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
          <span class="text-[10px] font-black text-secondary uppercase tracking-widest">Filter</span>
        </div>
        
        <div class="relative">
          <select class="pl-4 pr-10 py-2 bg-surface-overlay border border-border rounded-xl text-sm text-primary appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-accent transition-all">
            <option>Active</option>
            <option>Archived</option>
            <option>All</option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
          </div>
        </div>
        
        <div class="flex-1 min-w-[240px]">
          <div class="relative group">
            <svg class="w-4 h-4 text-muted absolute left-4 top-1/2 -translate-y-1/2 group-focus-within:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search projects..."
              class="w-full pl-11 pr-4 py-2 bg-surface-overlay border border-border rounded-xl text-sm text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all"
            />
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-surface-raised rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="bg-surface-overlay/30 px-6 py-4 border-b border-border flex items-center justify-between">
          <span class="text-xs font-bold text-primary uppercase tracking-widest">Project List</span>
          <span class="text-[10px] font-black text-muted bg-surface-overlay px-2 py-1 rounded border border-border">{{ filteredProjects.length }} Total</span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-surface-overlay/50 text-[10px] font-black text-secondary uppercase tracking-[0.2em]">
                <th class="px-6 py-4 border-b border-border">
                  <button @click="toggleSort('name')" class="flex items-center gap-1.5 hover:text-primary transition-colors">
                    Name
                    <svg v-if="sortField === 'name'" class="w-3 h-3 text-accent" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                  </button>
                </th>
                <th class="px-6 py-4 border-b border-border">
                  <button @click="toggleSort('tracked_seconds')" class="flex items-center gap-1.5 hover:text-primary transition-colors">
                    Tracked
                    <svg v-if="sortField === 'tracked_seconds'" class="w-3 h-3 text-accent" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                  </button>
                </th>
                <th class="px-6 py-4 border-b border-border">
                  <button @click="toggleSort('total_amount')" class="flex items-center gap-1.5 hover:text-primary transition-colors">
                    Amount
                    <svg v-if="sortField === 'total_amount'" class="w-3 h-3 text-accent" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                  </button>
                </th>
                <th class="px-6 py-4 border-b border-border">Access</th>
                <th class="px-6 py-4 border-b border-border w-24"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr 
                v-for="project in filteredProjects"
                :key="project.id"
                class="hover:bg-surface-overlay/30 transition-colors group"
              >
                <td class="px-6 py-5">
                  <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full ring-2 ring-surface-raised shadow-sm" :style="{ backgroundColor: project.color || '#6366f1' }" />
                    <span class="font-bold text-primary text-sm group-hover:text-accent transition-colors">{{ project.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span class="text-secondary font-mono text-xs font-bold bg-surface-overlay px-2 py-1 rounded border border-border group-hover:text-primary transition-colors">{{ project.tracked_time || '00:00:00' }}</span>
                </td>
                <td class="px-6 py-5">
                  <span class="text-primary font-bold text-sm">
                    {{ formatCurrency(project.total_amount || 0, project.currency || 'USD') }}
                  </span>
                </td>
                <td class="px-6 py-5">
                  <span 
                    class="inline-flex px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded border"
                    :class="project.access === 'private' ? 'bg-surface-overlay border-border text-muted' : 'bg-accent/10 border-accent/20 text-accent'"
                  >
                    {{ project.access === 'private' ? 'Private' : 'Public' }}
                  </span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="openEditModal(project)" class="p-2 rounded-lg text-muted hover:text-accent hover:bg-surface-overlay transition-all" title="Edit">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button @click="deleteProject(project)" class="p-2 rounded-lg text-muted hover:text-danger hover:bg-danger/10 transition-all" title="Delete">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="filteredProjects.length === 0" class="flex flex-col items-center justify-center py-20 bg-surface-overlay/10">
          <div class="w-16 h-16 rounded-2xl bg-surface-overlay flex items-center justify-center mb-6 border border-border group">
            <svg class="w-8 h-8 text-muted group-hover:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
          </div>
          <p class="text-primary font-bold text-lg">No projects found</p>
          <p class="text-secondary text-sm mb-8">Create your first project to start tracking your time.</p>
          <button @click="openCreateModal" class="px-8 py-3 bg-accent text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-accent-hover transition-all shadow-lg shadow-accent/20">
            Add New Project
          </button>
        </div>
      </div>

      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-scale-in" @click.self="showModal = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          
          <div class="relative bg-surface-raised rounded-2xl shadow-2xl max-w-lg w-full border border-border overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5 shadow-sm" :style="{ backgroundColor: form.color }" />
            
            <div class="p-8">
              <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-primary uppercase tracking-tighter">
                  {{ editingProject ? 'Edit Project' : 'New Project' }}
                </h3>
                <button @click="showModal = false" class="p-2 text-muted hover:text-primary transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
              
              <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Project Name</label>
                  <input
                    v-model="form.name"
                    type="text"
                    placeholder="Enter project name..."
                    class="w-full px-4 py-3 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all"
                    :class="{ 'border-danger': form.errors.name }"
                  />
                  <p v-if="form.errors.name" class="text-danger text-[10px] font-bold uppercase tracking-wider">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <label class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Description</label>
                  <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Brief description of the project..."
                    class="w-full px-4 py-3 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all resize-none"
                  />
                </div>

                <div class="grid grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Hourly Rate</label>
                    <div class="relative">
                      <input
                        v-model.number="form.hourly_rate"
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        class="w-full px-4 py-3 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all"
                      />
                    </div>
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Currency</label>
                    <div class="relative">
                      <select v-model="form.currency" class="w-full px-4 py-3 pr-10 bg-surface-overlay border border-border rounded-xl text-primary appearance-none focus:outline-none focus:ring-2 focus:ring-accent transition-all">
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="PLN">PLN</option>
                        <option value="GBP">GBP</option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="space-y-4">
                  <label class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Label Color</label>
                  <div class="flex flex-wrap gap-2.5">
                    <button
                      v-for="color in colorPresets"
                      :key="color"
                      type="button"
                      @click="form.color = color"
                      class="w-8 h-8 rounded-lg transition-all hover:scale-110 shadow-sm"
                      :class="form.color === color ? 'ring-2 ring-accent ring-offset-4 ring-offset-surface-raised scale-110 shadow-lg' : 'opacity-80 hover:opacity-100'"
                      :style="{ backgroundColor: color }"
                    />
                  </div>
                </div>

                <div class="flex gap-4 pt-4">
                  <button
                    type="button"
                    @click="showModal = false"
                    class="flex-1 px-6 py-3.5 bg-surface-overlay text-primary border border-border rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-border transition-all"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex-1 px-6 py-3.5 bg-accent text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-accent-hover disabled:opacity-50 transition-all shadow-lg shadow-accent/20 active:scale-95"
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
  </MainLayout>
</template>
