<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
  status?: string;
}>();

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'));
};
</script>

<template>
  <GuestLayout>
    <Head title="Forgot Password" />

    <!-- Header -->
    <div class="text-center mb-8">
      <div class="w-16 h-16 rounded-full bg-blue-600/10 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-gray-900">Forgot your password?</h1>
      <p class="text-gray-600 mt-2 text-sm">
        No problem. Just enter your email address and we'll send you a link to reset it.
      </p>
    </div>

    <!-- Status message -->
    <div v-if="status" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
      <p class="text-sm text-emerald-600">{{ status }}</p>
    </div>

    <form @submit.prevent="submit" class="space-y-5">
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
          Email address
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
            </svg>
          </div>
          <input
            id="email"
            type="email"
            v-model="form.email"
            required
            autofocus
            placeholder="you@example.com"
            class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
            :class="{ 'border-red-500': form.errors.email }"
          />
        </div>
        <p v-if="form.errors.email" class="text-red-400 text-sm mt-1.5">
          {{ form.errors.email }}
        </p>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="form.processing"
        class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-blue-500/25"
      >
        <span v-if="form.processing" class="flex items-center justify-center gap-2">
          <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
          Sending...
        </span>
        <span v-else>Send Reset Link</span>
      </button>
    </form>
  </GuestLayout>
</template>
