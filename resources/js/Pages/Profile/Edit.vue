<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';

defineProps<{
  mustVerifyEmail?: boolean;
  status?: string;
}>();

const page = usePage();
const user = page.props.auth?.user;
</script>

<template>
  <Head title="Profile Settings" />

  <MainLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
      <div class="container mx-auto px-4 sm:px-6 md:px-10 lg:px-8 py-6 sm:py-8 max-w-4xl pt-20 sm:pt-24 md:pt-28 xl:pt-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2">Profile Settings</h1>
          <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">Manage your account settings and preferences</p>
        </div>

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 sm:p-6 mb-4 sm:mb-6">
          <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white text-xl sm:text-2xl font-bold shadow-lg shadow-blue-500/25 flex-shrink-0">
              {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
            </div>
            <div class="text-center sm:text-left">
              <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">{{ user?.name }}</h2>
              <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base break-all">{{ user?.email }}</p>
              <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Member since {{ user?.created_at ? new Date(user.created_at).toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) : 'Unknown' }}</p>
            </div>
          </div>
        </div>

        <!-- Settings Sections -->
        <div class="space-y-4 sm:space-y-6">
          <!-- Profile Information -->
          <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile Information
              </h3>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">Update your account's profile information and email address.</p>
            </div>
            <div class="p-4 sm:p-6">
              <UpdateProfileInformationForm
                :must-verify-email="mustVerifyEmail"
                :status="status"
              />
            </div>
          </div>

          <!-- Update Password -->
          <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Update Password
              </h3>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">Ensure your account is using a long, random password to stay secure.</p>
            </div>
            <div class="p-4 sm:p-6">
              <UpdatePasswordForm />
            </div>
          </div>

          <!-- Delete Account -->
          <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-500/20 dark:border-red-500/30 overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-base sm:text-lg font-semibold text-red-400 dark:text-red-500 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Danger Zone
              </h3>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">Once you delete your account, there is no going back. Please be certain.</p>
            </div>
            <div class="p-4 sm:p-6">
              <DeleteUserForm />
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
