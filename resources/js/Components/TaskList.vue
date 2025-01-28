<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useTasksStore } from "@/stores/tasks";
import { useProjectsStore } from "@/stores/projects";
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
const projectsStore = useProjectsStore(); 
const timeSessionsStore = useTimeSessionsStore();

const selectedTaskIds = ref<number[]>([]);

const showConfirmModal = ref(false);
const confirmMessage = ref("");
let confirmAction: (() => void) | null = null;

let intervalId: number | null = null;
const openAccordion = ref<string[]>([]);

const showEditModal = ref(false);
const editTaskId = ref<number | null>(null);
const editTitle = ref("");
const editProjectId = ref<number | null>(null);

onMounted(async () => {
  await tasksStore.fetchTasks();
  await projectsStore.fetchProjects(); 
  await timeSessionsStore.fetchSessions();

  tasksStore.tasks.forEach((task: any) => {
    task.time_sessions = timeSessionsStore.sessions.filter(
      (session: TimeSession) => session.task_id === task.id
    );
  });
});

function canBulkDelete() {
  return selectedTaskIds.value.length >= 2;
}

function openBulkDeleteModal() {
  if (!canBulkDelete()) return;
  showConfirmModal.value = true;
  confirmMessage.value = `Are you sure you want to delete ${selectedTaskIds.value.length} tasks?`;
  confirmAction = async () => {
    await tasksStore.bulkDeleteTasks(selectedTaskIds.value);
    selectedTaskIds.value = [];
  };
}

function confirmYes() {
  if (confirmAction) confirmAction();
  showConfirmModal.value = false;
  confirmAction = null;
}
function confirmNo() {
  showConfirmModal.value = false;
  confirmAction = null;
}

async function deleteTask(taskId: number) {
  await tasksStore.deleteTask(taskId);
}

function openEditModal(task: any) {
  showEditModal.value = true;
  editTaskId.value = task.id;
  editTitle.value = task.title;
  editProjectId.value = task.project?.id ?? null;
}

async function saveEdit() {
  if (editTaskId.value === null) return;
  await tasksStore.updateTask(editTaskId.value, {
    title: editTitle.value,
    project_id: editProjectId.value, 
  });
  showEditModal.value = false;
}

function cancelEdit() {
  showEditModal.value = false;
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
    const s = dayjs(session.start_time);
    const f = session.end_time ? dayjs(session.end_time) : dayjs();
    return sum + f.diff(s);
  }, 0);
  const durationTime = dayjs.duration(totalMilliseconds, "milliseconds");
  return durationTime.format("HH:mm:ss");
}
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Tasks</h2>

    <div class="mb-4 flex items-center space-x-2">
      <Button
        class="bg-red-500 text-white px-3 py-1 rounded"
        :disabled="!canBulkDelete()"
        @click="openBulkDeleteModal"
      >
        Bulk Delete
      </Button>
      <span class="text-sm text-gray-600">
        {{ selectedTaskIds.length }} selected
      </span>
    </div>

    <div
      v-for="task in tasksStore.tasks"
      :key="task.id"
      class="mb-4 border rounded p-4 flex items-start"
    >
      <input
        type="checkbox"
        class="mr-2 mt-1"
        :value="task.id"
        v-model="selectedTaskIds"
      />

      <div class="flex-1">
        <div class="flex justify-between items-center">
          <div>
            <strong>{{ task.title }}</strong>
            <span v-if="task.project"> [{{ task.project.name }}]</span>
          </div>
          <div class="flex items-center space-x-2">
            <p>Total: {{ calculateTotalTime(task.time_sessions) }}</p>
            <button
              class="bg-gray-500 text-white px-2 py-1 rounded"
              @click="openEditModal(task)"
            >
              Edit
            </button>
            <button
              class="bg-red-500 text-white px-2 py-1 rounded"
              @click="deleteTask(task.id)"
            >
              Delete
            </button>
          </div>
        </div>

        <div class="mt-2 flex flex-wrap gap-2">
          <span
            v-for="tag in task.tags ?? []"
            :key="tag.id"
            class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm flex items-center"
          >
            {{ tag.name }}
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

    <div
      v-if="showConfirmModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white p-4 rounded shadow w-80">
        <h3 class="text-lg font-semibold mb-2">Confirm Deletion</h3>
        <p class="mb-4">{{ confirmMessage }}</p>
        <div class="flex justify-end space-x-2">
          <Button variant="outline" @click="confirmNo">Cancel</Button>
          <Button class="bg-red-500 text-white" @click="confirmYes">Delete</Button>
        </div>
      </div>
    </div>

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
          <Button variant="outline" @click="cancelEdit">Cancel</Button>
          <Button class="bg-blue-500 text-white" @click="saveEdit">Save</Button>
        </div>
      </div>
    </div>
  </div>
</template>
