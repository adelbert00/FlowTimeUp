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
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-[1200px] mx-auto pt-20 sm:pt-24 md:pt-28 xl:pt-6 xl:py-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <h1 class="text-2xl font-bold font-sans text-gray-900 dark:text-white">Tags</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4 flex flex-wrap items-center gap-4">
          <div class="relative">
            <button
              @click="toggleShowArchived"
              class="flex items-center gap-2 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
            >
              {{ showArchivedFilter ? 'Show all' : 'Show active' }}
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>
          
          <div class="flex-1 min-w-[200px] max-w-[300px]">
            <div class="relative">
              <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by name"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>
          
          <div class="flex-1"></div>
          
          <div class="flex items-center gap-2">
            <input
              v-model="quickAddName"
              @keyup.enter="quickAddTag"
              type="text"
              placeholder="Add new tag"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 w-[200px]"
            />
            <button
              @click="quickAddTag"
              :disabled="!quickAddName.trim()"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              ADD
            </button>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-4 py-3">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Tags</span>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <th class="px-4 py-3 text-left w-12">
                    <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600" />
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    Name
                  </th>
                  <th class="px-4 py-3 w-32"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr 
                  v-for="tag in filteredTags"
                  :key="tag.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group"
                  :class="{ 'opacity-60': tag.is_archived }"
                >
                  <td class="px-4 py-3">
                    <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600" />
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                      <span class="text-gray-900 dark:text-white">{{ tag.name }}</span>
                      <span v-if="tag.is_archived" class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                        Archived
                      </span>
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-1 justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                      <button
                        @click="openEditModal(tag)"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        title="Edit"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button
                        v-if="!tag.is_archived"
                        @click="archiveTag(tag)"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors"
                        title="Archive"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                      </button>
                      <button
                        v-else
                        @click="restoreTag(tag)"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors"
                        title="Restore"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                      </button>
                      <button
                        @click="deleteTag(tag)"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                        title="Delete"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="filteredTags.length === 0" class="flex flex-col items-center justify-center py-12">
            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
              <svg class="w-8 h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
              </svg>
            </div>
            <p class="text-gray-600 dark:text-gray-300 text-lg font-medium">No tags yet</p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Create tags to categorize your tasks</p>
          </div>
        </div>

        <Teleport to="body">
          <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click.self="showModal = false"
          >
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
            
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="absolute top-0 left-0 right-0 h-1 bg-blue-600" />
              
              <div class="p-5 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold font-sans text-gray-900 dark:text-white mb-4">
                  {{ editingTag ? 'Edit Tag' : 'New Tag' }}
                </h3>
                
                <form @submit.prevent="submit" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                      Tag name
                    </label>
                    <div class="relative">
                      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">#</span>
                      <input
                        v-model="form.name"
                        type="text"
                        placeholder="e.g., urgent, design, bug"
                        class="w-full pl-8 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.name }"
                      />
                    </div>
                    <p v-if="form.errors.name" class="text-red-400 text-sm mt-1">
                      {{ form.errors.name }}
                    </p>
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
                      {{ editingTag ? 'Save Changes' : 'Create Tag' }}
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
