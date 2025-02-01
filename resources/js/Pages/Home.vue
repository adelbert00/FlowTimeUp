<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <MainLayout>
    <div class="p-4">
      <h1 class="text-2xl font-bold mb-4">Welcome to MyToDoApp!</h1>
      <p class="text-gray-700">Manage your tasks and track time efficiently.</p>
      <div class="mt-4">
        <Link href="/tasks" class="bg-blue-500 text-white px-4 py-2 rounded">
          Go to Tasks
        </Link>
      </div>
    </div>
  </MainLayout>
</template>
