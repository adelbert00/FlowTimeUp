<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
  mustVerifyEmail?: boolean;
  status?: string;
}>();

const user = usePage().props.auth.user;

const form = useForm({
  name: user.name,
  email: user.email,
});

const submit = () => {
  form.patch(route('profile.update'));
};
</script>

<template>
  <form @submit.prevent="submit" class="space-y-5 max-w-xl">
    <!-- Name -->
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
        Full name
      </label>
      <input
        id="name"
        type="text"
        v-model="form.name"
        required
        autocomplete="name"
        class="w-full px-4 py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
        :class="{ 'border-red-500': form.errors.name }"
      />
      <p v-if="form.errors.name" class="text-red-400 text-sm mt-1.5">
        {{ form.errors.name }}
      </p>
    </div>

    <!-- Email -->
    <div>
      <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
        Email address
      </label>
      <input
        id="email"
        type="email"
        v-model="form.email"
        required
        autocomplete="username"
        class="w-full px-4 py-2.5 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
        :class="{ 'border-red-500': form.errors.email }"
      />
      <p v-if="form.errors.email" class="text-red-400 text-sm mt-1.5">
        {{ form.errors.email }}
      </p>
    </div>

    <!-- Email verification notice -->
    <div v-if="mustVerifyEmail && !user.email_verified_at" class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-lg">
      <p class="text-sm text-amber-400">
        Your email address is unverified.
        <Link
          :href="route('verification.send')"
          method="post"
          as="button"
          class="font-medium underline hover:text-amber-600"
        >
          Click here to re-send the verification email.
        </Link>
      </p>
      <p v-if="status === 'verification-link-sent'" class="mt-2 text-sm text-emerald-600">
        A new verification link has been sent to your email address.
      </p>
    </div>

    <!-- Submit -->
    <div class="flex items-center gap-4">
      <button
        type="submit"
        :disabled="form.processing"
        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-50 transition-colors"
      >
        <span v-if="form.processing">Saving...</span>
        <span v-else>Save changes</span>
      </button>

      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        leave-active-class="transition ease-in duration-150"
        leave-to-class="opacity-0"
      >
        <span v-if="form.recentlySuccessful" class="text-sm text-emerald-600 flex items-center gap-1">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          Saved
        </span>
      </Transition>
    </div>
  </form>
</template>
