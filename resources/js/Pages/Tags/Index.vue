<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, computed } from 'vue';

interface Tag {
  id: number;
  name: string;
  is_archived?: boolean;
  tasks_count?: number;
}

const props = defineProps<{
  tags: Tag[];
  showArchived?: boolean;
}>();

const showModal = ref(false);
const editingTag = ref<Tag | null>(null);
const searchQuery = ref('');
const showArchivedFilter = ref(props.showArchived || false);

const form = useForm({
  name: '',
});

const filteredTags = computed(() => {
  let result = [...props.tags];
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(t => t.name.toLowerCase().includes(query));
  }
  
  return result;
});

function toggleShowArchived() {
  showArchivedFilter.value = !showArchivedFilter.value;
  router.get('/tags', { show_archived: showArchivedFilter.value }, { preserveState: true, preserveScroll: true });
}

function openCreateModal() {
  editingTag.value = null;
  form.reset();
  showModal.value = true;
}

function openEditModal(tag: Tag) {
  editingTag.value = tag;
  form.name = tag.name;
  showModal.value = true;
}

function submit() {
  if (editingTag.value) {
    form.put(route('tags.update', editingTag.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showModal.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('tags.store'), {
      preserveScroll: true,
      onSuccess: () => {
        showModal.value = false;
        form.reset();
      },
    });
  }
}

function archiveTag(tag: Tag) {
  router.post(route('tags.archive', tag.id), {}, { preserveScroll: true });
}

function restoreTag(tag: Tag) {
  router.post(route('tags.restore', tag.id), {}, { preserveScroll: true });
}

function deleteTag(tag: Tag) {
  if (confirm(`Are you sure you want to delete "#${tag.name}"?`)) {
    router.delete(route('tags.destroy', tag.id));
  }
}

const quickAddName = ref('');
function quickAddTag() {
  if (!quickAddName.value.trim()) return;
  
  router.post(route('tags.store'), { name: quickAddName.value.trim() }, {
    preserveScroll: true,
    onSuccess: () => {
      quickAddName.value = '';
    },
  });
}
</script>

<template>
  <Head title="Tags" />

  <MainLayout>
    <div class="space-y-6 animate-fade-up">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-primary tracking-tighter uppercase">Tags</h1>
          <p class="text-secondary text-xs font-bold uppercase tracking-widest mt-1">Classification & Organization</p>
        </div>
        
        <div class="flex items-center gap-2 group">
          <input
            v-model="quickAddName"
            @keyup.enter="quickAddTag"
            type="text"
            placeholder="Quick add tag..."
            class="h-10 w-[240px] rounded-xl border border-border bg-surface-raised px-4 text-sm font-normal text-primary placeholder:text-primary/60 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 transition-all"
          />
          <button
            @click="quickAddTag"
            :disabled="!quickAddName.trim()"
            class="inline-flex h-10 shrink-0 items-center justify-center rounded-xl bg-accent px-6 text-xs font-bold uppercase tracking-widest text-white shadow-lg shadow-accent/20 transition-all hover:bg-accent-hover active:scale-95 disabled:cursor-not-allowed disabled:opacity-50"
          >
            Add
          </button>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="bg-surface-raised rounded-2xl border border-border p-4 shadow-sm flex flex-wrap items-center gap-4">
        <button
          @click="toggleShowArchived"
          type="button"
          class="inline-flex h-10 items-center gap-2 rounded-xl border border-border bg-surface-raised px-4 text-xs font-bold uppercase tracking-wider text-secondary transition-all hover:text-primary"
        >
          <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          {{ showArchivedFilter ? 'Show All' : 'Active Only' }}
        </button>
        
        <div class="flex-1 min-w-[240px] max-w-md">
          <div class="relative group">
            <svg class="w-4 h-4 text-muted absolute left-4 top-1/2 -translate-y-1/2 group-focus-within:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search tags..."
              class="h-10 w-full rounded-xl border border-border bg-surface-raised pl-11 pr-4 text-sm font-normal text-primary placeholder:text-primary/60 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 transition-all"
            />
          </div>
        </div>
      </div>

      <!-- Tags Grid/List -->
      <div class="bg-surface-raised rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-border bg-surface-raised px-6 py-4">
          <span class="text-xs font-bold uppercase tracking-wider text-primary">Management</span>
          <span class="rounded border border-border bg-surface-raised px-2 py-1 text-xs font-semibold text-muted">{{ filteredTags.length }} Tags</span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-surface-raised text-xs font-bold uppercase tracking-wider text-secondary">
                <th class="px-6 py-4 border-b border-border w-12">
                  <div class="flex justify-center">
                    <input type="checkbox" class="w-4 h-4 rounded border-border bg-surface-raised text-accent focus:ring-accent" />
                  </div>
                </th>
                <th class="px-6 py-4 border-b border-border">Tag Identity</th>
                <th class="px-6 py-4 border-b border-border w-32"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr 
                v-for="tag in filteredTags"
                :key="tag.id"
                class="hover:bg-border/10 transition-colors group"
                :class="{ 'opacity-50 grayscale': tag.is_archived }"
              >
                <td class="px-6 py-5">
                  <div class="flex justify-center">
                    <input type="checkbox" class="w-4 h-4 rounded border-border bg-surface-raised text-accent focus:ring-accent" />
                  </div>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-3">
                    <div class="p-2 bg-surface-raised rounded-lg border border-border group-hover:border-accent/30 transition-all">
                      <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div class="flex flex-col">
                      <span class="text-sm font-medium text-primary transition-colors group-hover:text-accent">#{{ tag.name }}</span>
                      <span v-if="tag.is_archived" class="mt-0.5 text-xs font-semibold uppercase tracking-wide text-muted">Archived</span>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="openEditModal(tag)" class="p-2 rounded-lg text-muted hover:text-accent hover:bg-border/15 transition-all" title="Edit">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    
                    <button v-if="!tag.is_archived" @click="archiveTag(tag)" class="p-2 rounded-lg text-muted hover:text-warning hover:bg-warning/10 transition-all" title="Archive">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </button>
                    <button v-else @click="restoreTag(tag)" class="p-2 rounded-lg text-muted hover:text-success hover:bg-success/10 transition-all" title="Restore">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>

                    <button @click="deleteTag(tag)" class="p-2 rounded-lg text-muted hover:text-danger hover:bg-danger/10 transition-all" title="Delete">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="filteredTags.length === 0" class="flex flex-col items-center justify-center py-20 bg-surface-raised">
          <div class="w-16 h-16 rounded-2xl bg-surface-raised flex items-center justify-center mb-6 border border-border group">
            <svg class="w-8 h-8 text-muted group-hover:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
          </div>
          <p class="text-base font-bold text-primary">No tags found</p>
          <p class="mb-8 text-sm text-secondary">Tags help you categorize sessions for better reporting.</p>
        </div>
      </div>

      <Teleport to="body">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-scale-in" @click.self="showModal = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          
          <div class="relative bg-surface-raised rounded-2xl shadow-2xl max-w-md w-full border border-border overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-accent shadow-sm" />
            
            <div class="p-8">
              <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-primary uppercase tracking-tighter">
                  {{ editingTag ? 'Edit Tag' : 'New Tag' }}
                </h3>
                <button @click="showModal = false" class="p-2 text-muted hover:text-primary transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
              
              <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                  <label class="text-xs font-bold uppercase tracking-wider text-secondary">Tag Name</label>
                  <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-accent font-black">#</span>
                    <input
                      v-model="form.name"
                      type="text"
                      placeholder="urgent, creative, billable..."
                      class="h-10 w-full rounded-xl border border-border bg-surface-raised py-0 pl-9 pr-4 text-sm font-normal text-primary placeholder:text-primary/60 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 transition-all"
                      :class="{ 'border-danger': form.errors.name }"
                    />
                  </div>
                  <p v-if="form.errors.name" class="text-xs font-medium text-danger">{{ form.errors.name }}</p>
                </div>

                <div class="flex gap-4 pt-4">
                  <button
                    type="button"
                    @click="showModal = false"
                    class="flex-1 px-6 py-3.5 bg-surface-raised text-primary border border-border rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-border transition-all"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex-1 px-6 py-3.5 bg-accent text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-accent-hover disabled:opacity-50 transition-all shadow-lg shadow-accent/20 active:scale-95"
                  >
                    {{ editingTag ? 'Save Changes' : 'Create Tag' }}
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
