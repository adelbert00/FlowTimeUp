<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head>
    <title>Cookie Policy - FlowTimeUp</title>
    <meta name="description" content="FlowTimeUp Cookie Policy - Learn about how we use cookies." />
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
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Cookie Policy</h1>
        <p class="text-sm text-gray-500 mb-8">Last updated: January 23, 2026</p>

        <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm space-y-6">
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">What Are Cookies</h2>
            <p class="text-gray-600 mb-4">
              Cookies are small text files that are placed on your device when you visit a website. They are widely 
              used to make websites work more efficiently and provide information to website owners.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">How We Use Cookies</h2>
            <p class="text-gray-600 mb-4">FlowTimeUp uses cookies for the following purposes:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
              <li><strong>Essential Cookies:</strong> Required for the website to function properly, including authentication and security</li>
              <li><strong>Functional Cookies:</strong> Remember your preferences and settings</li>
              <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Third-Party Cookies</h2>
            <p class="text-gray-600 mb-4">
              We may use third-party services that set cookies on your device. These services help us analyze website 
              usage and improve our services. We do not control these third-party cookies.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Managing Cookies</h2>
            <p class="text-gray-600 mb-4">
              You can control and manage cookies in your browser settings. However, disabling certain cookies may 
              affect the functionality of FlowTimeUp.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Contact Us</h2>
            <p class="text-gray-600 mb-4">
              If you have questions about our Cookie Policy, please contact us at 
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
