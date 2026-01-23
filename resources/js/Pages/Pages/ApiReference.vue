<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head>
    <title>API Reference - FlowTimeUp</title>
    <meta name="description" content="FlowTimeUp API Reference - Integrate FlowTimeUp with your applications." />
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
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">API Reference</h1>
        <p class="text-lg text-gray-600 mb-8">
          Integrate FlowTimeUp with your applications using our RESTful API.
        </p>

        <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm">
          <h2 class="text-2xl font-semibold text-gray-900 mb-4">Overview</h2>
          <p class="text-gray-600 mb-6">
            The FlowTimeUp API allows you to programmatically access and manage your time tracking data. 
            The API follows RESTful principles and returns JSON responses.
          </p>

          <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">Authentication</h3>
          <p class="text-gray-600 mb-4">
            API authentication is currently under development. Please check back soon for API access.
          </p>

          <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">Endpoints</h3>
          <p class="text-gray-600 mb-4">
            API endpoints will be available for:
          </p>
          <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
            <li>Projects management</li>
            <li>Tasks CRUD operations</li>
            <li>Time sessions tracking</li>
            <li>Tags management</li>
            <li>Reports generation</li>
          </ul>

          <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">Rate Limits</h3>
          <p class="text-gray-600 mb-4">
            Rate limiting details will be provided when the API becomes available.
          </p>

          <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">Status</h3>
          <p class="text-gray-600 mb-4">
            The FlowTimeUp API is currently in development. For updates, please visit our 
            <Link href="/documentation" class="text-blue-600 hover:text-blue-700 underline">Documentation</Link> page.
          </p>
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
