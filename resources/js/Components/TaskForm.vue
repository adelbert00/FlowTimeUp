<template>
    <div class="border p-4 mb-4">
      <h2 class="font-semibold mb-2">Add new task</h2>
  
      <form @submit.prevent="submitTask">
        <label class="block mb-1">Title:</label>
        <input v-model="title" type="text" class="border rounded px-2 py-1 w-full" required />
  
        <label class="block mt-2 mb-1">Project:</label>
        <select v-model="projectId" class="border rounded px-2 py-1 w-full" required>
          <option disabled value="">-- select project --</option>
          <option
            v-for="proj in projectsStore.projects"
            :key="proj.id"
            :value="proj.id"
          >
            {{ proj.name }}
          </option>
        </select>
  
        <button
          type="button"
          class="text-blue-500 underline text-sm mt-1"
          @click="showProjectModal = true"
        >
          + Create new project
        </button>
  
        <div class="mt-2">
          <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">
            Save
          </button>
        </div>
      </form>
  
      <div
        v-if="showProjectModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
      >
        <div class="bg-white p-4 rounded shadow w-80">
          <h3 class="text-lg font-semibold mb-2">New Project</h3>
          <input
            v-model="newProjectName"
            type="text"
            placeholder="Project name"
            class="border rounded px-2 py-1 w-full"
          />
          <div class="mt-3 flex justify-end space-x-2">
            <button
              class="px-3 py-1 border border-gray-400 rounded"
              @click="cancelNewProject"
            >
              Cancel
            </button>
            <button
              class="bg-blue-500 text-white px-3 py-1 rounded"
              @click="submitNewProject"
            >
              Create
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent, ref, onMounted } from 'vue'
  import { useProjectsStore } from '@/stores/projects'
  import { useTasksStore } from '@/stores/tasks'
  
  export default defineComponent({
    setup() {
      const projectsStore = useProjectsStore()
      const tasksStore = useTasksStore()
  
      const title = ref('')
      const projectId = ref<number | null>(null)
  
      const showProjectModal = ref(false)
      const newProjectName = ref('')
  
      onMounted(() => {
        projectsStore.fetchProjects()
      })
  
      const submitTask = async () => {
        if (!title.value || !projectId.value) {
          return
        }
        await tasksStore.createTask(title.value, projectId.value)
  
        title.value = ''
        projectId.value = null
      }
  
      const submitNewProject = async () => {
        if (!newProjectName.value) {
          return
        }
        const created = await projectsStore.createProject(newProjectName.value)
        if (created) {
          projectId.value = created.id
        }
        showProjectModal.value = false
        newProjectName.value = ''
      }
  
      const cancelNewProject = () => {
        showProjectModal.value = false
        newProjectName.value = ''
      }
  
      return {
        projectsStore,
        tasksStore,
        title,
        projectId,
        submitTask,
        showProjectModal,
        newProjectName,
        submitNewProject,
        cancelNewProject,
      }
    }
  })
  </script>
  