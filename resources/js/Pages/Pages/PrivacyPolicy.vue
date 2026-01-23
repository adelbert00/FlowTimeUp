<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head>
    <title>Privacy Policy - FlowTimeUp</title>
    <meta name="description" content="FlowTimeUp Privacy Policy - Learn how we protect your data and privacy." />
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
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
        <p class="text-sm text-gray-500 mb-8">Last updated: January 23, 2026</p>

        <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm space-y-6">
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Introduction</h2>
            <p class="text-gray-600 mb-4">
              FlowTimeUp ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains 
              how we collect, use, disclose, and safeguard your information when you use our time tracking application.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Information We Collect</h2>
            <h3 class="text-xl font-semibold text-gray-900 mb-2 mt-4">Personal Information</h3>
            <p class="text-gray-600 mb-4">
              We collect information that you provide directly to us, including:
            </p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
              <li>Name and email address</li>
              <li>Account credentials</li>
              <li>Profile information</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-900 mb-2 mt-4">Usage Data</h3>
            <p class="text-gray-600 mb-4">
              We automatically collect certain information when you use our service, including:
            </p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
              <li>Time tracking data (tasks, projects, time sessions)</li>
              <li>Device information</li>
              <li>Log data and usage patterns</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">How We Use Your Information</h2>
            <p class="text-gray-600 mb-4">We use the information we collect to:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
              <li>Provide, maintain, and improve our services</li>
              <li>Process your transactions and send related information</li>
              <li>Send technical notices and support messages</li>
              <li>Respond to your comments and questions</li>
              <li>Monitor and analyze usage patterns</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Data Security</h2>
            <p class="text-gray-600 mb-4">
              We implement appropriate technical and organizational measures to protect your personal information. 
              However, no method of transmission over the Internet is 100% secure.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your Rights</h2>
            <p class="text-gray-600 mb-4">You have the right to:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
              <li>Access your personal data</li>
              <li>Correct inaccurate data</li>
              <li>Request deletion of your data</li>
              <li>Export your data</li>
              <li>Object to processing of your data</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Contact Us</h2>
            <p class="text-gray-600 mb-4">
              If you have questions about this Privacy Policy, please contact us at 
              <Link href="/support" class="text-blue-600 hover:text-blue-700 underline">support</Link>.
            </p>
          </section>
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
