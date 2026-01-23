<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head>
    <title>Help Center - FlowTimeUp</title>
    <meta name="description" content="FlowTimeUp Help Center - Find answers to common questions and get support." />
  </Head>

  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-gray-100">
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

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Help Center</h1>
        <p class="text-lg text-gray-600 mb-8">
          Find answers to frequently asked questions and get help with FlowTimeUp.
        </p>

        <div class="space-y-6">
          <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Frequently Asked Questions</h2>
            
            <div class="space-y-6">
              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">How do I start tracking time?</h3>
                <p class="text-gray-600">
                  Create a task, then click the start button to begin tracking time. The timer will run until you stop it.
                </p>
              </div>

              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Can I edit time entries?</h3>
                <p class="text-gray-600">
                  Yes, you can manually add or edit time entries. Use the manual time entry feature to add time retroactively.
                </p>
              </div>

              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">How do I export my data?</h3>
                <p class="text-gray-600">
                  Go to the Reports section and use the export feature to download your time data as CSV.
                </p>
              </div>

              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Is FlowTimeUp free?</h3>
                <p class="text-gray-600">
                  Yes, FlowTimeUp is completely free to use with unlimited projects and tasks.
                </p>
              </div>

              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">How do recurring tasks work?</h3>
                <p class="text-gray-600">
                  Recurring tasks automatically create new instances based on your selected frequency (daily, weekly, or monthly).
                </p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Still Need Help?</h2>
            <p class="text-gray-600 mb-4">
              If you can't find what you're looking for, please visit our 
              <Link href="/documentation" class="text-blue-600 hover:text-blue-700 underline">Documentation</Link> 
              or <Link href="/support" class="text-blue-600 hover:text-blue-700 underline">contact support</Link>.
            </p>
          </div>
        </div>
      </div>
    </main>

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
