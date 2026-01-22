<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps<{
  modelValue?: string;
  placeholder?: string;
  disabled?: boolean;
}>();

const emit = defineEmits<{
  'update:modelValue': [value: string];
}>();

const isOpen = ref(false);
const selectedDate = ref<Date | null>(null);
const currentMonth = ref(new Date());
const dateInputRef = ref<HTMLElement | null>(null);
const calendarPosition = ref({ top: '0px', left: '0px' });

function isValidDate(dateString: string | undefined): boolean {
  if (!dateString || dateString.trim() === '') return false;
  const date = new Date(dateString);
  return !isNaN(date.getTime());
}

function parseDate(dateString: string | undefined): Date | null {
  if (!isValidDate(dateString)) return null;
  return new Date(dateString!);
}

function updateCalendarPosition() {
  if (!dateInputRef.value) return;
  
  const rect = dateInputRef.value.getBoundingClientRect();
  const windowHeight = window.innerHeight;
  const calendarHeight = 380;
  
  const spaceBelow = windowHeight - rect.bottom;
  const showAbove = spaceBelow < calendarHeight && rect.top > calendarHeight;
  
  calendarPosition.value = {
    top: showAbove ? `${rect.top - calendarHeight - 8}px` : `${rect.bottom + 8}px`,
    left: `${Math.max(8, Math.min(rect.left, window.innerWidth - 308))}px`
  };
}

const daysInMonth = computed(() => {
  const year = currentMonth.value.getFullYear();
  const month = currentMonth.value.getMonth();
  return new Date(year, month + 1, 0).getDate();
});

const firstDayOfMonth = computed(() => {
  const year = currentMonth.value.getFullYear();
  const month = currentMonth.value.getMonth();
  return new Date(year, month, 1).getDay();
});

const monthName = computed(() => {
  return currentMonth.value.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const formattedValue = computed(() => {
  const date = parseDate(props.modelValue);
  if (!date) return props.placeholder || 'Select date';
  return date.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
});

const calendarDays = computed(() => {
  const days: (Date | null)[] = [];
  
  for (let i = 0; i < firstDayOfMonth.value; i++) {
    days.push(null);
  }
  
  const year = currentMonth.value.getFullYear();
  const month = currentMonth.value.getMonth();
  for (let day = 1; day <= daysInMonth.value; day++) {
    days.push(new Date(year, month, day));
  }
  
  return days;
});

function selectDate(date: Date) {
  selectedDate.value = date;
  const formatted = date.toISOString().split('T')[0];
  emit('update:modelValue', formatted);
  isOpen.value = false;
}

function previousMonth() {
  currentMonth.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() - 1,
    1
  );
}

function nextMonth() {
  currentMonth.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() + 1,
    1
  );
}

function isToday(date: Date): boolean {
  const today = new Date();
  return (
    date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
  );
}

function isSelected(date: Date): boolean {
  const selected = parseDate(props.modelValue);
  if (!selected) return false;
  return (
    date.getDate() === selected.getDate() &&
    date.getMonth() === selected.getMonth() &&
    date.getFullYear() === selected.getFullYear()
  );
}

function toggleCalendar() {
  if (!isOpen.value) {
    updateCalendarPosition();
  }
  isOpen.value = !isOpen.value;
}

function handleClickOutside(event: MouseEvent) {
  if (!dateInputRef.value) return;
  
  const target = event.target as Node;
  const calendar = document.querySelector('[data-calendar-dropdown]');
  if (!dateInputRef.value.contains(target) && calendar && !calendar.contains(target)) {
    isOpen.value = false;
  }
}

watch(() => props.modelValue, (newValue) => {
  const parsedDate = parseDate(newValue);
  if (parsedDate) {
    selectedDate.value = parsedDate;
    currentMonth.value = new Date(parsedDate.getFullYear(), parsedDate.getMonth(), 1);
  }
}, { immediate: true });

onMounted(() => {
  // Initialize currentMonth to today if no date is selected
  if (!parseDate(props.modelValue)) {
    const today = new Date();
    currentMonth.value = new Date(today.getFullYear(), today.getMonth(), 1);
  }
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative" ref="dateInputRef">
    <input
      type="date"
      :value="modelValue"
      @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      class="sr-only"
    />
    
    <button
      type="button"
      @click="toggleCalendar"
      :disabled="disabled"
      class="w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm sm:text-base text-left flex items-center justify-between disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <span>
        {{ formattedValue }}
      </span>
      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
    </button>

    <Teleport to="body">
      <div
        v-if="isOpen"
        class="fixed z-[9999] bg-white border border-gray-200 rounded-xl shadow-2xl p-4 w-[300px]"
        :style="calendarPosition"
        data-calendar-dropdown
      >
      <div class="flex items-center justify-between mb-4">
        <button
          type="button"
          @click="previousMonth"
          class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <h3 class="text-sm font-semibold text-gray-900">{{ monthName }}</h3>
        <button
          type="button"
          @click="nextMonth"
          class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-7 gap-1 mb-2">
        <div
          v-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']"
          :key="day"
          class="text-xs font-medium text-gray-500 text-center py-1"
        >
          {{ day }}
        </div>
      </div>

      <div class="grid grid-cols-7 gap-1">
        <button
          v-for="(date, index) in calendarDays"
          :key="index"
          type="button"
          @click="date && selectDate(date)"
          :disabled="!date"
          class="aspect-square p-1 rounded-lg text-sm font-medium transition-colors disabled:opacity-0 disabled:cursor-default"
          :class="
            date
              ? isSelected(date!)
                ? 'bg-blue-600 text-white'
                : isToday(date!)
                ? 'bg-blue-100 text-blue-600 border border-blue-300'
                : 'text-gray-700 hover:bg-gray-100'
              : ''
          "
        >
          {{ date ? date.getDate() : '' }}
        </button>
      </div>

      <div class="mt-4 pt-4 border-t border-gray-200">
        <button
          type="button"
          @click="selectDate(new Date())"
          class="w-full px-3 py-2 text-sm text-blue-600 hover:text-blue-600 hover:bg-blue-600/10 rounded-lg transition-colors"
        >
          Today
        </button>
      </div>
    </div>
    </Teleport>
  </div>
</template>
