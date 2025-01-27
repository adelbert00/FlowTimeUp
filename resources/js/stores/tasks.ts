import { defineStore } from 'pinia'
import axios from 'axios'

export interface Task {
  id: number
  title: string
  project_id: number
  created_at?: string
  updated_at?: string
  project?: {
    id: number
    name: string
  }
  time_sessions?: any[] 
}

interface TasksState {
  tasks: Task[]
}

export const useTasksStore = defineStore('tasks', {
  state: (): TasksState => ({
    tasks: [],
  }),

  actions: {
    setTasks(data: Task[]) {
      this.tasks = data
    },

    async fetchTasks() {
      const response = await axios.get<Task[]>('/tasks', {
        headers: { 'Accept': 'application/json' }
      })
      this.tasks = response.data
    },

    async createTask(title: string, project_id: number) {
      await axios.post('/tasks', { title, project_id })
      this.fetchTasks()
    },

    async deleteTask(id: number) {
      await axios.delete(`/tasks/${id}`)
      this.fetchTasks()
    }
  },
})
