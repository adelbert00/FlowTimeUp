<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

interface SkeletonProps {
  class?: HTMLAttributes['class'];
}

const props = defineProps<SkeletonProps>();
</script>

<template>
  <div
    :class="cn('shimmer rounded-md h-4', props.class)"
  />
</template>
