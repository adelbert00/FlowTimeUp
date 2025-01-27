<template>
    <div class="border p-4">
      <h2 class="font-semibold mb-2">Your tasks</h2>
      <ul>
        <li
          v-for="task in tasksStore.tasks"
          :key="task.id"
          class="border-b py-2"
        >
          <div class="flex justify-between items-center">
            <div>
              <strong>{{ task.title }}</strong>
              <span v-if="task.project"> [{{ task.project.name }}]</span>
            </div>
            <div>
              <button
                @click="deleteTask(task.id)"
                class="text-red-500 text-sm"
              >
                Delete
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent } from 'vue'
  import { useTasksStore } from '@/stores/tasks'
  
  export default defineComponent({
    setup() {
      const tasksStore = useTasksStore()
  
      const deleteTask = async (id: number) => {
        await tasksStore.deleteTask(id)
      }
  
      return {
        tasksStore,
        deleteTask
      }
    }
  })
  </script>
  