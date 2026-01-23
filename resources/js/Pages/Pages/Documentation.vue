<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head>
    <title>Documentation - FlowTimeUp</title>
    <meta name="description" content="FlowTimeUp documentation - Learn how to use FlowTimeUp to track your time effectively." />
  </Head>

  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100">
    <!-- Navigation -->
    <nav class="border-b border-gray-200 bg-white/80 backdrop-blur-lg">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <Link href="/" class="flex items-center gap-2 sm:gap-3">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-lg sm:text-xl font-bold text-gray-900">FlowTimeUp</span>
          </Link>
          <Link href="/" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
            ← Back to Home
          </Link>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Documentation</h1>
        <p class="text-lg text-gray-600 mb-8">
          Welcome to the FlowTimeUp documentation. Learn how to get the most out of our time tracking application.
        </p>

        <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm">
          <h2 class="text-2xl font-semibold text-gray-900 mb-4">Getting Started</h2>
          <p class="text-gray-600 mb-6">
            FlowTimeUp is a modern time tracking application designed to help you track your time across tasks and projects. 
            Get started by creating an account and exploring the features.
          </p>

          <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">Features</h3>
          <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
            <li>Precise time tracking with centisecond accuracy</li>
            <li>Project and tag organization</li>
            <li>Task management with priorities and due dates</li>
            <li>Manual time entry for retroactive tracking</li>
            <li>Billable hours tracking with hourly rates</li>
            <li>Recurring tasks</li>
            <li>Calendar view</li>
            <li>Reports and CSV export</li>
            <li>Dark mode support</li>
          </ul>

          <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">Quick Start Guide</h3>
          <ol class="list-decimal list-inside text-gray-600 space-y-2 mb-6">
            <li>Create an account or sign in</li>
            <li>Create your first project</li>
            <li>Add tasks to your project</li>
            <li>Start tracking time on a task</li>
            <li>View your time reports and analytics</li>
          </ol>

          <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">Need Help?</h3>
          <p class="text-gray-600 mb-4">
            If you need additional assistance, please visit our <Link href="/help-center" class="text-blue-600 hover:text-blue-700 underline">Help Center</Link> 
            or <Link href="/support" class="text-blue-600 hover:text-blue-700 underline">contact support</Link>.
          </p>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white/80 backdrop-blur-lg mt-20">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-gray-600 text-sm">© 2026 FlowTimeUp. All rights reserved.</span>
          </div>
          <div class="flex items-center gap-4 text-sm text-gray-600">
            <Link href="/privacy-policy" class="hover:text-gray-900 transition-colors">Privacy</Link>
            <Link href="/terms-of-service" class="hover:text-gray-900 transition-colors">Terms</Link>
            <Link href="/support" class="hover:text-gray-900 transition-colors">Support</Link>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
