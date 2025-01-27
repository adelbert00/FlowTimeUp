import { defineStore } from 'pinia'
import axios from 'axios'

export interface Project {
  id: number
  name: string
  created_at?: string
  updated_at?: string
}

interface ProjectsState {
  projects: Project[]
}

export const useProjectsStore = defineStore('projects', {
  state: (): ProjectsState => ({
    projects: [],
  }),

  actions: {
    setProjects(data: Project[]) {
      this.projects = data
    },

    async fetchProjects() {
      const response = await axios.get<Project[]>('/projects', {
        headers: { 'Accept': 'application/json' }
      })
      this.projects = response.data
    },

    async createProject(name: string) {
      await axios.post('/projects', { name })
      this.fetchProjects()
    },

    async deleteProject(id: number) {
      await axios.delete(`/projects/${id}`)
      this.fetchProjects()
    }
  },
})
