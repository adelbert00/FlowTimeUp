<script setup lang="ts">
import { computed, ref, onBeforeUnmount } from 'vue';
import { defineProps } from 'vue';
import { Task } from '@/stores/tasks';
import { useTimeSessionsStore } from '@/stores/timeSessions';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps<{
  task: Task;
}>();

const timeSessionsStore = useTimeSessionsStore();

const isRunning = ref(false);
const startTime = ref<Date | null>(null);
const currentTime = ref<Date>(new Date());
let intervalId: number | null = null;
const showAddManualTimeModal = ref(false);
const manualTimeForm = ref({
  duration: 0, // in minutes
  description: '',
});

const startTimer = () => {
  isRunning.value = true;
  startTime.value = new Date();
  intervalId = window.setInterval(() => {
    currentTime.value = new Date();
  }, 1);
};

const stopTimer = async () => {
  if (!startTime.value) return;

  isRunning.value = false;
  const endTime = new Date();
  await timeSessionsStore.createTimeSession(
    props.task.id,
    startTime.value.toISOString(),
    endTime.toISOString()
  );
  if (intervalId) {
    clearInterval(intervalId);
    intervalId = null;
  }
  startTime.value = null;
};

const formattedTime = computed(() => {
  if (!isRunning.value || !startTime.value) {
    return '00:00:00:000';
  }

  const diffMilliseconds =
    currentTime.value.getTime() - startTime.value.getTime();
  if (diffMilliseconds <= 0) {
    return '00:00:00:000';
  }

  const diffSeconds = Math.floor(diffMilliseconds / 1000);
  const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
  const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(
    2,
    '0'
  );
  const seconds = String(diffSeconds % 60).padStart(2, '0');
  const milliseconds = String(diffMilliseconds % 1000).padStart(3, '0');

  return `${hours}:${minutes}:${seconds}:${milliseconds}`;
});

onBeforeUnmount(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>

<template>
  <div class="flex items-center space-x-4">
    <div>
      <strong>{{ task.title }}</strong>
      <span v-if="task.project"> [{{ task.project.name }}]</span>
    </div>

    <div class="text-gray-600 text-center w-[10ch] font-mono">
      {{ formattedTime }}
    </div>

    <div class="pl-4">
      <button
        v-if="!isRunning"
        @click="startTimer"
        class="px-3 py-1 bg-green-500 text-white rounded"
      >
        Start
      </button>
      <button
        v-else
        @click="stopTimer"
        class="px-3 py-1 bg-red-500 text-white rounded"
      >
        Stop
      </button>
      <button
        @click="showAddManualTimeModal = true"
        class="ml-2 px-3 py-1 bg-blue-500 text-white rounded"
      >
        Add Manual Time
      </button>
    </div>
  </div>

  <Modal :show="showAddManualTimeModal" @close="showAddManualTimeModal = false">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900">Add Manual Time for {{ task.title }}</h2>

      <div class="mt-6">
        <label for="duration" class="block font-medium text-sm text-gray-700">Duration (minutes)</label>
        <TextInput
          id="duration"
          type="number"
          class="mt-1 block w-full"
          v-model="manualTimeForm.duration"
          required
          min="1"
        />
      </div>

      <div class="mt-4">
        <label for="description" class="block font-medium text-sm text-gray-700">Description (Optional)</label>
        <TextInput
          id="description"
          type="text"
          class="mt-1 block w-full"
          v-model="manualTimeForm.description"
        />
      </div>

      <div class="mt-6 flex justify-end">
        <PrimaryButton @click="addManualTime">
          Add Time
        </PrimaryButton>
        <button
          type="button"
          class="ml-3 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          @click="showAddManualTimeModal = false"
        >
          Cancel
        </button>
      </div>
    </div>
  </Modal>
</template>

<script lang="ts">
import { useTimeSessionsStore } from '@/stores/timeSessions';

const addManualTime = async () => {
  const endTime = new Date();
  const startTime = new Date(endTime.getTime() - manualTimeForm.value.duration * 60 * 1000);

  await timeSessionsStore.createTimeSession(
    props.task.id,
    startTime.toISOString(),
    endTime.toISOString(),
    true, // is_billable
    null, // billable_rate
    manualTimeForm.value.description
  );

  showAddManualTimeModal.value = false;
  manualTimeForm.value.duration = 0;
  manualTimeForm.value.description = '';
};

onBeforeUnmount(() => {
  if (intervalId) clearInterval(intervalId);
});
