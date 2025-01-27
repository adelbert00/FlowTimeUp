<template>
    <div class="flex">
        
      <Sidebar />
  
      <div class="flex-1 ml-64 md:ml-0">
        <Navbar />
        <slot />
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import Sidebar from "@/Components/Sidebar.vue";
  import Navbar from "@/Components/Navbar.vue";
  
  export default {
    components: {
      Sidebar,
      Navbar,
    },
  };
  </script>
  