<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head>
    <title>GDPR Compliance - FlowTimeUp</title>
    <meta name="description" content="FlowTimeUp GDPR Compliance - Learn about our commitment to data protection." />
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
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">GDPR Compliance</h1>
        <p class="text-sm text-gray-500 mb-8">Last updated: January 23, 2026</p>

        <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 shadow-sm space-y-6">
          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Our Commitment</h2>
            <p class="text-gray-600 mb-4">
              FlowTimeUp is committed to compliance with the General Data Protection Regulation (GDPR). We take your 
              privacy seriously and have implemented measures to ensure your data is protected.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your Rights Under GDPR</h2>
            <p class="text-gray-600 mb-4">As a data subject, you have the following rights:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
              <li><strong>Right of Access:</strong> You can request access to your personal data</li>
              <li><strong>Right to Rectification:</strong> You can request correction of inaccurate data</li>
              <li><strong>Right to Erasure:</strong> You can request deletion of your data</li>
              <li><strong>Right to Restrict Processing:</strong> You can request limitation of data processing</li>
              <li><strong>Right to Data Portability:</strong> You can request your data in a portable format</li>
              <li><strong>Right to Object:</strong> You can object to processing of your data</li>
              <li><strong>Right to Withdraw Consent:</strong> You can withdraw consent at any time</li>
            </ul>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Data Processing</h2>
            <p class="text-gray-600 mb-4">
              We process your personal data only for the purposes stated in our 
              <Link href="/privacy-policy" class="text-blue-600 hover:text-blue-700 underline">Privacy Policy</Link>. 
              We do not sell your data to third parties.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Data Security</h2>
            <p class="text-gray-600 mb-4">
              We implement appropriate technical and organizational measures to ensure a level of security appropriate 
              to the risk, including encryption, access controls, and regular security assessments.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Data Breach Notification</h2>
            <p class="text-gray-600 mb-4">
              In the event of a data breach that may affect your rights and freedoms, we will notify you and the 
              relevant supervisory authority without undue delay.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Exercising Your Rights</h2>
            <p class="text-gray-600 mb-4">
              To exercise any of your GDPR rights, please contact us at 
              <Link href="/support" class="text-blue-600 hover:text-blue-700 underline">support</Link>. 
              We will respond to your request within one month.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Supervisory Authority</h2>
            <p class="text-gray-600 mb-4">
              If you believe we have not addressed your concerns, you have the right to lodge a complaint with your 
              local data protection supervisory authority.
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
