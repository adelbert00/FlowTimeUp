import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.ts',
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],

  // Production build optimizations
  build: {
    // Enable minification
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true, // Remove console.log in production
        drop_debugger: true,
      },
    },

    // Code splitting configuration
    rollupOptions: {
      output: {
        // Manual chunk splitting for better caching
        manualChunks: {
          // Vue core
          'vue-vendor': ['vue', '@inertiajs/vue3'],
          // UI libraries
          'ui-vendor': ['radix-vue', 'lucide-vue-next', 'class-variance-authority', 'clsx', 'tailwind-merge'],
          // Utilities
          'utils-vendor': ['dayjs', '@vueuse/core', 'pinia'],
        },
        // Better chunk naming for caching
        chunkFileNames: 'assets/js/[name]-[hash].js',
        entryFileNames: 'assets/js/[name]-[hash].js',
        assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
      },
    },

    // Target modern browsers for smaller bundle
    target: 'es2020',

    // Chunk size warning limit
    chunkSizeWarningLimit: 1000,

    // Enable source maps for production debugging (optional, can be disabled)
    sourcemap: false,
  },

  // Optimize dependency pre-bundling
  optimizeDeps: {
    include: [
      'vue',
      '@inertiajs/vue3',
      'pinia',
      'dayjs',
      '@vueuse/core',
      'radix-vue',
      'lucide-vue-next',
    ],
  },

  // CSS optimization
  css: {
    devSourcemap: true,
  },
});
