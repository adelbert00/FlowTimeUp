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
  
        <div class="mt-2">
          <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Save</button>
        </div>
      </form>
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
  
      onMounted(() => {
        projectsStore.fetchProjects()
      })
  
      const submitTask = async () => {
        if (!title.value || !projectId.value) return
        await tasksStore.createTask(title.value, projectId.value)
        title.value = ''
        projectId.value = null
      }
  
      return {
        projectsStore,
        tasksStore,
        title,
        projectId,
        submitTask
      }
    }
  })
  </script>
  