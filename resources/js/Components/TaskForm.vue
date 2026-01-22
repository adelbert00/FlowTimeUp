<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import CustomDatePicker from '@/Components/CustomDatePicker.vue';

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
  projects?: Project[];
  tags?: Tag[];
}>();

const form = useForm({
  title: '',
  description: '',
  project_id: null as number | null,
  tag_ids: [] as number[],
  due_date: '',
  priority: 'medium' as 'low' | 'medium' | 'high',
});

const showProjectModal = ref(false);
const showTagModal = ref(false);

const projectForm = useForm({
  name: '',
  color: '#6366f1',
});

const tagForm = useForm({
  name: '',
});

const selectedProjectColor = computed(() => {
  const project = props.projects!.find(p => p.id === form.project_id);
  return project!.color;
});

function submitTask() {
  form.post(route('tasks.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    },
  });
}

function createProject() {
  projectForm.post(route('projects.store'), {
    preserveScroll: true,
    onSuccess: () => {
      projectForm.reset();
      showProjectModal.value = false;
      router.reload({ only: ['projects'] });
    },
  });
}

function createTag() {
  tagForm.post(route('tags.store'), {
    preserveScroll: true,
    onSuccess: () => {
      tagForm.reset();
      showTagModal.value = false;
      router.reload({ only: ['tags'] });
    },
  });
}

const colorPresets = [
  '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6',
  '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899',
];
</script>

<template>
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden shadow-sm">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200/50">
      <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Task
      </h2>
    </div>

    <form @submit.prevent="submitTask" class="p-4 sm:p-6 space-y-4 sm:space-y-5 bg-white rounded-xl border border-gray-200 shadow-sm">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Task name <span class="text-red-400">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          placeholder="What are you working on?"
          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm sm:text-base"
          :class="{ 'border-red-500': form.errors.title }"
        />
        <p v-if="form.errors.title" class="text-red-400 text-sm mt-1">
          {{ form.errors.title }}
        </p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Description
        </label>
        <textarea
          v-model="form.description"
          rows="3"
          placeholder="Add more details..."
          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors resize-none text-sm sm:text-base"
        />
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="text-sm font-medium text-gray-700">Project</label>
          <button
            type="button"
            @click="showProjectModal = true"
            class="text-xs text-blue-600 hover:text-blue-600 font-medium"
          >
            + New project
          </button>
        </div>
        <div class="relative">
          <select
            v-model="form.project_id"
            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors appearance-none cursor-pointer text-sm sm:text-base"
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
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Due date
        </label>
        <CustomDatePicker
          v-model="form.due_date"
          placeholder="Select due date"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Priority
        </label>
        <div class="flex gap-2">
          <button
            v-for="p in ['low', 'medium', 'high'] as const"
            :key="p"
            type="button"
            @click="form.priority = p"
            class="flex-1 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium capitalize transition-colors"
            :class="form.priority === p
              ? p === 'high' ? 'bg-red-500 text-white'
                : p === 'medium' ? 'bg-amber-500 text-white'
                : 'bg-emerald-500 text-white'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200'"
          >
            {{ p }}
          </button>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="text-sm font-medium text-gray-700">Tags</label>
          <button
            type="button"
            @click="showTagModal = true"
            class="text-xs text-blue-600 hover:text-blue-600 font-medium"
          >
            + New tag
          </button>
        </div>
        <div v-if="tags!.length > 0" class="flex flex-wrap gap-2">
          <label
            v-for="tag in tags"
            :key="tag.id"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg cursor-pointer transition-colors"
            :class="form.tag_ids.includes(tag.id)
              ? 'bg-blue-100 text-blue-700 border border-blue-300'
              : 'bg-gray-100 text-gray-700 border border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
          >
            <input
              type="checkbox"
              :value="tag.id"
              v-model="form.tag_ids"
              class="sr-only"
            />
            <span class="text-sm">#{{ tag.name }}</span>
          </label>
        </div>
        <p v-else class="text-sm text-gray-500">No tags yet</p>
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="w-full py-2.5 sm:py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm sm:text-base"
      >
        <span v-if="form.processing" class="flex items-center justify-center gap-2">
          <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
          Creating...
        </span>
        <span v-else>Create Task</span>
      </button>
    </form>

    <Teleport to="body">
      <div
        v-if="showProjectModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="showProjectModal = false"
      >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1" :style="{ backgroundColor: projectForm.color }" />
          
          <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">New Project</h3>
            
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                  Project name
                </label>
                <input
                  v-model="projectForm.name"
                  type="text"
                  placeholder="e.g., Work, Personal, Side Project"
                  class="w-full px-4 py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  :class="{ 'border-red-500': projectForm.errors.name }"
                />
                <p v-if="projectForm.errors.name" class="text-red-400 text-sm mt-1">
                  {{ projectForm.errors.name }}
                </p>
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
                    @click="projectForm.color = color"
                    class="w-8 h-8 rounded-lg transition-transform hover:scale-110"
                    :class="projectForm.color === color ? 'ring-2 ring-white ring-offset-2 ring-offset-white' : ''"
                    :style="{ backgroundColor: color }"
                  />
                </div>
              </div>
            </div>

            <div class="flex gap-3 mt-6">
              <button
                type="button"
                @click="showProjectModal = false"
                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
              >
                Cancel
              </button>
              <button
                type="button"
                @click="createProject"
                :disabled="projectForm.processing"
                class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 transition-colors"
              >
                Create Project
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <Teleport to="body">
      <div
        v-if="showTagModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="showTagModal = false"
      >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1 bg-blue-600" />
          
          <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">New Tag</h3>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Tag name
              </label>
              <input
                v-model="tagForm.name"
                type="text"
                placeholder="e.g., urgent, design, bug"
                class="w-full px-4 py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                :class="{ 'border-red-500': tagForm.errors.name }"
              />
              <p v-if="tagForm.errors.name" class="text-red-400 text-sm mt-1">
                {{ tagForm.errors.name }}
              </p>
            </div>

            <div class="flex gap-3 mt-6">
              <button
                type="button"
                @click="showTagModal = false"
                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
              >
                Cancel
              </button>
              <button
                type="button"
                @click="createTag"
                :disabled="tagForm.processing"
                class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 transition-colors"
              >
                Create Tag
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
