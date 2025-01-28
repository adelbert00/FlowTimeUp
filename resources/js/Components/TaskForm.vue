<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useProjectsStore } from '@/stores/projects';
import { useTasksStore } from '@/stores/tasks';
import { useTagsStore } from '@/stores/tags';

import { Button } from './ui/button';
import { Input } from './ui/input';
import { Label } from './ui/label';

const projectsStore = useProjectsStore();
const tasksStore = useTasksStore();
const tagsStore = useTagsStore();

const title = ref('');
const selectedProjectId = ref<number | null>(null);

const selectedTags = ref<number[]>([]);

const showProjectModal = ref(false);
const newProjectName = ref('');

const showTagModal = ref(false);
const newTagName = ref('');

onMounted(async () => {

  await projectsStore.fetchProjects();
  await tagsStore.fetchTags();
});


const submitTask = async () => {
  if (!title.value) return;

  const createdTask = await tasksStore.createTask(
  title.value,
  selectedProjectId.value
);

console.log('Created task = ', createdTask);

if (createdTask && createdTask.id && selectedTags.value.length > 0) {
  console.log('Will attach tags', selectedTags.value, 'to task', createdTask.id);
  await tasksStore.attachTags(createdTask.id, selectedTags.value);
} else {
  console.log('Skipped attach tags, because no tags or no createdTask');
}

  if (createdTask && createdTask.id && selectedTags.value.length > 0) {
    await tasksStore.attachTags(createdTask.id, selectedTags.value);
  }

  title.value = '';
  selectedProjectId.value = null;
  selectedTags.value = [];
};

const submitNewProject = async () => {
  if (!newProjectName.value) return;

  const createdProject = await projectsStore.createProject(newProjectName.value);
  if (createdProject && createdProject.id) {
    selectedProjectId.value = createdProject.id;
  }

  showProjectModal.value = false;
  newProjectName.value = '';
};


const submitNewTag = async () => {
  if (!newTagName.value) return;
  const createdTag = await tagsStore.createTag(newTagName.value);
  if (createdTag && createdTag.id) {

    selectedTags.value.push(createdTag.id);
  }

  showTagModal.value = false;
  newTagName.value = '';
};

const cancelNewProject = () => {
  showProjectModal.value = false;
  newProjectName.value = '';
};

const cancelNewTag = () => {
  showTagModal.value = false;
  newTagName.value = '';
};
</script>

<template>
  <div class="border p-4 mb-4">
    <h2 class="text-xl font-semibold mb-2">Add new task</h2>

    <form @submit.prevent="submitTask">

      <Label class="font-medium">Title:</Label>
      <Input
        v-model="title"
        placeholder="Enter task title"
        class="mt-1 mb-3 w-full"
      />


      <Label class="font-medium">Project:</Label>
      <select
        v-model.number="selectedProjectId"
        class="border px-2 py-1 rounded w-full mt-1 mb-3"
      >
        <option :value="null">-- Select project --</option>
        <option
          v-for="proj in projectsStore.projects"
          :key="proj.id"
          :value="proj.id"
        >
          {{ proj.name }}
        </option>
      </select>

      <Button
        type="button"
        variant="outline"
        class="text-sm mb-3"
        @click="showProjectModal = true"
      >
        + Create new project
      </Button>

      <Label class="font-medium block mt-3 mb-1">Tags:</Label>
      <div class="flex flex-wrap gap-2 mb-3">
        <div
          v-for="tag in tagsStore.tags"
          :key="tag.id"
          class="flex items-center"
        >
          <input
            type="checkbox"
            :id="'tag-' + tag.id"
            :value="tag.id"
            v-model="selectedTags"
            class="mr-1"
          />
          <label :for="'tag-' + tag.id">{{ tag.name }}</label>
        </div>
      </div>

      <Button
        type="button"
        variant="outline"
        class="text-sm mb-4"
        @click="showTagModal = true"
      >
        + Create new tag
      </Button>


      <div>
        <Button type="submit" class="bg-blue-500 text-white px-3 py-2 rounded">
          Save
        </Button>
      </div>
    </form>

    <div
      v-if="showProjectModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white p-4 rounded shadow w-80">
        <h3 class="text-lg font-semibold mb-2">New Project</h3>
        <Input
          v-model="newProjectName"
          placeholder="Enter project name"
          class="w-full mb-3"
        />
        <div class="mt-3 flex justify-end space-x-2">
          <Button variant="outline" @click="cancelNewProject">Cancel</Button>
          <Button @click="submitNewProject">Create</Button>
        </div>
      </div>
    </div>

    <div
      v-if="showTagModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white p-4 rounded shadow w-80">
        <h3 class="text-lg font-semibold mb-2">New Tag</h3>
        <Input
          v-model="newTagName"
          placeholder="Enter tag name"
          class="w-full mb-3"
        />
        <div class="mt-3 flex justify-end space-x-2">
          <Button variant="outline" @click="cancelNewTag">Cancel</Button>
          <Button @click="submitNewTag">Create</Button>
        </div>
      </div>
    </div>
  </div>
</template>
