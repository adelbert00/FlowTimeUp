<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
  nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;
  form.clearErrors();
  form.reset();
};
</script>

<template>
  <div>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
      Once your account is deleted, all of its resources and data will be permanently deleted. 
      Before deleting your account, please download any data or information that you wish to retain.
    </p>

    <button
      @click="confirmUserDeletion"
      class="px-4 py-2.5 bg-red-500/10 dark:bg-red-500/20 text-red-400 dark:text-red-500 border border-red-500/20 dark:border-red-500/30 rounded-lg font-medium hover:bg-red-500/20 dark:hover:bg-red-500/30 hover:border-red-500/30 dark:hover:border-red-500/40 transition-colors"
    >
      Delete Account
    </button>

    <!-- Modal -->
    <Teleport to="body">
      <div
        v-if="confirmingUserDeletion"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closeModal"
      >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
        
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
          <!-- Red accent -->
          <div class="absolute top-0 left-0 right-0 h-1 bg-red-500" />
          
          <div class="p-6">
            <!-- Icon -->
            <div class="w-12 h-12 rounded-full bg-red-500/10 dark:bg-red-500/20 flex items-center justify-center mb-4 mx-auto">
              <svg class="w-6 h-6 text-red-400 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>

            <h3 class="text-xl font-semibold text-gray-900 dark:text-white text-center mb-2">
              Delete Account
            </h3>
            <p class="text-gray-600 dark:text-gray-400 text-center text-sm mb-6">
              Are you sure you want to delete your account? This action is permanent and cannot be undone. 
              All your data will be lost.
            </p>

            <!-- Password input -->
            <div class="mb-6">
              <label for="delete_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Confirm your password
              </label>
              <input
                id="delete_password"
                ref="passwordInput"
                type="password"
                v-model="form.password"
                placeholder="Enter your password"
                @keyup.enter="deleteUser"
                class="w-full px-4 py-2.5 bg-gray-50/50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-red-500 dark:focus:border-red-400 focus:ring-1 focus:ring-red-500 dark:focus:ring-red-400 transition-colors"
                :class="{ 'border-red-500': form.errors.password }"
              />
              <p v-if="form.errors.password" class="text-red-400 dark:text-red-500 text-sm mt-1.5">
                {{ form.errors.password }}
              </p>
            </div>

            <div class="flex gap-3">
              <button
                @click="closeModal"
                class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="deleteUser"
                :disabled="form.processing"
                class="flex-1 px-4 py-2.5 bg-red-500 dark:bg-red-600 text-white rounded-lg font-medium hover:bg-red-600 dark:hover:bg-red-700 disabled:opacity-50 transition-colors"
              >
                <span v-if="form.processing">Deleting...</span>
                <span v-else>Delete Account</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
