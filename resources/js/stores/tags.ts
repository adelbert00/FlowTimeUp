import { defineStore } from 'pinia';
import axios from 'axios';

export interface Tag {
  id: number;
  name: string;
  created_at?: string;
  updated_at?: string;
}

interface TagsState {
  tags: Tag[];
}

export const useTagsStore = defineStore('tags', {
  state: (): TagsState => ({
    tags: [],
  }),

  actions: {
    async fetchTags() {
      const response = await axios.get('/tags');
      this.tags = response.data; 
    },

    async createTag(name: string) {
      const response = await axios.post('/tags', { name });
      const newTag = response.data as Tag;

      this.tags.push(newTag);

      return newTag;
    },
  },
});
