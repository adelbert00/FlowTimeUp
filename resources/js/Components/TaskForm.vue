<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useProjectsStore } from "@/stores/projects";
import { useTasksStore } from "@/stores/tasks";
import { useTagsStore } from "@/stores/tags";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { Label } from "./ui/label";

const projectsStore = useProjectsStore();
const tasksStore = useTasksStore();
const tagsStore = useTagsStore();

const title = ref("");
const selectedProjectId = ref<number | null>(null);
const selectedTags = ref<number[]>([]);

const showProjectModal = ref(false);
const newProjectName = ref("");

const showTagModal = ref(false);
const newTagName = ref("");

const showEditTagModal = ref(false);
const editTagId = ref<number | null>(null);
const editTagName = ref("");

onMounted(async () => {
  await projectsStore.fetchProjects();
  await tagsStore.fetchTags();
});

const submitTask = async () => {
  if (!title.value) return;

  const createdTask = await tasksStore.createTask(title.value, selectedProjectId.value);
  if (createdTask && createdTask.id && selectedTags.value.length > 0) {
    await tasksStore.attachTags(createdTask.id, selectedTags.value);
  }

  title.value = "";
  selectedProjectId.value = null;
  selectedTags.value = [];
};

const submitNewProject = async () => {
  if (!newProjectName.value) return;
  const createdProject = await projectsStore.createProject(newProjectName.value);
  if (createdProject && createdProject.id) {
    selectedProjectId.value = createdProject.id;
  }
  newProjectName.value = "";
  showProjectModal.value = false;
};

const submitNewTag = async () => {
  if (!newTagName.value) return;
  const createdTag = await tagsStore.createTag(newTagName.value);
  if (createdTag && createdTag.id) {
    selectedTags.value.push(createdTag.id);
  }
  newTagName.value = "";
  showTagModal.value = false;
};

function openEditTagModal(tagId: number, currentName: string) {
  editTagId.value = tagId;
  editTagName.value = currentName;
  showEditTagModal.value = true;
}

async function saveTagEdit() {
  if (editTagId.value === null) return;
  if (!editTagName.value) return;
  await tagsStore.updateTag(editTagId.value, editTagName.value);
  showEditTagModal.value = false;
}

async function deleteTag(tagId: number) {
  await tagsStore.deleteTag(tagId);
  selectedTags.value = selectedTags.value.filter(id => id !== tagId);
}

const cancelNewProject = () => {
  newProjectName.value = "";
  showProjectModal.value = false;
};

const cancelNewTag = () => {
  newTagName.value = "";
  showTagModal.value = false;
};

function cancelTagEdit() {
  showEditTagModal.value = false;
}
</script>

<template>
  <div class="border p-4 mb-4">
    <h2 class="text-xl font-semibold mb-4">Add New Task</h2>
    <form @submit.prevent="submitTask">
      <Label for="title" class="block font-medium mb-1">Title:</Label>
      <Input
        id="title"
        v-model="title"
        placeholder="Task title"
        class="mb-3 w-full"
      />

      <Label for="project" class="block font-medium mb-1">Project:</Label>
      <select
        id="project"
        v-model.number="selectedProjectId"
        class="border px-2 py-1 rounded w-full mb-3"
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
        class="text-sm mb-4"
        @click="showProjectModal = true"
      >
        + Create new project
      </Button>

      <Label class="block font-medium mb-1">Tags:</Label>
      <div class="flex flex-col gap-2 mb-3">
        <div
          v-for="tag in tagsStore.tags"
          :key="tag.id"
          class="flex items-center justify-between border-b py-2"
        >
          <div class="flex items-center">
            <input
              type="checkbox"
              :id="'tag-' + tag.id"
              :value="tag.id"
              v-model="selectedTags"
              class="mr-1"
            />
            <label :for="'tag-' + tag.id">{{ tag.name }}</label>
          </div>

          <div class="flex gap-2">
            <button
              class="border px-2 py-1 rounded text-sm"
              @click="openEditTagModal(tag.id, tag.name)"
            >
              Edit
            </button>
            <button
              class="border px-2 py-1 rounded text-sm text-red-500"
              @click="deleteTag(tag.id)"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
      <Button
        type="button"
        class="text-sm mb-4"
        @click="showTagModal = true"
      >
        + Create new tag
      </Button>

      <div class="mt-4">
        <Button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
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

    <div
      v-if="showEditTagModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white p-4 rounded shadow w-80">
        <h3 class="text-lg font-semibold mb-2">Edit Tag</h3>
        <Input
          v-model="editTagName"
          placeholder="New tag name"
          class="w-full mb-3"
        />
        <div class="mt-3 flex justify-end space-x-2">
          <Button variant="outline" @click="cancelTagEdit">Cancel</Button>
          <Button @click="saveTagEdit">Save</Button>
        </div>
      </div>
    </div>
  </div>
</template>
