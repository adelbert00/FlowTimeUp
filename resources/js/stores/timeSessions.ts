import { defineStore } from 'pinia';
import axios from 'axios';

export interface TimeSession {
  id: number;
  task_id: number;
  start_time: string;
  end_time?: string | null;
  created_at?: string;
  updated_at?: string;
  task?: {
    id: number;
    title: string;
    project_id: number;
  };
}

interface TimeSessionsState {
  sessions: TimeSession[];
}

export const useTimeSessionsStore = defineStore('timeSessions', {
  state: (): TimeSessionsState => ({
    sessions: [],
  }),

  actions: {
    async fetchSessions(): Promise<TimeSession[]> {
      const response = await axios.get<TimeSession[]>('/time-sessions', {
        headers: { Accept: 'application/json' },
      });
      this.sessions = response.data;
      return this.sessions;
    },

    async createTimeSession(
      task_id: number,
      start_time: string,
      end_time?: string,
      is_billable?: boolean,
      billable_rate?: number | null,
      description?: string
    ) {
      await axios.post('/time-sessions', { 
        task_id, 
        start_time, 
        end_time,
        is_billable,
        billable_rate,
        description
      });
      this.fetchSessions();
    },

    async deleteTimeSession(id: number) {
      await axios.delete(`/time-sessions/${id}`);
      this.fetchSessions();
    },
  },
});
