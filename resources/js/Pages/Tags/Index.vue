<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref } from 'vue';

interface Tag {
  id: number;
  name: string;
  tasks_count?: number;
}

const props = defineProps<{
  tags: Tag[];
}>();

const showModal = ref(false);
const editingTag = ref<Tag | null>(null);

const form = useForm({
  name: '',
});

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

function deleteTag(tag: Tag) {
  if (confirm(`Are you sure you want to delete "#${tag.name}"?`)) {
    router.delete(route('tags.destroy', tag.id));
  }
}
</script>

<template>
  <Head title="Tags" />

  <MainLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100">
      <div class="container mx-auto px-4 sm:px-6 md:px-10 lg:px-8 py-6 sm:py-8 max-w-4xl pt-20 sm:pt-24 md:pt-28 xl:pt-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Tags</h1>
            <p class="text-gray-600 text-sm sm:text-base">Organize and categorize your tasks</p>
          </div>
          <button
            @click="openCreateModal"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Tag
          </button>
        </div>

        <!-- Tags List -->
        <div v-if="tags.length > 0" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div
            v-for="(tag, index) in tags"
            :key="tag.id"
            class="flex items-center justify-between p-3 sm:p-4 hover:bg-gray-100/30 transition-colors group"
            :class="{ 'border-t border-gray-200': index > 0 }"
          >
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
              <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-600/10 flex items-center justify-center flex-shrink-0">
                <span class="text-blue-600 text-xs sm:text-sm font-medium">#</span>
              </div>
              <div class="min-w-0">
                <p class="text-gray-900 font-medium text-sm sm:text-base truncate">{{ tag.name }}</p>
                <p class="text-gray-500 text-xs sm:text-sm">{{ tag.tasks_count || 0 }} tasks</p>
              </div>
            </div>
            <div class="flex items-center gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
              <button
                @click="openEditModal(tag)"
                class="p-1.5 sm:p-2 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
              <button
                @click="deleteTag(tag)"
                class="p-1.5 sm:p-2 rounded-lg text-gray-600 hover:text-red-500 hover:bg-red-50 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center py-12 sm:py-16 bg-white rounded-xl border border-gray-200 shadow-sm">
          <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
          </div>
          <p class="text-gray-600 text-base sm:text-lg font-medium">No tags yet</p>
          <p class="text-gray-500 text-xs sm:text-sm mt-1 mb-4 text-center px-4">Create tags to categorize your tasks</p>
          <button
            @click="openCreateModal"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Tag
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
              <div class="absolute top-0 left-0 right-0 h-1 bg-blue-600" />
              
              <div class="p-5 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">
                  {{ editingTag ? 'Edit Tag' : 'New Tag' }}
                </h3>
                
                <form @submit.prevent="submit" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                      Tag name
                    </label>
                    <div class="relative">
                      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">#</span>
                      <input
                        v-model="form.name"
                        type="text"
                        placeholder="e.g., urgent, design, bug"
                        class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
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
                      class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 border border-gray-200 rounded-lg font-medium hover:bg-gray-200 transition-colors"
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
