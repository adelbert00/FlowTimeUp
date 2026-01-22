import { defineStore } from 'pinia';
import axios from 'axios';

export interface Task {
  id: number;
  title: string;
  project_id: number | null;
  created_at?: string;
  updated_at?: string;

  tags?: any[];
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
    async createTask(title: string, projectId?: number | null) {
      const res = await axios.post('/tasks', {
        title,
        ...(projectId !== undefined && { project_id: projectId }),
      });
      return res.data;
    },
    async deleteTask(taskId: number) {
      await axios.delete(`/tasks/${taskId}`);
      await this.fetchTasks();
    },
    async bulkDeleteTasks(taskIds: number[]) {
      for (const id of taskIds) {
        await this.deleteTask(id);
      }
    },

    async updateTask(
      taskId: number,
      payload: { title?: string; project_id?: number | null }
    ) {
      await axios.patch(`/tasks/${taskId}`, payload);
      await this.fetchTasks();
    },

    async attachTags(taskId: number, tagIds: number[]) {
      await axios.post(`/tasks/${taskId}/tags`, { tags: tagIds });
      await this.fetchTasks();
    },

    async detachTag(taskId: number, tagId: number) {
      await axios.delete(`/tasks/${taskId}/tags/${tagId}`);
      await this.fetchTasks();
    },
  },
});
