<template>
    <nav class="flex md:hidden bg-gray-800 text-white py-2 px-4 justify-between items-center">
      <div>
        <Link href="/home" class="text-lg font-bold">MyToDoApp</Link>
      </div>
      <div>
        <button @click="toggleMenu" class="focus:outline-none">
          ☰
        </button>
        <div
          v-if="menuOpen"
          class="absolute top-12 right-4 bg-gray-700 text-white rounded shadow-lg w-48"
        >
          <ul>
            <li>
              <Link @click="closeMenu" href="/home" class="block py-2 px-4 hover:bg-gray-600">
                Home
              </Link>
            </li>
            <li>
              <Link @click="closeMenu" href="/tasks" class="block py-2 px-4 hover:bg-gray-600">
                Tasks
              </Link>
            </li>
            <li>
              <button
                @click="logout"
                class="block w-full text-left py-2 px-4 hover:bg-gray-600"
              >
                Logout
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </template>
  
  <script lang="ts">
  import { defineComponent, ref } from "vue";
  import { Link } from "@inertiajs/vue3";
  
  export default defineComponent({
    components: {
      Link,
    },
    setup() {
      const menuOpen = ref(false);
  
      const toggleMenu = () => {
        menuOpen.value = !menuOpen.value;
      };
  
      const closeMenu = () => {
        menuOpen.value = false;
      };
  
      const logout = async () => {
        closeMenu();
        await fetch("/logout", {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
          },
        });
        window.location.href = "/login";
      };
  
      return {
        menuOpen,
        toggleMenu,
        closeMenu,
        logout,
      };
    },
  });
  </script>
  