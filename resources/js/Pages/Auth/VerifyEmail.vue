<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
  status?: string;
}>();

const page = usePage();
const user = page.props.auth?.user;

const form = useForm({});

const submit = () => {
  form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
  <GuestLayout>
    <Head title="Verify Email" />

    <!-- Header -->
    <div class="text-center mb-6 sm:mb-8">
      <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600/10 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
      </div>
      <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Verify your email</h1>
      <p class="text-gray-600 mt-2 text-xs sm:text-sm max-w-sm mx-auto">
        We've sent a verification link to
      </p>
      <p class="text-blue-600 font-medium mt-1 text-sm sm:text-base break-all px-4">{{ user?.email }}</p>
    </div>

    <!-- Instructions -->
    <div class="bg-gray-50/50 rounded-lg border border-gray-200 p-3 sm:p-4 mb-4 sm:mb-6">
      <div class="flex gap-3">
        <div class="flex-shrink-0">
          <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="text-xs sm:text-sm text-gray-600">
          <p>Please check your inbox and click the verification link to activate your account.</p>
          <p class="mt-2">Can't find the email? Check your spam folder or request a new link below.</p>
        </div>
      </div>
    </div>

    <!-- Success message -->
    <div v-if="verificationLinkSent" class="mb-4 sm:mb-6 p-3 sm:p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
      <p class="text-xs sm:text-sm text-emerald-600 flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        A new verification link has been sent to your email address.
      </p>
    </div>

    <form @submit.prevent="submit" class="space-y-3 sm:space-y-4">
      <!-- Resend button -->
      <button
        type="submit"
        :disabled="form.processing"
        class="w-full py-2.5 sm:py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-blue-500/25 text-sm sm:text-base"
      >
        <span v-if="form.processing" class="flex items-center justify-center gap-2">
          <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
          Sending...
        </span>
        <span v-else class="flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Resend Verification Email
        </span>
      </button>

      <!-- Logout link -->
      <Link
        :href="route('logout')"
        method="post"
        as="button"
        class="w-full py-2.5 sm:py-3 text-center border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100/50 hover:border-gray-300 transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Sign out and use different email
      </Link>
    </form>

    <!-- Help text -->
    <div class="mt-4 sm:mt-6 text-center">
      <p class="text-xs text-gray-500">
        Still having trouble? 
        <a href="mailto:support@flowtime.pl" class="text-blue-600 hover:text-blue-600">Contact support</a>
      </p>
    </div>
  </GuestLayout>
</template>
