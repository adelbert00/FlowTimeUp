<script setup lang="ts">
import Main_Sidebar from "@/Components/Main_Sidebar.vue";
</script>

<template>
  <div class="flex">
    <Main_Sidebar />
    <div class="flex-1">
      <slot />
    </div>
  </div>
</template>
