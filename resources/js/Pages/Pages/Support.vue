<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head>
    <title>Support - FlowTimeUp</title>
    <meta name="description" content="FlowTimeUp Support - Get help and contact our support team." />
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
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Support</h1>
        <p class="text-lg text-gray-600 mb-8">
          We're here to help! Get support for FlowTimeUp.
        </p>

        <div class="space-y-6">
          <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Get Help</h2>
            <p class="text-gray-600 mb-6">
              Before contacting support, check out these resources:
            </p>
            <ul class="space-y-3 mb-6">
              <li>
                <Link href="/documentation" class="text-blue-600 hover:text-blue-700 underline flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                  Documentation
                </Link>
              </li>
              <li>
                <Link href="/help-center" class="text-blue-600 hover:text-blue-700 underline flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Help Center
                </Link>
              </li>
              <li>
                <Link href="/api-reference" class="text-blue-600 hover:text-blue-700 underline flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                  </svg>
                  API Reference
                </Link>
              </li>
            </ul>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Contact Support</h2>
            <p class="text-gray-600 mb-4">
              If you need additional assistance, please reach out to us:
            </p>
            <div class="space-y-4">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <div>
                  <p class="font-semibold text-gray-900">Email</p>
                  <p class="text-gray-600">support@flowtimeup.com</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                  <p class="font-semibold text-gray-900">Response Time</p>
                  <p class="text-gray-600">We typically respond within 24-48 hours</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-blue-50 rounded-xl border border-blue-200 p-6 sm:p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Report a Bug</h2>
            <p class="text-gray-600 mb-4">
              Found a bug? Please include as much detail as possible, including steps to reproduce the issue.
            </p>
            <p class="text-gray-600">
              Email us at <a href="mailto:support@flowtimeup.com" class="text-blue-600 hover:text-blue-700 underline">support@flowtimeup.com</a>
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
