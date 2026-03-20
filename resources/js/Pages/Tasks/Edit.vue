<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CustomDatePicker from '@/Components/CustomDatePicker.vue';

interface Task {
  id: number;
  title: string;
  description?: string;
  project_id?: number | null;
  due_date?: string;
  priority?: 'low' | 'medium' | 'high';
  completed: boolean;
  tags?: Array<{ id: number; name: string }>;
}

interface Project {
  id: number;
  name: string;
  color?: string;
}

interface Tag {
  id: number;
  name: string;
}

const props = defineProps<{
  task: Task;
  projects: Project[];
  tags: Tag[];
}>();

const form = useForm({
  title: props.task.title || '',
  description: props.task.description || '',
  project_id: props.task.project_id || null,
  due_date: props.task.due_date || '',
  priority: props.task.priority || 'medium',
  completed: props.task.completed || false,
  tag_ids: props.task.tags?.map(t => t.id) || [],
});

function submit() {
  form.put(route('tasks.update', props.task.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('tasks.index'));
    },
  });
}
</script>

<template>
  <Head :title="`Edit: ${props.task.title}`" />

  <MainLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
      <div class="container mx-auto px-4 sm:px-6 md:px-10 lg:px-8 py-6 sm:py-8 max-w-2xl pt-2 xl:pt-4">
        <div class="mb-6 sm:mb-8 flex items-center gap-3 sm:gap-4">
          <button
            @click="router.visit(route('tasks.index'))"
            class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white dark:hover:bg-gray-700 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
          </button>
          <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Edit Task</h1>
            <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">Update task details</p>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
          <form @submit.prevent="submit" class="p-4 sm:p-6 space-y-4 sm:space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Task name <span class="text-red-400">*</span>
              </label>
              <input
                v-model="form.title"
                type="text"
                placeholder="Task name"
                class="w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm sm:text-base"
                :class="{ 'border-red-500': form.errors.title }"
              />
              <p v-if="form.errors.title" class="text-red-400 text-xs sm:text-sm mt-1">
                {{ form.errors.title }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Description
              </label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors resize-none text-sm sm:text-base"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Project
              </label>
              <div class="relative">
                <select
                  v-model="form.project_id"
                  class="w-full px-3 sm:px-4 py-2 sm:py-2.5 pr-10 bg-gray-50/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors appearance-none cursor-pointer text-sm sm:text-base"
                >
                  <option :value="null">No project</option>
                  <option
                    v-for="project in projects"
                    :key="project.id"
                    :value="project.id"
                  >
                    {{ project.name }}
                  </option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                  Due date
                </label>
                <CustomDatePicker
                  v-model="form.due_date"
                  placeholder="Select due date"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                  Priority
                </label>
                <div class="flex gap-2">
                  <button
                    v-for="p in ['low', 'medium', 'high'] as const"
                    :key="p"
                    type="button"
                    @click="form.priority = p"
                    class="flex-1 px-2 sm:px-3 py-2 rounded-lg text-xs sm:text-sm font-medium capitalize transition-colors"
                    :class="form.priority === p
                      ? p === 'high' ? 'bg-red-500 text-white'
                        : p === 'medium' ? 'bg-amber-500 text-white'
                        : 'bg-emerald-500 text-white'
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600'"
                  >
                    {{ p }}
                  </button>
                </div>
              </div>
            </div>

            <div>
              <label class="flex items-center gap-3 cursor-pointer">
                <input
                  v-model="form.completed"
                  type="checkbox"
                  class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-blue-600 focus:ring-blue-500 focus:ring-offset-white dark:focus:ring-offset-gray-800"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mark as completed</span>
              </label>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Tags
              </label>
              <div v-if="tags.length > 0" class="flex flex-wrap gap-2">
                <label
                  v-for="tag in tags"
                  :key="tag.id"
                  class="inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg cursor-pointer transition-colors"
                  :class="form.tag_ids.includes(tag.id)
                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-700'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-600'"
                >
                  <input
                    type="checkbox"
                    :value="tag.id"
                    v-model="form.tag_ids"
                    class="sr-only"
                  />
                  <span class="text-xs sm:text-sm">#{{ tag.name }}</span>
                </label>
              </div>
              <p v-else class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">No tags available</p>
            </div>

            <div class="flex gap-3 pt-2 sm:pt-4">
              <button
                type="button"
                @click="router.visit(route('tasks.index'))"
                class="flex-1 px-4 py-2 sm:py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm sm:text-base"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 px-4 py-2 sm:py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 transition-colors text-sm sm:text-base"
              >
                <span v-if="form.processing">Saving...</span>
                <span v-else>Save Changes</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
