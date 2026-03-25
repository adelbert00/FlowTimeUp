<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import CustomDatePicker from '@/Components/CustomDatePicker.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';

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
  collapsible?: boolean;
}>();

const emit = defineEmits<{
  submit: [];
}>();

const isCollapsed = ref(false);

const form = useForm({
  title: '',
  description: '',
  project_id: null as number | null,
  tag_ids: [] as number[],
  due_date: '',
  priority: 'medium' as 'low' | 'medium' | 'high',
  hourly_rate: null as number | null,
  currency: 'USD',
  is_recurring: false,
  recurrence_type: null as 'daily' | 'weekly' | 'monthly' | null,
  recurrence_interval: 1,
  recurrence_ends_at: '',
});

const showProjectModal = ref(false);
const showTagModal = ref(false);
const showAdvanced = ref(false);

const projectForm = useForm({
  name: '',
  color: '#6366f1',
});

const tagForm = useForm({
  name: '',
});

watch(() => form.is_recurring, (val) => {
  if (!val) {
    form.recurrence_type = null;
    form.recurrence_interval = 1;
    form.recurrence_ends_at = '';
  }
});

function submitTask() {
  form.post(route('tasks.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      emit('submit');
    },
  });
}

const pendingProjectName = ref<string | null>(null);

function createProject() {
  pendingProjectName.value = projectForm.name;
  projectForm.post(route('projects.store'), {
    preserveScroll: true,
    onSuccess: () => {
      projectForm.reset();
      showProjectModal.value = false;
      router.reload({ 
        only: ['projects'],
        onSuccess: () => {
          if (pendingProjectName.value && props.projects) {
            const newProject = props.projects.find(p => p.name === pendingProjectName.value);
            if (newProject) {
              form.project_id = newProject.id;
            }
            pendingProjectName.value = null;
          }
        }
      });
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
  <div class="rounded-2xl border border-border bg-surface-raised shadow-sm overflow-hidden">
    <div
      v-if="collapsible !== false"
      class="px-4 sm:px-6 py-3 sm:py-4 border-b border-border flex items-center justify-between cursor-pointer hover:bg-accent/5 transition-colors"
      :class="{ 'border-b-0': isCollapsed }"
      @click="isCollapsed = !isCollapsed"
    >
      <h2 class="text-lg font-bold text-primary tracking-tight flex items-center gap-2">
        <svg class="w-5 h-5 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Task
      </h2>
      <Button
        type="button"
        variant="ghost"
        size="icon"
        class="text-secondary hover:text-primary hover:bg-accent/10"
        @click.stop="isCollapsed = !isCollapsed"
      >
        <svg
          class="w-5 h-5 transition-transform duration-200"
          :class="{ 'rotate-180': !isCollapsed }"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
        </svg>
      </Button>
    </div>

    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="max-h-0 opacity-0"
      enter-to-class="max-h-[2000px] opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="max-h-[2000px] opacity-100"
      leave-to-class="max-h-0 opacity-0"
    >
      <form v-show="!isCollapsed" @submit.prevent="submitTask" class="p-4 sm:p-6 space-y-4 sm:space-y-5 overflow-hidden">
      <div class="space-y-1.5">
        <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">
          Task name <span class="text-danger">*</span>
        </Label>
        <Input
          v-model="form.title"
          type="text"
          placeholder="What are you working on?"
          class="h-auto min-h-0 py-2.5 sm:py-3"
          :class="{ 'border-danger': form.errors.title }"
        />
        <p v-if="form.errors.title" class="text-danger text-xs font-medium">{{ form.errors.title }}</p>
      </div>

      <div class="space-y-1.5">
        <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Description</Label>
        <Textarea
          v-model="form.description"
          rows="2"
          placeholder="Add more details..."
          class="min-h-[72px] resize-none py-2.5 sm:py-3"
        />
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <Label class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Project</Label>
          <Button type="button" variant="link" class="h-auto p-0 text-xs font-bold text-accent" @click="showProjectModal = true">
            + New project
          </Button>
        </div>
        <div class="relative">
          <select
            v-model="form.project_id"
            class="w-full cursor-pointer appearance-none bg-surface-raised px-3 py-2 pr-10 text-sm text-primary [background-image:none] transition-colors focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 sm:px-4 sm:py-2.5 rounded-xl border border-border"
          >
            <option :value="null">No project</option>
            <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="space-y-1.5">
        <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Due date</Label>
        <CustomDatePicker v-model="form.due_date" placeholder="Select date" />
      </div>

      <div class="space-y-1.5">
        <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Priority</Label>
        <div class="flex gap-1">
          <button
            v-for="p in ['low', 'medium', 'high'] as const"
            :key="p"
            type="button"
            @click="form.priority = p"
            class="flex-1 px-2 py-2 rounded-xl text-xs font-bold capitalize transition-colors"
            :class="form.priority === p
              ? p === 'high' ? 'bg-red-500 text-white'
                : p === 'medium' ? 'bg-amber-500 text-white'
                : 'bg-emerald-500 text-white'
              : 'bg-surface-raised text-primary border border-border hover:bg-accent/5'"
          >
            {{ p[0].toUpperCase() }}
          </button>
        </div>
      </div>

      <Button
        type="button"
        variant="ghost"
        class="h-auto w-full justify-start gap-2 px-0 text-sm font-medium text-secondary hover:bg-transparent hover:text-primary"
        @click="showAdvanced = !showAdvanced"
      >
        <svg class="w-4 h-4 transition-transform shrink-0" :class="{ 'rotate-90': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        Advanced options
      </Button>

      <div v-if="showAdvanced" class="space-y-4 pt-2 border-t border-border">
        <div class="space-y-1.5">
          <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Hourly Rate</Label>
          <div class="flex gap-2">
            <div class="relative flex-1">
              <span class="pointer-events-none absolute left-3 top-1/2 z-[1] -translate-y-1/2 text-sm font-medium text-secondary">$</span>
              <Input
                :model-value="form.hourly_rate ?? ''"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="pl-7"
                @update:model-value="
                  (v) =>
                    (form.hourly_rate =
                      v === '' || v === null ? null : Number(v))
                "
              />
            </div>
            <div class="relative">
              <select
                v-model="form.currency"
                class="w-full cursor-pointer appearance-none bg-surface-raised px-3 py-2 pr-10 text-sm text-primary [background-image:none] transition-colors focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 rounded-xl border border-border"
              >
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="GBP">GBP</option>
                <option value="PLN">PLN</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div>
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" v-model="form.is_recurring" class="w-4 h-4 rounded border-border text-accent focus:ring-accent" />
            <span class="text-sm font-medium text-primary">Recurring task</span>
          </label>
        </div>

        <div v-if="form.is_recurring" class="space-y-4 rounded-xl border border-border bg-surface-raised p-4">
          <div class="space-y-2">
            <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Repeat every</Label>
            <div class="flex gap-2">
              <Input
                v-model.number="form.recurrence_interval"
                type="number"
                min="1"
                max="365"
                class="w-20"
              />
              <div class="flex-1 relative">
                <select
                  v-model="form.recurrence_type"
                  class="w-full cursor-pointer appearance-none bg-surface-raised px-3 py-2 pr-10 text-sm text-primary [background-image:none] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 rounded-xl border border-border"
                >
                  <option :value="null">Select...</option>
                  <option value="daily">Day(s)</option>
                  <option value="weekly">Week(s)</option>
                  <option value="monthly">Month(s)</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>
          <div class="space-y-1.5">
            <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Ends on</Label>
            <CustomDatePicker v-model="form.recurrence_ends_at" placeholder="Never" />
          </div>
        </div>
      </div>

      <div>
        <div class="mb-1.5 flex items-center justify-between">
          <Label class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Tags</Label>
          <Button type="button" variant="link" class="h-auto p-0 text-xs font-bold text-accent" @click="showTagModal = true">
            + New tag
          </Button>
        </div>
        <div v-if="tags && tags.length > 0" class="flex flex-wrap gap-2">
          <label
            v-for="tag in tags"
            :key="tag.id"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl cursor-pointer transition-colors text-sm font-medium"
            :class="form.tag_ids.includes(tag.id)
              ? 'bg-accent-subtle text-accent-text border border-accent/30'
              : 'bg-surface-raised text-primary border border-border hover:border-accent/40'"
          >
            <input type="checkbox" :value="tag.id" v-model="form.tag_ids" class="sr-only" />
            #{{ tag.name }}
          </label>
        </div>
        <p v-else class="text-sm text-secondary">No tags yet</p>
      </div>

      <Button
        type="submit"
        :disabled="form.processing"
        class="h-auto w-full rounded-xl py-3 text-xs font-bold uppercase tracking-widest shadow-lg shadow-accent/20"
      >
        <span v-if="form.processing" class="flex items-center justify-center gap-2">
          <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
          Creating...
        </span>
        <span v-else>Create Task</span>
      </Button>
    </form>
    </Transition>

    <Teleport to="body">
      <div v-if="showProjectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showProjectModal = false">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
        <div class="relative bg-surface-raised rounded-2xl shadow-2xl max-w-md w-full border border-border overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1" :style="{ backgroundColor: projectForm.color }" />
          <div class="p-6">
            <h3 class="text-xl font-bold text-primary mb-4">New Project</h3>
            <div class="space-y-4">
              <div class="space-y-1.5">
                <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Project name</Label>
                <Input
                  v-model="projectForm.name"
                  type="text"
                  placeholder="e.g., Work, Personal"
                  class="h-auto py-2.5"
                  :class="{ 'border-danger': projectForm.errors.name }"
                />
                <p v-if="projectForm.errors.name" class="text-danger text-xs">{{ projectForm.errors.name }}</p>
              </div>
              <div>
                <Label class="mb-1.5 block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Color</Label>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="color in colorPresets"
                    :key="color"
                    type="button"
                    @click="projectForm.color = color"
                    class="w-8 h-8 rounded-lg transition-transform hover:scale-110"
                    :class="projectForm.color === color ? 'ring-2 ring-white ring-offset-2 ring-offset-surface-raised' : ''"
                    :style="{ backgroundColor: color }"
                  />
                </div>
              </div>
            </div>
            <div class="mt-6 flex gap-3">
              <Button type="button" variant="outline" class="flex-1 rounded-xl py-2.5 text-xs font-bold uppercase tracking-widest" @click="showProjectModal = false">
                Cancel
              </Button>
              <Button type="button" :disabled="projectForm.processing" class="flex-1 rounded-xl py-2.5 text-xs font-bold uppercase tracking-widest shadow-lg shadow-accent/20" @click="createProject">
                Create
              </Button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <Teleport to="body">
      <div v-if="showTagModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showTagModal = false">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
        <div class="relative bg-surface-raised rounded-2xl shadow-2xl max-w-md w-full border border-border overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1 bg-accent" />
          <div class="p-6">
            <h3 class="text-xl font-bold text-primary mb-4">New Tag</h3>
            <div class="space-y-1.5">
              <Label class="block text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Tag name</Label>
              <Input
                v-model="tagForm.name"
                type="text"
                placeholder="e.g., urgent, design"
                class="h-auto py-2.5"
                :class="{ 'border-danger': tagForm.errors.name }"
              />
              <p v-if="tagForm.errors.name" class="text-danger text-xs">{{ tagForm.errors.name }}</p>
            </div>
            <div class="mt-6 flex gap-3">
              <Button type="button" variant="outline" class="flex-1 rounded-xl py-2.5 text-xs font-bold uppercase tracking-widest" @click="showTagModal = false">
                Cancel
              </Button>
              <Button type="button" :disabled="tagForm.processing" class="flex-1 rounded-xl py-2.5 text-xs font-bold uppercase tracking-widest shadow-lg shadow-accent/20" @click="createTag">
                Create
              </Button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
