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
    top: showAbove
      ? `${rect.top - calendarHeight - 8}px`
      : `${rect.bottom + 8}px`,
    left: `${Math.max(8, Math.min(rect.left, window.innerWidth - 308))}px`,
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
  return currentMonth.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric',
  });
});

const formattedValue = computed(() => {
  const date = parseDate(props.modelValue);
  if (!date) return props.placeholder || 'Select date';
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  });
});

const showingPlaceholder = computed(() => !parseDate(props.modelValue));

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
  if (
    !dateInputRef.value.contains(target) &&
    calendar &&
    !calendar.contains(target)
  ) {
    isOpen.value = false;
  }
}

watch(
  () => props.modelValue,
  (newValue) => {
    const parsedDate = parseDate(newValue);
    if (parsedDate) {
      selectedDate.value = parsedDate;
      currentMonth.value = new Date(
        parsedDate.getFullYear(),
        parsedDate.getMonth(),
        1
      );
    }
  },
  { immediate: true }
);

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
      @input="
        emit('update:modelValue', ($event.target as HTMLInputElement).value)
      "
      class="sr-only"
    />

    <button
      type="button"
      @click="toggleCalendar"
      :disabled="disabled"
      class="flex h-10 w-full items-center justify-between gap-2 rounded-xl border border-border bg-surface-raised px-3 py-2 text-left text-sm text-primary ring-offset-white transition-all focus-visible:outline-none focus-visible:border-accent focus-visible:ring-2 focus-visible:ring-accent/30 disabled:cursor-not-allowed disabled:opacity-50"
    >
      <span
        class="min-w-0 flex-1 truncate font-normal"
        :class="
          showingPlaceholder
            ? 'text-black dark:text-white/45'
            : 'text-primary'
        "
      >
        {{ formattedValue }}
      </span>
      <svg
        class="h-4 w-4 shrink-0 text-secondary"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2.5"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
      </svg>
    </button>

    <Teleport to="body">
      <div
        v-if="isOpen"
        class="fixed z-[9999] bg-surface-overlay border border-border rounded-2xl shadow-2xl p-4 w-[300px] animate-in fade-in zoom-in duration-200"
        :style="calendarPosition"
        data-calendar-dropdown
      >
        <div class="flex items-center justify-between mb-4">
          <button
            type="button"
            @click="previousMonth"
            class="p-2 rounded-xl text-secondary hover:text-accent hover:bg-surface-raised transition-all"
          >
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              stroke-width="2.5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
          <h3
            class="text-xs font-black text-primary uppercase tracking-tighter"
          >
            {{ monthName }}
          </h3>
          <button
            type="button"
            @click="nextMonth"
            class="p-2 rounded-xl text-secondary hover:text-accent hover:bg-surface-raised transition-all"
          >
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              stroke-width="2.5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
        </div>

        <div class="grid grid-cols-7 gap-1 mb-2">
          <div
            v-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']"
            :key="day"
            class="text-[10px] font-black text-muted uppercase tracking-widest text-center py-1"
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
            class="aspect-square p-1 rounded-xl text-xs font-bold transition-all disabled:opacity-0 disabled:cursor-default"
            :class="
              date
                ? isSelected(date!)
                  ? 'bg-accent text-white'
                  : isToday(date!)
                    ? 'bg-accent/10 text-accent border border-accent/20'
                    : 'text-secondary hover:bg-surface-raised hover:text-primary'
                : ''
            "
          >
            {{ date ? date.getDate() : '' }}
          </button>
        </div>

        <div class="mt-4 pt-4 border-t border-border flex gap-2">
          <button
            type="button"
            @click="selectDate(new Date())"
            class="flex-1 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-accent hover:bg-accent/10 rounded-xl transition-all"
          >
            Today
          </button>
          <button
            v-if="modelValue"
            type="button"
            @click="emit('update:modelValue', '')"
            class="flex-1 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-muted hover:text-secondary hover:bg-surface-raised rounded-xl transition-all"
          >
            Clear
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>
