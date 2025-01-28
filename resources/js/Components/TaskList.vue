<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useTasksStore } from "@/stores/tasks";
import { useTimeSessionsStore, TimeSession } from "@/stores/timeSessions";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
import Button from "./ui/button/Button.vue";
import { useProjectsStore } from "@/stores/projects"; 

import Accordion from "./ui/accordion/Accordion.vue";
import AccordionItem from "./ui/accordion/AccordionItem.vue";
import AccordionTrigger from "./ui/accordion/AccordionTrigger.vue";
import AccordionContent from "./ui/accordion/AccordionContent.vue";

dayjs.extend(duration);

// Inicjujemy store z zadaniami
const tasksStore = useTasksStore();
const timeSessionsStore = useTimeSessionsStore();
const projectsStore = useProjectsStore(); // <-- init

// Timery i akordeon
let intervalId: number | null = null;
const openAccordion = ref<string[]>([]);

// Zmienne do modala edycji
const showEditModal = ref(false);
const editTaskId = ref<number | null>(null);
const editTitle = ref("");
// (Opcjonalnie) project dla edycji:
const editProjectId = ref<number | null>(null);

// Lifecycle
onMounted(async () => {
  await tasksStore.fetchTasks();
  await timeSessionsStore.fetchSessions();
  await projectsStore.fetchProjects(); 

  // Przypisz time_sessions do local (opcjonalnie)
  tasksStore.tasks.forEach((task: any) => {
    task.time_sessions = timeSessionsStore.sessions.filter(
      (session: TimeSession) => session.task_id === task.id
    );
  });
});

// Timer - start
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

// Timer - stop
async function stopTimer(task: any) {
  if (!task.isRunning) return;

  task.isRunning = false;
  const endTime = dayjs();

  // Zapis sesji
  await timeSessionsStore.createTimeSession(
    task.id,
    task.startTime.toISOString(),
    endTime.toISOString()
  );

  // Odśwież sessions
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

// Funkcje formatowania
function formatDate(date: string) {
  return dayjs(date).format("YYYY-MM-DD");
}

function formatTimeRange(start: string, end: string) {
  return `${dayjs(start).format("HH:mm")} - ${dayjs(end).format("HH:mm")}`;
}

function calculateTotalTime(sessions: TimeSession[] = []) {
  const totalMilliseconds = sessions.reduce((sum, session) => {
    const s = dayjs(session.start_time);
    const f = session.end_time ? dayjs(session.end_time) : dayjs();
    return sum + f.diff(s);
  }, 0);
  const durationTime = dayjs.duration(totalMilliseconds, "milliseconds");
  return durationTime.format("HH:mm:ss");
}

// Kasowanie zadania
async function deleteTask(taskId: number) {
  await tasksStore.deleteTask(taskId);
}

// Otwieranie modala edycji
function openEditModal(task: any) {
  showEditModal.value = true;
  editTaskId.value = task.id;
  editTitle.value = task.title;
  // (opcjonalnie) if you have project in the store
  editProjectId.value = task.project?.id ?? null;
}

// Zapis edycji
async function saveEdit() {
  if (editTaskId.value === null) return;

  await tasksStore.updateTask(editTaskId.value, {
    title: editTitle.value,
    project_id: editProjectId.value, // if you want to handle project
  });
  showEditModal.value = false;
}

// Anulowanie edycji
function cancelEdit() {
  showEditModal.value = false;
}

// Accordion
function toggleAccordion(taskId: string) {
  if (openAccordion.value.includes(taskId)) {
    openAccordion.value = openAccordion.value.filter((id) => id !== taskId);
  } else {
    openAccordion.value.push(taskId);
  }
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
        <div class="flex items-center space-x-2">
          <p>Total: {{ calculateTotalTime(task.time_sessions) }}</p>

          <!-- Przycisk Edit -->
          <button
            class="bg-gray-500 text-white px-2 py-1 rounded"
            @click="openEditModal(task)"
          >
            Edit
          </button>

          <!-- Przycisk Delete -->
          <button
            class="bg-red-500 text-white px-2 py-1 rounded"
            @click="deleteTask(task.id)"
          >
            Delete
          </button>
        </div>
      </div>

      <!-- Tagi -->
      <div class="mt-2 flex flex-wrap gap-2">
        <span
          v-for="tag in task.tags ?? []"
          :key="tag.id"
          class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm flex items-center"
        >
          {{ tag.name }}
        </span>
      </div>

      <!-- Timer -->
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

      <!-- Accordion z sesjami -->
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

    <!-- Modal edycji -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white p-4 rounded shadow w-80">
        <h3 class="text-lg font-semibold mb-2">Edit Task</h3>
        <label class="block mb-2 font-medium">Title:</label>
        <input
          v-model="editTitle"
          class="border px-2 py-1 rounded w-full mb-3"
        />

        <!-- (opcjonalnie) Wybór projektu -->
        <label class="block mb-2 font-medium">Project:</label>
        <select
  v-model.number="editProjectId"
  class="border px-2 py-1 rounded w-full mb-3"
>
  <option :value="null">-- Select project --</option>
  <option
    v-for="proj in projectsStore.projects"
    :key="proj.id"
    :value="proj.id"
  >
    {{ proj.name }}
  </option>
</select>


        <div class="mt-3 flex justify-end space-x-2">
          <button
            class="border px-3 py-1 rounded"
            @click="cancelEdit"
          >
            Cancel
          </button>
          <button
            class="bg-blue-500 text-white px-3 py-1 rounded"
            @click="saveEdit"
          >
            Save
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
