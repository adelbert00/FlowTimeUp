<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';

const props = defineProps<{
  defaultValue?: string | number | null;
  modelValue?: string | number | null;
  class?: HTMLAttributes['class'];
}>();

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number | null): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
});

defineOptions({ inheritAttrs: false });
</script>

<template>
  <input
    v-model="modelValue"
    v-bind="$attrs"
    :class="
      cn(
        'flex h-10 w-full rounded-xl border border-border bg-surface-raised px-3 py-2 text-sm text-primary ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-primary/60 placeholder:transition-colors focus-visible:outline-none focus-visible:border-accent focus-visible:ring-2 focus-visible:ring-accent/30 disabled:cursor-not-allowed disabled:opacity-50',
        props.class
      )
    "
  />
</template>
