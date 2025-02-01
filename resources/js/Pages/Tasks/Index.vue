<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import TaskForm from '@/Components/TaskForm.vue';
import TaskList from '@/Components/TaskList.vue';
</script>

<template>
  <MainLayout>
    <div class="p-4">
      <TaskForm />
      <TaskList />
    </div>
  </MainLayout>
</template>
