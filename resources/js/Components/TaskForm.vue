<template>
  <div class="border p-4 mb-4">
    <h2 class="font-semibold mb-2">Add new task</h2>
    <form @submit.prevent="submitTask">
      <Label for="title">Title:</Label>
      <Input id="title" v-model="title" placeholder="Enter task title" />

      <Label for="project">Project:</Label>
      <Select v-model="selectedProjectId">
        <SelectTrigger>
          <SelectValue placeholder="Select a project" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem
            v-for="proj in projectsStore.projects"
            :key="proj.id"
            :value="proj.id.toString()" 
          >
            {{ proj.name }}
          </SelectItem>
        </SelectContent>
      </Select>

      <Button
        type="button"
        class="text-sm mt-2"
        @click="showProjectModal = true"
      >
        + Create new project
      </Button>

      <div class="mt-2">
        <Button type="submit">Save</Button>
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
        />
        <div class="mt-3 flex justify-end space-x-2">
          <Button variant="outline" @click="cancelNewProject">Cancel</Button>
          <Button @click="submitNewProject">Create</Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useProjectsStore } from "@/stores/projects";
import { useTasksStore } from "@/stores/tasks";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from "./ui/select";
import { Label } from "./ui/label";

export default defineComponent({
  name: "TaskForm",
  components: {
    Button,
    Input,
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
    Label,
  },
  setup() {
    const projectsStore = useProjectsStore();
    const tasksStore = useTasksStore();

    const title = ref("");
    const selectedProjectId = ref<string | undefined>(undefined); 
    const showProjectModal = ref(false);
    const newProjectName = ref("");

    onMounted(() => {
      projectsStore.fetchProjects();
    });

    const submitTask = async () => {
      if (!title.value || !selectedProjectId.value) {
        return;
      }
      const projectId = parseInt(selectedProjectId.value, 10);
      await tasksStore.createTask(title.value, projectId);

      title.value = "";
      selectedProjectId.value = undefined;
    };

    const submitNewProject = async () => {
      if (!newProjectName.value) return;
      const created = await projectsStore.createProject(newProjectName.value);
      if (created) {
        selectedProjectId.value = created.id.toString();
      }
      showProjectModal.value = false;
      newProjectName.value = "";
    };

    const cancelNewProject = () => {
      showProjectModal.value = false;
      newProjectName.value = "";
    };

    return {
      projectsStore,
      tasksStore,
      title,
      selectedProjectId,
      submitTask,
      showProjectModal,
      newProjectName,
      submitNewProject,
      cancelNewProject,
    };
  },
});
</script>
