<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
  canResetPassword?: boolean;
  status?: string;
}>();

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);

const submit = () => {
  form.post(route('login'), {
    onFinish: () => {
      form.reset('password');
    },
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Sign in" />

    <div class="text-center mb-10">
      <h1 class="text-2xl font-black text-primary uppercase tracking-tighter">Identity Verification</h1>
      <p class="text-secondary mt-2 text-[10px] font-bold uppercase tracking-[0.2em]">Secure Access Point</p>
    </div>

    <div v-if="status" class="mb-8 p-4 bg-accent/10 border border-accent/20 rounded-xl">
      <p class="text-xs font-bold text-accent uppercase tracking-widest text-center">{{ status }}</p>
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <div class="space-y-2">
        <label for="email" class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] ml-1">
          Access ID (Email)
        </label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-accent transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
          </div>
          <input
            id="email"
            type="email"
            v-model="form.email"
            required
            autofocus
            placeholder="system_user@v2.core"
            class="w-full pl-11 pr-4 py-3.5 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all text-sm font-bold"
            :class="{ 'border-danger': form.errors.email }"
          />
        </div>
        <p v-if="form.errors.email" class="text-danger text-[10px] font-black uppercase tracking-wider mt-1 ml-1">
          {{ form.errors.email }}
        </p>
      </div>

      <div class="space-y-2">
        <label for="password" class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] ml-1">
          Access Token (Password)
        </label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-accent transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          </div>
          <input
            id="password"
            :type="showPassword ? 'text' : 'password'"
            v-model="form.password"
            required
            placeholder="••••••••••••"
            class="w-full pl-11 pr-12 py-3.5 bg-surface-overlay border border-border rounded-xl text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-accent transition-all text-sm font-bold"
            :class="{ 'border-danger': form.errors.password }"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 pr-4 flex items-center text-muted hover:text-primary transition-colors"
          >
            <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
        <p v-if="form.errors.password" class="text-danger text-[10px] font-black uppercase tracking-wider mt-1 ml-1">
          {{ form.errors.password }}
        </p>
      </div>

      <div class="flex items-center justify-between px-1">
        <label class="flex items-center gap-2.5 cursor-pointer group">
          <div class="relative flex items-center">
            <input type="checkbox" v-model="form.remember" class="peer sr-only" />
            <div class="w-8 h-4 bg-border rounded-full transition-colors peer-checked:bg-accent"></div>
            <div class="absolute left-1 top-1 w-2 h-2 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
          </div>
          <span class="text-[10px] font-black text-secondary uppercase tracking-widest group-hover:text-primary">Keep session</span>
        </label>

        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-[10px] font-black text-accent hover:text-accent-hover uppercase tracking-widest transition-colors"
        >
          Recover Access
        </Link>
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="w-full py-4 bg-accent text-accent-text rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-accent-hover transition-all shadow-xl shadow-accent/20 active:scale-95 disabled:opacity-50"
      >
        <span v-if="form.processing" class="flex items-center justify-center gap-3">
          <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
          Authorizing...
        </span>
        <span v-else>Initialize Access</span>
      </button>

      <div class="relative py-4">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border/50"></div></div>
        <div class="relative flex justify-center text-[9px] font-black uppercase tracking-[0.3em] text-muted"><span class="px-4 bg-surface-raised">Alternative</span></div>
      </div>

      <Link
        :href="route('register')"
        class="block w-full py-4 text-center border border-border text-secondary rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-surface-overlay hover:text-primary transition-all"
      >
        Create New Identity
      </Link>
    </form>
  </GuestLayout>
</template>