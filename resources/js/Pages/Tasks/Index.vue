<template>
  <MainLayout>
    <div class="pl-80 p-4">
      <h1 class="text-xl font-bold mb-4">Tasks</h1>

      <TaskForm />

      <TaskList />
    </div>
  </MainLayout>
</template>

<script lang="ts">
import MainLayout from "@/Layouts/MainLayout.vue";
import TaskForm from "@/Components/TaskForm.vue";
import TaskList from "@/Components/TaskList.vue";

export default {
  components: {
    MainLayout,
    TaskForm,
    TaskList,
  },
};
</script>
