<script setup lang="ts">
import { computed } from 'vue';
import { CheckboxIndicator, CheckboxRoot } from 'radix-vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
  checked: boolean;
  indeterminate?: boolean;
  disabled?: boolean;
}>();

const emit = defineEmits<{
  'update:checked': [value: boolean];
  change: [value: boolean];
}>();

const checkedState = computed<boolean | 'indeterminate'>({
  get() {
    if (props.indeterminate) return 'indeterminate';
    return props.checked;
  },
  set(val: boolean | 'indeterminate') {
    if (typeof val !== 'boolean') return;
    emit('update:checked', val);
    emit('change', val);
  },
});

const isOn = computed(
  () => checkedState.value === true || checkedState.value === 'indeterminate',
);
</script>

<template>
  <CheckboxRoot
    v-model:checked="checkedState"
    :disabled="disabled"
    :class="
      cn(
        'flex h-5 w-5 shrink-0 items-center justify-center rounded border-2 outline-none transition-all duration-200',
        'focus-visible:ring-2 focus-visible:ring-accent/50 focus-visible:ring-offset-2',
        isOn ? 'border-accent bg-accent' : 'border-border bg-surface-raised',
        disabled ? 'cursor-not-allowed opacity-50' : !isOn && 'hover:border-accent/60',
      )
    "
  >
    <CheckboxIndicator class="flex h-full w-full items-center justify-center">
      <svg
        v-if="checkedState === 'indeterminate'"
        class="h-3 w-3 text-white"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path
          fill-rule="evenodd"
          d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
          clip-rule="evenodd"
        />
      </svg>
      <svg
        v-else
        class="h-3 w-3 text-white"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path
          fill-rule="evenodd"
          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
          clip-rule="evenodd"
        />
      </svg>
    </CheckboxIndicator>
  </CheckboxRoot>
</template>
