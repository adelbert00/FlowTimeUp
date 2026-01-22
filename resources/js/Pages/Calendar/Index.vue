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
  return dayjs(`${currentYear.value}-${currentMonth.value}-01`).day();
});

const daysInMonth = computed(() => {
  return dayjs(`${currentYear.value}-${currentMonth.value}-01`).daysInMonth();
});

const calendarDays = computed(() => {
  const days: Array<{ date: string; day: number; isCurrentMonth: boolean; tasks: Task[]; totalTime?: string }> = [];
  
  // Previous month days
  const prevMonth = currentMonth.value === 1 ? 12 : currentMonth.value - 1;
  const prevYear = currentMonth.value === 1 ? currentYear.value - 1 : currentYear.value;
  const daysInPrevMonth = dayjs(`${prevYear}-${prevMonth}-01`).daysInMonth();
  
  for (let i = firstDayOfMonth.value - 1; i >= 0; i--) {
    const day = daysInPrevMonth - i;
    days.push({
      date: `${prevYear}-${String(prevMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
      day,
      isCurrentMonth: false,
      tasks: [],
    });
  }
  
  // Current month days
  for (let day = 1; day <= daysInMonth.value; day++) {
    const date = `${currentYear.value}-${String(currentMonth.value).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const dayTasks = props.tasks.filter(t => t.due_date === date);
    days.push({
      date,
      day,
      isCurrentMonth: true,
      tasks: dayTasks,
      totalTime: props.dailyTotals[date],
    });
  }
  
  // Next month days to fill the grid
  const remainingDays = 42 - days.length;
  const nextMonth = currentMonth.value === 12 ? 1 : currentMonth.value + 1;
  const nextYear = currentMonth.value === 12 ? currentYear.value + 1 : currentYear.value;
  
  for (let day = 1; day <= remainingDays; day++) {
    days.push({
      date: `${nextYear}-${String(nextMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
      day,
      isCurrentMonth: false,
      tasks: [],
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

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
</script>

<template>
  <Head title="Calendar" />

  <MainLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 max-w-7xl pt-20 sm:pt-24 md:pt-28 xl:pt-8">
        <div class="mb-6 sm:mb-8 flex items-center justify-between">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Calendar</h1>
            <p class="text-gray-600 text-sm sm:text-base">View your tasks and time tracking by date</p>
          </div>
          
          <div class="flex items-center gap-2">
            <button
              @click="previousMonth"
              class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-white transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
            
            <button
              @click="goToToday"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
            >
              Today
            </button>
            
            <button
              @click="nextMonth"
              class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-white transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div class="p-4 sm:p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 text-center">{{ monthName }}</h2>
            
            <!-- Week days header -->
            <div class="grid grid-cols-7 gap-2 mb-2">
              <div
                v-for="day in weekDays"
                :key="day"
                class="text-center text-xs font-medium text-gray-500 py-2"
              >
                {{ day }}
              </div>
            </div>
            
            <!-- Calendar grid -->
            <div class="grid grid-cols-7 gap-2">
              <div
                v-for="(day, index) in calendarDays"
                :key="index"
                class="min-h-[100px] sm:min-h-[120px] p-2 rounded-lg border transition-colors"
                :class="day.isCurrentMonth
                  ? 'bg-gray-50/50 border-gray-200 hover:border-gray-300'
                  : 'bg-white/30 border-gray-200 opacity-50'"
              >
                <div class="flex items-center justify-between mb-1">
                  <span
                    class="text-sm font-medium"
                    :class="day.isCurrentMonth ? 'text-gray-700' : 'text-slate-600'"
                  >
                    {{ day.day }}
                  </span>
                  <span
                    v-if="day.totalTime && day.isCurrentMonth"
                    class="text-xs text-emerald-600 font-mono"
                  >
                    {{ day.totalTime }}
                  </span>
                </div>
                
                <div class="space-y-1">
                  <div
                    v-for="task in day.tasks.slice(0, 3)"
                    :key="task.id"
                    @click="router.visit(route('tasks.edit', task.id))"
                    class="text-xs px-2 py-1 rounded cursor-pointer truncate"
                    :class="task.completed
                      ? 'bg-gray-100 text-gray-500 line-through'
                      : task.priority === 'high'
                      ? 'bg-red-100 text-red-600 border border-red-300'
                      : task.priority === 'medium'
                      ? 'bg-amber-100 text-amber-600 border border-amber-300'
                      : 'bg-blue-100 text-blue-600 border border-blue-300'"
                    :style="task.project ? { borderColor: task.project.color + '80', backgroundColor: task.project.color + '20', color: task.project.color } : {}"
                  >
                    {{ task.title }}
                  </div>
                  <div
                    v-if="day.tasks.length > 3"
                    class="text-xs text-gray-500 px-2"
                  >
                    +{{ day.tasks.length - 3 }} more
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
