import { defineStore } from 'pinia';
import axios from 'axios';

export interface Project {
  id: number;
  name: string;
  created_at?: string;
  updated_at?: string;
}

interface ProjectsState {
  projects: Project[];
}

export const useProjectsStore = defineStore('projects', {
  state: (): ProjectsState => ({
    projects: [],
  }),

  actions: {
    setProjects(data: Project[]) {
      this.projects = data;
    },

    async fetchProjects() {
      const response = await axios.get<Project[]>('/projects', {
        headers: { Accept: 'application/json' },
      });
      this.projects = response.data;
    },

    async createProject(name: string): Promise<Project | undefined> {
      try {
        const response = await axios.post<Project>(
          '/projects',
          { name },
          { headers: { Accept: 'application/json' } }
        );
        const createdProject = response.data;
        this.projects.push(createdProject);
        return createdProject;
      } catch (error) {
        console.error('Error creating project:', error);
        return undefined;
      }
    },

    async updateProject(projectId: number, newName: string) {
      const response = await axios.patch<Project>(
        `/projects/${projectId}`,
        { name: newName },
        { headers: { Accept: 'application/json' } }
      );
      const updatedProject = response.data;

      const index = this.projects.findIndex((p) => p.id === projectId);
      if (index !== -1) {
        this.projects[index] = updatedProject;
      }
      return updatedProject;
    },

    async deleteProject(id: number) {
      await axios.delete(`/projects/${id}`, {
        headers: { Accept: 'application/json' },
      });
      this.projects = this.projects.filter((p) => p.id !== id);
    },
  },
});
