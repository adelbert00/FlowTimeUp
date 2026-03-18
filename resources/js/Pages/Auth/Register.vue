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
</script>

<template>
  <GuestLayout>
    <Head title="Create account" />

    <div class="text-center mb-10">
      <h1 class="text-2xl font-black text-primary uppercase tracking-tighter">Initialize Identity</h1>
      <p class="text-secondary mt-2 text-[10px] font-bold uppercase tracking-[0.2em]">New Core User Enrollment</p>
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="space-y-2">
          <label for="first_name" class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] ml-1">First Name</label>
          <input
            id="first_name"
            type="text"
            v-model="form.first_name"
            required
            placeholder="John"
            class="w-full px-4 py-3.5 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all text-sm font-bold"
            :class="{ 'border-danger': form.errors.first_name }"
          />
          <p v-if="form.errors.first_name" class="text-danger text-[10px] font-black uppercase mt-1">{{ form.errors.first_name }}</p>
        </div>

        <div class="space-y-2">
          <label for="last_name" class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] ml-1">Last Name</label>
          <input
            id="last_name"
            type="text"
            v-model="form.last_name"
            required
            placeholder="Doe"
            class="w-full px-4 py-3.5 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all text-sm font-bold"
            :class="{ 'border-danger': form.errors.last_name }"
          />
          <p v-if="form.errors.last_name" class="text-danger text-[10px] font-black uppercase mt-1">{{ form.errors.last_name }}</p>
        </div>
      </div>

      <div class="space-y-2">
        <label for="email" class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] ml-1">Access ID (Email)</label>
        <input
          id="email"
          type="email"
          v-model="form.email"
          required
          placeholder="user@v2.core"
          class="w-full px-4 py-3.5 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all text-sm font-bold"
          :class="{ 'border-danger': form.errors.email }"
        />
        <p v-if="form.errors.email" class="text-danger text-[10px] font-black uppercase mt-1">{{ form.errors.email }}</p>
      </div>

      <div class="space-y-2">
        <label for="password" class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] ml-1">Access Token</label>
        <div class="relative group">
          <input
            id="password"
            :type="showPassword ? 'text' : 'password'"
            v-model="form.password"
            required
            placeholder="••••••••••••"
            class="w-full px-4 pr-12 py-3.5 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all text-sm font-bold"
            :class="{ 'border-danger': form.errors.password }"
          />
          <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-muted hover:text-primary">
            <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>

      <div class="space-y-2">
        <label for="password_confirmation" class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] ml-1">Verify Token</label>
        <input
          id="password_confirmation"
          :type="showConfirmPassword ? 'text' : 'password'"
          v-model="form.password_confirmation"
          required
          placeholder="••••••••••••"
          class="w-full px-4 py-3.5 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all text-sm font-bold"
          :class="{ 'border-danger': form.errors.password_confirmation }"
        />
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="w-full py-4 bg-accent text-accent-text rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-accent-hover transition-all shadow-xl shadow-accent/20 active:scale-95 disabled:opacity-50"
      >
        Initialize Enrollment
      </button>

      <div class="relative py-4">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border/50"></div></div>
        <div class="relative flex justify-center text-[9px] font-black uppercase tracking-[0.3em] text-muted"><span class="px-4 bg-surface-raised">Identity Exists</span></div>
      </div>

      <Link
        :href="route('login')"
        class="block w-full py-4 text-center border border-border text-secondary rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-surface-overlay hover:text-primary transition-all"
      >
        Sign in to existing identity
      </Link>
    </form>
  </GuestLayout>
</template>