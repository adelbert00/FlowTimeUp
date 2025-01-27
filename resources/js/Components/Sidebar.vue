<template>
    <aside class="hidden md:flex flex-col bg-gray-800 text-white w-64 h-screen fixed">
      <div class="p-4">
        <h2 class="text-lg font-bold">MyToDoApp</h2>
      </div>
      <ul class="space-y-2 mt-4">
        <li>
          <Link href="/home" class="block py-2 px-4 hover:bg-gray-700">Home</Link>
        </li>
        <li>
          <Link href="/tasks" class="block py-2 px-4 hover:bg-gray-700">Tasks</Link>
        </li>
        <li>
          <button @click="logout" class="block w-full text-left py-2 px-4 hover:bg-gray-700">
            Logout
          </button>
        </li>
      </ul>
    </aside>
  </template>
  
<script lang="ts">
import { defineComponent } from "vue";
import { Link, router } from "@inertiajs/vue3";

export default defineComponent({
  components: {
    Link,
  },
  setup() {
    const logout = async () => {
      await router.post("/logout", {}, {
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
        },
      });
    };

    return {
      logout,
    };
  },
});
</script>
