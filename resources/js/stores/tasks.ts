import { defineStore } from 'pinia';
import axios from 'axios';
import type { Tag } from './tags';

export interface Task {
  id: number;
  title: string;
  project_id: number | null;
  created_at?: string;
  updated_at?: string;

  tags?: Tag[];
  time_sessions?: any[];
  project?: {
    id: number;
    name: string;
  };

  timer?: string;
  isRunning?: boolean;
  startTime?: any;
}

interface TasksState {
  tasks: Task[];
}

export const useTasksStore = defineStore('tasks', {
  state: (): TasksState => ({
    tasks: [],
  }),

  actions: {
    async fetchTasks() {
      const response = await axios.get('/tasks');
      this.tasks = response.data;
    },

    async createTask(title: string, projectId: number | null)  {
      const res = await axios.post('/tasks', { title, project_id: projectId });
      const newTask = res.data;
      return newTask;
    },    

    async attachTags(taskId: number, tagIds: number[]) {
      console.log('Calling attachTags with', taskId, tagIds);
      await axios.post(`/tasks/${taskId}/tags`, { tags: tagIds });
      await this.fetchTasks();
    },

    async detachTag(taskId: number, tagId: number) {
      await axios.delete(`/tasks/${taskId}/tags/${tagId}`);
      await this.fetchTasks();
    },
  },
});
