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
    <div class="space-y-6 animate-fade-up">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-primary uppercase tracking-tighter">Timeline</h1>
          <p class="text-secondary text-[10px] font-bold uppercase tracking-widest mt-1">Calendar & Schedule Analysis</p>
        </div>
        
        <div class="flex items-center gap-3 bg-surface-raised p-1 rounded-2xl border border-border shadow-sm">
          <button
            @click="previousMonth"
            class="p-2.5 rounded-xl text-secondary hover:text-accent hover:bg-surface-overlay transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          </button>
          
          <button
            @click="goToToday"
            class="px-6 py-2 bg-accent text-accent-text rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-accent-hover transition-all shadow-lg shadow-accent/20 active:scale-95"
          >
            Present
          </button>
          
          <button
            @click="nextMonth"
            class="p-2.5 rounded-xl text-secondary hover:text-accent hover:bg-surface-overlay transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>

      <div class="bg-surface-raised rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border bg-surface-overlay/30 flex items-center justify-between">
          <h2 class="text-lg font-black text-primary uppercase tracking-tighter">{{ monthName }}</h2>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
            <span class="text-[10px] font-black text-muted uppercase tracking-widest">Active Timeline</span>
          </div>
        </div>

        <div class="p-4 sm:p-6 lg:p-8">
          <div class="grid grid-cols-7 gap-3 mb-6">
            <div
              v-for="(day, index) in weekDays"
              :key="day"
              class="text-center text-[10px] font-black text-secondary uppercase tracking-[0.2em]"
            >
              <span class="hidden sm:inline">{{ day }}</span>
              <span class="sm:hidden">{{ weekDaysShort[index] }}</span>
            </div>
          </div>
          
          <div class="grid grid-cols-7 gap-3">
            <div
              v-for="(day, index) in calendarDays"
              :key="index"
              class="min-h-[100px] sm:min-h-[120px] lg:min-h-[140px] p-3 rounded-2xl border transition-all relative group"
              :class="[
                day.isCurrentMonth
                  ? 'bg-surface-overlay border-border hover:border-accent/40 hover:shadow-xl hover:shadow-accent/5'
                  : 'bg-surface/10 border-border/20 opacity-30 grayscale pointer-events-none',
                day.isToday && 'ring-2 ring-accent shadow-lg shadow-accent/10 border-accent/20'
              ]"
            >
              <div class="flex items-start justify-between mb-3">
                <span
                  class="text-xs font-black w-6 h-6 flex items-center justify-center rounded-lg transition-colors"
                  :class="[
                    day.isToday ? 'bg-accent text-accent-text' : 'text-primary'
                  ]"
                >
                  {{ day.day }}
                </span>
                <span
                  v-if="day.totalTime && day.isCurrentMonth"
                  class="text-[9px] font-mono font-black text-accent bg-accent/10 px-1.5 py-0.5 rounded border border-accent/20"
                >
                  {{ day.totalTime }}
                </span>
              </div>
              
              <div class="space-y-1.5 overflow-hidden">
                <div
                  v-for="task in day.tasks.slice(0, 3)"
                  :key="task.id"
                  @click="router.visit(route('tasks.edit', task.id))"
                  class="text-[9px] font-bold px-2 py-1.5 rounded-lg cursor-pointer truncate transition-all border border-transparent hover:border-white/10"
                  :class="task.completed
                    ? 'bg-muted/10 text-muted line-through grayscale'
                    : 'bg-accent/10 text-accent'"
                  :style="task.project && !task.completed ? { backgroundColor: task.project.color + '15', color: task.project.color, borderColor: task.project.color + '30' } : {}"
                >
                  {{ task.title }}
                </div>
                <div
                  v-if="day.tasks.length > 3"
                  class="text-[9px] font-black text-muted uppercase tracking-widest pl-1 mt-1"
                >
                  +{{ day.tasks.length - 3 }} More
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
