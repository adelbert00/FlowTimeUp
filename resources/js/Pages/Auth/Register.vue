<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
  recaptchaSiteKey?: string;
}>();

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  recaptcha_token: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const recaptchaError = ref('');

function loadRecaptcha(): Promise<void> {
  if (!props.recaptchaSiteKey) return Promise.resolve();
  if (window.grecaptcha) return Promise.resolve();

  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = `https://www.google.com/recaptcha/api.js?render=${props.recaptchaSiteKey}&badge=inline`;
    script.async = true;
    script.onload = () =>
      window.grecaptcha
        ? resolve()
        : reject(new Error('reCAPTCHA failed to initialize'));
    script.onerror = () => reject(new Error('Failed to load reCAPTCHA'));
    document.head.appendChild(script);
  });
}

async function getRecaptchaToken(): Promise<string> {
  if (!props.recaptchaSiteKey) return '';

  try {
    await loadRecaptcha();
  } catch (e) {
    recaptchaError.value =
      'reCAPTCHA is not available. Please refresh and try again.';
    return '';
  }

  try {
    return await new Promise<string>((resolve, reject) => {
      window.grecaptcha!.ready(async () => {
        try {
          const token = await window.grecaptcha!.execute(
            props.recaptchaSiteKey!,
            { action: 'register' }
          );
          resolve(token);
        } catch (error) {
          reject(error);
        }
      });
    });
  } catch {
    return '';
  }
}

const submit = async () => {
  form.recaptcha_token = await getRecaptchaToken();

  form.post(route('register'), {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};

declare global {
  interface Window {
    grecaptcha: {
      ready: (callback: () => void) => void;
      execute: (
        siteKey: string,
        options: { action: string }
      ) => Promise<string>;
    };
  }
}
</script>

<template>
  <GuestLayout>
    <Head title="Create account">
      <link
        v-if="recaptchaSiteKey"
        rel="preconnect"
        href="https://www.google.com"
      />
      <link
        v-if="recaptchaSiteKey"
        rel="preconnect"
        href="https://www.gstatic.com"
        crossorigin=""
      />
    </Head>

    <div class="text-center mb-6 sm:mb-8">
      <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
        Create your account
      </h1>
      <p class="text-gray-600 mt-2 text-sm sm:text-base">
        Start tracking your time today
      </p>
    </div>

    <form @submit.prevent="submit" class="space-y-4 sm:space-y-5">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label
            for="first_name"
            class="block text-sm font-medium text-gray-700 mb-1.5"
          >
            First name
          </label>
          <div class="relative">
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              aria-hidden="true"
            >
              <svg
                class="w-5 h-5 text-gray-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
              </svg>
            </div>
            <input
              id="first_name"
              type="text"
              v-model="form.first_name"
              required
              autofocus
              autocomplete="given-name"
              placeholder="Alex"
              class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm sm:text-base"
              :class="{ 'border-red-500': form.errors.first_name }"
            />
          </div>
          <p
            v-if="form.errors.first_name"
            class="text-red-400 text-xs sm:text-sm mt-1.5"
          >
            {{ form.errors.first_name }}
          </p>
        </div>

        <div>
          <label
            for="last_name"
            class="block text-sm font-medium text-gray-700 mb-1.5"
          >
            Last name
          </label>
          <div class="relative">
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              aria-hidden="true"
            >
              <svg
                class="w-5 h-5 text-gray-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
              </svg>
            </div>
            <input
              id="last_name"
              type="text"
              v-model="form.last_name"
              required
              autocomplete="family-name"
              placeholder="Taylor"
              class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm sm:text-base"
              :class="{ 'border-red-500': form.errors.last_name }"
            />
          </div>
          <p
            v-if="form.errors.last_name"
            class="text-red-400 text-xs sm:text-sm mt-1.5"
          >
            {{ form.errors.last_name }}
          </p>
        </div>
      </div>

      <div>
        <label
          for="email"
          class="block text-sm font-medium text-gray-700 mb-1.5"
        >
          Email address
        </label>
        <div class="relative">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            aria-hidden="true"
          >
            <svg
              class="w-5 h-5 text-gray-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
              />
            </svg>
          </div>
          <input
            id="email"
            type="email"
            v-model="form.email"
            required
            autocomplete="username"
            placeholder="name@company.com"
            class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm sm:text-base"
            :class="{ 'border-red-500': form.errors.email }"
          />
        </div>
        <p
          v-if="form.errors.email"
          class="text-red-400 text-xs sm:text-sm mt-1.5"
        >
          {{ form.errors.email }}
        </p>
      </div>

      <div>
        <label
          for="password"
          class="block text-sm font-medium text-gray-700 mb-1.5"
        >
          Password
        </label>
        <div class="relative">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            aria-hidden="true"
          >
            <svg
              class="w-5 h-5 text-gray-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
              />
            </svg>
          </div>
          <input
            id="password"
            :type="showPassword ? 'text' : 'password'"
            v-model="form.password"
            required
            autocomplete="new-password"
            placeholder="••••••••"
            class="w-full pl-10 pr-12 py-2.5 sm:py-3 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm sm:text-base"
            :class="{ 'border-red-500': form.errors.password }"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            :aria-label="showPassword ? 'Hide password' : 'Show password'"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors"
          >
            <svg
              v-if="!showPassword"
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
              />
            </svg>
            <svg
              v-else
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
              />
            </svg>
          </button>
        </div>
        <p
          v-if="form.errors.password"
          class="text-red-400 text-xs sm:text-sm mt-1.5"
        >
          {{ form.errors.password }}
        </p>
      </div>

      <div class="[content-visibility:auto] [contain-intrinsic-size:0_180px]">
        <label
          for="password_confirmation"
          class="block text-sm font-medium text-gray-700 mb-1.5"
        >
          Confirm password
        </label>
        <div class="relative">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            aria-hidden="true"
          >
            <svg
              class="w-5 h-5 text-gray-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
              />
            </svg>
          </div>
          <input
            id="password_confirmation"
            :type="showConfirmPassword ? 'text' : 'password'"
            v-model="form.password_confirmation"
            required
            autocomplete="new-password"
            placeholder="••••••••"
            class="w-full pl-10 pr-12 py-2.5 sm:py-3 bg-gray-50/50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors text-sm sm:text-base"
            :class="{ 'border-red-500': form.errors.password_confirmation }"
          />
          <button
            type="button"
            @click="showConfirmPassword = !showConfirmPassword"
            :aria-label="
              showConfirmPassword
                ? 'Hide confirm password'
                : 'Show confirm password'
            "
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors"
          >
            <svg
              v-if="!showConfirmPassword"
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
              />
            </svg>
            <svg
              v-else
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
              />
            </svg>
          </button>
        </div>
        <p
          v-if="form.errors.password_confirmation"
          class="text-red-400 text-xs sm:text-sm mt-1.5"
        >
          {{ form.errors.password_confirmation }}
        </p>
      </div>

      <p
        v-if="(form.errors as any).recaptcha"
        class="text-red-400 text-xs sm:text-sm"
      >
        {{ (form.errors as any).recaptcha }}
      </p>

      <div
        v-if="recaptchaError"
        class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg"
      >
        <p class="text-yellow-800 text-xs sm:text-sm">
          {{ recaptchaError }}
        </p>
      </div>

      <div class="[content-visibility:auto] [contain-intrinsic-size:0_200px]">
        <p class="text-xs text-gray-500">
          By creating an account, you agree to our
          <a href="#" class="text-blue-600 hover:text-blue-600"
            >Terms of Service</a
          >
          and
          <a href="#" class="text-blue-600 hover:text-blue-600"
            >Privacy Policy</a
          >.
        </p>

        <div
          v-if="recaptchaSiteKey"
          class="flex items-center justify-center gap-2 p-3 bg-gray-50 border border-gray-200 rounded-lg"
        >
          <div class="flex items-center gap-2 text-xs text-gray-600">
            <svg
              class="w-4 h-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              aria-hidden="true"
            >
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
              <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            <span>Protected by</span>
            <span class="font-semibold text-gray-700">reCAPTCHA</span>
          </div>
          <div class="flex items-center gap-1 text-xs">
            <a
              href="https://policies.google.com/privacy"
              target="_blank"
              rel="noopener noreferrer"
              class="text-blue-600 hover:text-blue-700 hover:underline"
            >
              Privacy
            </a>
            <span class="text-gray-400">•</span>
            <a
              href="https://policies.google.com/terms"
              target="_blank"
              rel="noopener noreferrer"
              class="text-blue-600 hover:text-blue-700 hover:underline"
            >
              Terms
            </a>
          </div>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          :aria-busy="form.processing ? 'true' : 'false'"
          aria-label="Create account"
          class="w-full py-2.5 sm:py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-blue-500/25 text-sm sm:text-base"
        >
          <span
            v-if="form.processing"
            class="flex items-center justify-center gap-2"
          >
            <svg
              class="w-5 h-5 animate-spin"
              fill="none"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              />
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              />
            </svg>
            Creating account...
          </span>
          <span v-else>Create account</span>
        </button>
      </div>

      <div
        class="relative my-4 sm:my-6 [content-visibility:auto] [contain-intrinsic-size:0_120px]"
      >
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-200" />
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-4 text-gray-500">Already have an account?</span>
        </div>
      </div>

      <Link
        :href="route('login')"
        class="block w-full py-2.5 sm:py-3 text-center border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100/50 hover:border-gray-300 transition-colors text-sm sm:text-base"
      >
        Sign in instead
      </Link>
    </form>
  </GuestLayout>
</template>
