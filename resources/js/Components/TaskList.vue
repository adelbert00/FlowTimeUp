<template>
    <div>
      <h2 class="text-xl font-bold mb-4">Tasks</h2>
  
      <div v-for="task in tasksStore.tasks" :key="task.id" class="mb-2 border-b pb-2">

        <TimeTracker :task="task" />
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent, onMounted } from 'vue'
  import { useTasksStore } from '@/stores/tasks'
  import TimeTracker from '@/Components/TimeTracker.vue'
  
  export default defineComponent({
    name: 'TaskList',
    components: {
      TimeTracker
    },
    setup() {
      const tasksStore = useTasksStore()
  
      onMounted(() => {
        tasksStore.fetchTasks()
      })
  
      return {
        tasksStore
      }
    }
  })
  </script>
