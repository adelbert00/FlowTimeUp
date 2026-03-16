<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Method } from '@inertiajs/core';

defineProps<{
  href: string;
  method?: Method;
  as?: string;
}>();
</script>

<template>
  <Link
    :href="href"
    :method="method"
    :as="as"
    class="block w-full px-3.5 py-2 text-start text-sm text-secondary hover:text-primary transition-colors duration-100 hover:bg-surface-overlay focus:bg-surface-overlay focus:outline-none"
  >
    <slot />
  </Link>
</template>
