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
  <textarea
    v-model="modelValue"
    v-bind="$attrs"
    :class="
      cn(
        'flex min-h-[80px] w-full rounded-xl border border-border bg-surface-raised px-3 py-2 sm:px-4 sm:py-2.5 text-sm text-primary ring-offset-white placeholder:text-black focus-visible:outline-none focus-visible:border-accent focus-visible:ring-2 focus-visible:ring-accent/30 disabled:cursor-not-allowed disabled:opacity-50 dark:placeholder:text-white/45',
        props.class
      )
    "
  />
</template>
