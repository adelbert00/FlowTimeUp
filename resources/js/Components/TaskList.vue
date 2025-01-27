<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Tasks</h2>

    <div v-for="task in tasksStore.tasks" :key="task.id" class="mb-4 border rounded p-4">
      <div class="flex justify-between items-center">
        <div>
          <strong>{{ task.title }}</strong>
          <span v-if="task.project"> [{{ task.project.name }}]</span>
        </div>
        <div class="flex items-center space-x-1">
        <p>Total:</p>
        <span class="font-bold">
          {{ calculateTotalTime(task.time_sessions) }}
        </span>
      </div>
      </div>

      <div class="flex items-center space-x-4 mt-2">
        <div class="text-gray-600 font-mono w-[12ch] text-center">
          {{ task.timer ?? "00:00:00.000" }}
        </div>
        <div>
          <Button
            v-if="!task.isRunning"
            @click="startTimer(task)"
            class="px-3 py-1 bg-green-500 text-white rounded"
          >
            Start
          </Button>
          <Button
            v-else
            @click="stopTimer(task)"
            class="px-3 py-1 bg-red-500 text-white rounded"
          >
            Stop
          </Button>
        </div>
      </div>

      <Accordion v-model="openAccordion" collapsible>
  <AccordionItem :value="`task-${task.id}`">
    <AccordionTrigger>
      View Sessions
    </AccordionTrigger>
    <AccordionContent>
      <ul>
        <li
          v-for="session in (task.time_sessions ?? [])"
          :key="session.id"
          class="border-b last:border-none py-2"
        >
          <span class="block text-gray-700">
            Date: {{ formatDate(session.start_time) }}
          </span>
          <span class="block text-gray-500">
            Time: {{ formatTimeRange(session.start_time, session.end_time) }}
          </span>
        </li>
      </ul>
    </AccordionContent>
  </AccordionItem>
</Accordion>


    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useTasksStore } from "@/stores/tasks";
import { useTimeSessionsStore, TimeSession } from "@/stores/timeSessions";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
import Button from "./ui/button/Button.vue";

import Accordion from "./ui/accordion/Accordion.vue";
import AccordionItem from "./ui/accordion/AccordionItem.vue";
import AccordionTrigger from "./ui/accordion/AccordionTrigger.vue";
import AccordionContent from "./ui/accordion/AccordionContent.vue";

dayjs.extend(duration);

export default defineComponent({
  name: "TaskList",
  components: {
    Accordion,
    AccordionItem,
    AccordionTrigger,
    AccordionContent,
    Button,
  },
  setup() {
    const tasksStore = useTasksStore();
    const timeSessionsStore = useTimeSessionsStore();

    let intervalId: number | null = null;
    const openAccordion = ref<string[]>([]); 

    const toggleAccordion = (taskId: string) => {
      if (openAccordion.value.includes(taskId)) {
        openAccordion.value = openAccordion.value.filter((id) => id !== taskId);
      } else {
        openAccordion.value.push(taskId);
      }
    };

    const startTimer = (task: any) => {
      task.isRunning = true;
      task.startTime = dayjs();
      task.timer = "00:00:00.000";

      intervalId = window.setInterval(() => {
        if (task.isRunning && task.startTime) {
          const now = dayjs();
          const diffMilliseconds = now.diff(task.startTime);
          const duration = dayjs.duration(diffMilliseconds, "milliseconds");
          task.timer = duration.format("HH:mm:ss.SSS");
        }
      }, 10);
    };

    const stopTimer = async (task: any) => {
      if (!task.isRunning) return;

      task.isRunning = false;

      const endTime = dayjs();
      await timeSessionsStore.createTimeSession(
        task.id,
        task.startTime.toISOString(),
        endTime.toISOString()
      );

      const updatedSessions = await timeSessionsStore.fetchSessions();
      task.time_sessions = updatedSessions.filter((session: TimeSession) => session.task_id === task.id);

      task.totalTime = calculateTotalTime(task.time_sessions);

      if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
      }

      task.timer = "00:00:00.000";
      task.startTime = null;
    };

    const formatDate = (date: string) => dayjs(date).format("YYYY-MM-DD");

    const formatTimeRange = (start: string, end: string) =>
      `${dayjs(start).format("HH:mm")} - ${dayjs(end).format("HH:mm")}`;

    const calculateTotalTime = (sessions: TimeSession[] = []) => {
      const totalMilliseconds = sessions.reduce((sum, session) => {
        const start = dayjs(session.start_time);
        const end = session.end_time ? dayjs(session.end_time) : dayjs();
        return sum + end.diff(start);
      }, 0);
      const duration = dayjs.duration(totalMilliseconds, "milliseconds");
      return duration.format("HH:mm:ss");
    };

    onMounted(async () => {
      await tasksStore.fetchTasks();
      await timeSessionsStore.fetchSessions();

      tasksStore.tasks.forEach((task: any) => {
        task.time_sessions = timeSessionsStore.sessions.filter(
          (session: TimeSession) => session.task_id === task.id
        );
      });
    });

    return {
      tasksStore,
      startTimer,
      stopTimer,
      openAccordion,
      toggleAccordion,
      formatDate,
      formatTimeRange,
      calculateTotalTime,
    };
  },
});
</script>
