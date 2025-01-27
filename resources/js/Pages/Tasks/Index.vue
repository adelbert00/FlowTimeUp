<template>
    <div class="p-4">
      <h1 class="text-xl font-bold mb-4">Tasks</h1>
  
      <TaskForm />
  
      <TaskList />
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent, onMounted } from 'vue'
  import { useTasksStore } from '@/stores/tasks'
  import TaskForm from '@/Components/TaskForm.vue'
  import TaskList from '@/Components/TaskList.vue'
  
  export default defineComponent({
    components: {
      TaskForm,
      TaskList
    },
    setup() {
      const tasksStore = useTasksStore()
  
      onMounted(() => {
        tasksStore.fetchTasks()
      })
  
      return {}
    }
  })
  </script>
  