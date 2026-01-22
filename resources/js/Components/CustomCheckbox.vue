<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  checked: boolean;
  indeterminate?: boolean;
  disabled?: boolean;
}>();

const emit = defineEmits<{
  'update:checked': [value: boolean];
  change: [value: boolean];
}>();

const proxyChecked = computed({
  get() {
    return props.checked;
  },
  set(val: boolean) {
    emit('update:checked', val);
    emit('change', val);
  },
});

function handleClick(event: MouseEvent) {
  if (!props.disabled) {
    event.preventDefault();
    proxyChecked.value = !proxyChecked.value;
  }
}
</script>

<template>
  <label
    class="relative inline-flex items-center cursor-pointer"
    :class="{ 'cursor-not-allowed opacity-50': disabled }"
    @click="handleClick"
  >
    <input
      type="checkbox"
      :checked="checked"
      :indeterminate="indeterminate"
      :disabled="disabled"
      class="sr-only peer"
      @change="proxyChecked = ($event.target as HTMLInputElement).checked"
    />
    <div
      class="w-5 h-5 rounded border-2 transition-all duration-200 flex items-center justify-center"
      :class="[
        checked || indeterminate
          ? 'border-blue-600 bg-blue-600'
          : 'border-gray-300 bg-white',
        disabled ? 'opacity-50 cursor-not-allowed' : 'hover:border-blue-500'
      ]"
    >
      <!-- Checkmark -->
      <svg
        v-if="checked && !indeterminate"
        class="w-3 h-3 text-white"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path
          fill-rule="evenodd"
          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
          clip-rule="evenodd"
        />
      </svg>
      <!-- Indeterminate (dash) -->
      <svg
        v-else-if="indeterminate"
        class="w-3 h-3 text-white"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path
          fill-rule="evenodd"
          d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
          clip-rule="evenodd"
        />
      </svg>
    </div>
  </label>
</template>
