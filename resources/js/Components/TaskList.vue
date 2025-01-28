<script setup lang="ts">
import { ref, onMounted } from "vue";
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

const tasksStore = useTasksStore();

const timeSessionsStore = useTimeSessionsStore();

let intervalId: number | null = null;
const openAccordion = ref<string[]>([])

onMounted(async () => {
  await tasksStore.fetchTasks();
  await timeSessionsStore.fetchSessions();

  tasksStore.tasks.forEach((task: any) => {
    task.time_sessions = timeSessionsStore.sessions.filter(
      (session: TimeSession) => session.task_id === task.id
    );
  });
});

async function removeTagFromTask(taskId: number, tagId: number) {
  await tasksStore.detachTag(taskId, tagId);

}

function startTimer(task: any) {
  task.isRunning = true;
  task.startTime = dayjs();
  task.timer = "00:00:00.000";

  intervalId = window.setInterval(() => {
    if (task.isRunning && task.startTime) {
      const now = dayjs();
      const diffMilliseconds = now.diff(task.startTime);
      const durationTime = dayjs.duration(diffMilliseconds, "milliseconds");
      task.timer = durationTime.format("HH:mm:ss.SSS");
    }
  }, 10);
}

async function stopTimer(task: any) {
  if (!task.isRunning) return;
  task.isRunning = false;

  const endTime = dayjs();
  await timeSessionsStore.createTimeSession(
    task.id,
    task.startTime.toISOString(),
    endTime.toISOString()
  );

  await timeSessionsStore.fetchSessions();
  task.time_sessions = timeSessionsStore.sessions.filter(
    (session: TimeSession) => session.task_id === task.id
  );

  if (intervalId) {
    clearInterval(intervalId);
    intervalId = null;
  }

  task.timer = "00:00:00.000";
  task.startTime = null;
}

function formatDate(date: string) {
  return dayjs(date).format("YYYY-MM-DD");
}

function formatTimeRange(start: string, end: string) {
  return `${dayjs(start).format("HH:mm")} - ${dayjs(end).format("HH:mm")}`;
}

function calculateTotalTime(sessions: TimeSession[] = []) {
  const totalMilliseconds = sessions.reduce((sum, session) => {
    const start = dayjs(session.start_time);
    const finish = session.end_time ? dayjs(session.end_time) : dayjs();
    return sum + finish.diff(start);
  }, 0);
  const durationTime = dayjs.duration(totalMilliseconds, "milliseconds");
  return durationTime.format("HH:mm:ss");
}
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Tasks</h2>

    <div
      v-for="task in tasksStore.tasks"
      :key="task.id"
      class="mb-4 border rounded p-4"
    >
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

      <div class="mt-2 flex flex-wrap gap-2">
        <span
          v-for="tag in task.tags ?? []"
          :key="tag.id"
          class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm flex items-center"
        >
          {{ tag.name }}
          <button
            class="ml-2 text-red-500"
            @click="removeTagFromTask(task.id, tag.id)"
          >
            x
          </button>
        </span>
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
          <AccordionTrigger>View Sessions</AccordionTrigger>
          <AccordionContent>
            <ul>
              <li
                v-for="session in task.time_sessions ?? []"
                :key="session.id"
                class="border-b last:border-none py-2"
              >
                <span class="block text-gray-700">
                  Date: {{ formatDate(session.start_time) }}
                </span>
                <span class="block text-gray-500">
                  Time:
                  {{ formatTimeRange(session.start_time, session.end_time) }}
                </span>
              </li>
            </ul>
          </AccordionContent>
        </AccordionItem>
      </Accordion>
    </div>
  </div>
</template>
