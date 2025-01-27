<template>
    <div class="flex items-center space-x-4">
      <div>
        <strong>{{ task.title }}</strong>
        <span v-if="task.project"> [{{ task.project.name }}]</span>
      </div>

      <div class="text-gray-600 text-center w-[10ch] font-mono">
        {{ formattedTime }}
      </div>
  
      <div class="pl-4">
        <button
          v-if="!isRunning"
          @click="startTimer"
          class="px-3 py-1 bg-green-500 text-white rounded"
        >
          Start
        </button>
        <button
          v-else
          @click="stopTimer"
          class="px-3 py-1 bg-red-500 text-white rounded"
        >
          Stop
        </button>
      </div>
    </div>
  </template>
    
  <script lang="ts">
  import { defineComponent, computed, ref, onBeforeUnmount, PropType } from 'vue'
  import { Task } from '@/stores/tasks'
  import { useTimeSessionsStore } from '@/stores/timeSessions'
  
  export default defineComponent({
    name: 'TimeTracker',
    props: {
      task: {
        type: Object as PropType<Task>,
        required: true,
      },
    },
    setup(props) {
      const timeSessionsStore = useTimeSessionsStore()
  
      const isRunning = ref(false)
      const startTime = ref<Date | null>(null)
      const currentTime = ref<Date>(new Date())
      let intervalId: number | null = null
  
      const startTimer = () => {
        isRunning.value = true
        startTime.value = new Date()
        intervalId = window.setInterval(() => {
          currentTime.value = new Date()
        }, 1) 
      }
  
      const stopTimer = async () => {
        if (!startTime.value) return
  
        isRunning.value = false
        const endTime = new Date()
        await timeSessionsStore.createTimeSession(
          props.task.id,
          startTime.value.toISOString(),
          endTime.toISOString()
        )
        if (intervalId) {
          clearInterval(intervalId)
          intervalId = null
        }
        startTime.value = null
      }
  
      const formattedTime = computed(() => {
        if (!isRunning.value || !startTime.value) {
          return '00:00:00:000'
        }
  
        const diffMilliseconds = currentTime.value.getTime() - startTime.value.getTime()
  
        if (diffMilliseconds <= 0) {
          return '00:00:00:000'
        }
  
        const diffSeconds = Math.floor(diffMilliseconds / 1000)
        const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0')
        const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0')
        const seconds = String(diffSeconds % 60).padStart(2, '0')
        const milliseconds = String(diffMilliseconds % 1000).padStart(3, '0')
  
        return `${hours}:${minutes}:${seconds}:${milliseconds}`
      })
  
      onBeforeUnmount(() => {
        if (intervalId) clearInterval(intervalId)
      })
  
      return {
        isRunning,
        formattedTime,
        startTimer,
        stopTimer,
      }
    },
  })
  </script>
  