<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, computed } from 'vue';
import dayjs from 'dayjs';

interface Task {
  id: number;
  title: string;
  due_date?: string;
  priority?: 'low' | 'medium' | 'high';
  completed: boolean;
  project?: {
    name: string;
    color?: string;
  };
  tags?: string[];
}

interface Project {
  id: number;
  name: string;
  color?: string;
}

const props = defineProps<{
  month: number;
  year: number;
  tasks: Task[];
  dailyTotals: Record<string, string>;
  projects?: Project[];
}>();

const currentMonth = ref(props.month);
const currentYear = ref(props.year);

const monthName = computed(() => {
  return dayjs(`${currentYear.value}-${currentMonth.value}-01`).format('MMMM YYYY');
});

const firstDayOfMonth = computed(() => {
  const day = dayjs(`${currentYear.value}-${currentMonth.value}-01`).day();
  return day === 0 ? 6 : day - 1;
});

const daysInMonth = computed(() => {
  return dayjs(`${currentYear.value}-${currentMonth.value}-01`).daysInMonth();
});

const calendarDays = computed(() => {
  const days: Array<{ date: string; day: number; isCurrentMonth: boolean; tasks: Task[]; totalTime?: string; isToday: boolean }> = [];
  const today = dayjs().format('YYYY-MM-DD');
  
  const prevMonth = currentMonth.value === 1 ? 12 : currentMonth.value - 1;
  const prevYear = currentMonth.value === 1 ? currentYear.value - 1 : currentYear.value;
  const daysInPrevMonth = dayjs(`${prevYear}-${prevMonth}-01`).daysInMonth();
  
  for (let i = firstDayOfMonth.value - 1; i >= 0; i--) {
    const day = daysInPrevMonth - i;
    const date = `${prevYear}-${String(prevMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    days.push({
      date,
      day,
      isCurrentMonth: false,
      tasks: [],
      isToday: date === today,
    });
  }
  
  for (let day = 1; day <= daysInMonth.value; day++) {
    const date = `${currentYear.value}-${String(currentMonth.value).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const dayTasks = props.tasks.filter(t => t.due_date === date);
    days.push({
      date,
      day,
      isCurrentMonth: true,
      tasks: dayTasks,
      totalTime: props.dailyTotals[date],
      isToday: date === today,
    });
  }
  
  const remainingDays = 42 - days.length;
  const nextMonth = currentMonth.value === 12 ? 1 : currentMonth.value + 1;
  const nextYear = currentMonth.value === 12 ? currentYear.value + 1 : currentYear.value;
  
  for (let day = 1; day <= remainingDays; day++) {
    const date = `${nextYear}-${String(nextMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    days.push({
      date,
      day,
      isCurrentMonth: false,
      tasks: [],
      isToday: date === today,
    });
  }
  
  return days;
});

function previousMonth() {
  if (currentMonth.value === 1) {
    currentMonth.value = 12;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
  loadMonth();
}

function nextMonth() {
  if (currentMonth.value === 12) {
    currentMonth.value = 1;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
  loadMonth();
}

function loadMonth() {
  router.get(route('calendar.index'), {
    month: currentMonth.value,
    year: currentYear.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
}

function goToToday() {
  const today = dayjs();
  currentMonth.value = today.month() + 1;
  currentYear.value = today.year();
  loadMonth();
}

const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
const weekDaysShort = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
</script>

<template>
  <Head title="Calendar" />

  <MainLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
      <div class="px-2 sm:px-4 lg:px-8 py-4 sm:py-6 max-w-7xl mx-auto pt-16 sm:pt-20 md:pt-24 xl:pt-6">
        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="hidden sm:block">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold font-sans text-gray-900 dark:text-white mb-1">Calendar</h1>
            <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm lg:text-base">View your tasks and time tracking by date</p>
          </div>
          
          <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
            <h2 class="text-lg sm:text-xl font-semibold font-sans text-gray-900 dark:text-white sm:hidden">{{ monthName }}</h2>
            
            <div class="flex items-center gap-1 sm:gap-2">
              <button
                @click="previousMonth"
                class="p-1.5 sm:p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white dark:hover:bg-gray-700 transition-colors"
              >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </button>
              
              <button
                @click="goToToday"
                class="px-2 sm:px-4 py-1.5 sm:py-2 bg-blue-600 text-white rounded-lg text-xs sm:text-sm font-medium hover:bg-blue-700 transition-colors"
              >
                Today
              </button>
              
              <button
                @click="nextMonth"
                class="p-1.5 sm:p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white dark:hover:bg-gray-700 transition-colors"
              >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
          <div class="p-2 sm:p-4 lg:p-6">
            <h2 class="text-lg sm:text-xl font-semibold font-sans text-gray-900 dark:text-white mb-3 sm:mb-4 text-center hidden sm:block">{{ monthName }}</h2>
            
            <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-1 sm:mb-2">
              <div
                v-for="(day, index) in weekDays"
                :key="day"
                class="text-center text-xs font-medium text-gray-500 dark:text-gray-400 py-1 sm:py-2"
              >
                <span class="hidden sm:inline">{{ day }}</span>
                <span class="sm:hidden">{{ weekDaysShort[index] }}</span>
              </div>
            </div>
            
            <div class="grid grid-cols-7 gap-1 sm:gap-2">
              <div
                v-for="(day, index) in calendarDays"
                :key="index"
                class="min-h-[60px] sm:min-h-[80px] lg:min-h-[100px] p-1 sm:p-2 rounded-lg border transition-colors"
                :class="[
                  day.isCurrentMonth
                    ? 'bg-gray-50/50 dark:bg-gray-700/30 border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                    : 'bg-white/30 dark:bg-gray-800/30 border-gray-100 dark:border-gray-700 opacity-40',
                  day.isToday && 'ring-2 ring-blue-500 ring-inset'
                ]"
              >
                <div class="flex items-start justify-between mb-0.5 sm:mb-1">
                  <span
                    class="text-xs sm:text-sm font-medium w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center rounded-full"
                    :class="[
                      day.isCurrentMonth ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500',
                      day.isToday && 'bg-blue-600 text-white'
                    ]"
                  >
                    {{ day.day }}
                  </span>
                  <span
                    v-if="day.totalTime && day.isCurrentMonth"
                    class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 font-mono hidden sm:block"
                  >
                    {{ day.totalTime }}
                  </span>
                </div>
                
                <div class="space-y-0.5 sm:space-y-1 hidden sm:block">
                  <div
                    v-for="task in day.tasks.slice(0, 2)"
                    :key="task.id"
                    @click="router.visit(route('tasks.edit', task.id))"
                    class="text-[10px] sm:text-xs px-1 sm:px-2 py-0.5 sm:py-1 rounded cursor-pointer truncate"
                    :class="task.completed
                      ? 'bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400 line-through'
                      : task.priority === 'high'
                      ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                      : task.priority === 'medium'
                      ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400'
                      : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'"
                    :style="task.project ? { backgroundColor: task.project.color + '20', color: task.project.color } : {}"
                  >
                    {{ task.title }}
                  </div>
                  <div
                    v-if="day.tasks.length > 2"
                    class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 px-1"
                  >
                    +{{ day.tasks.length - 2 }}
                  </div>
                </div>
                
                <div v-if="day.tasks.length > 0" class="sm:hidden mt-1">
                  <span class="text-[10px] px-1 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded">
                    {{ day.tasks.length }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
