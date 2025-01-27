<template>
    <div class="flex">

      <Main_Sidebar />
  
      <div class="flex-1 ml-64 md:ml-0">
        <Navbar />
        <slot />
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import Main_Sidebar from "@/Components/Main_Sidebar.vue";
  import Navbar from "@/Components/Navbar.vue";
  
  export default {
    components: {
      Main_Sidebar,
      Navbar,
    },
  };
  </script>
  