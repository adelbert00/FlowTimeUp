import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import pinia from './stores/store';

const appName = import.meta.env.VITE_APP_NAME || 'FlowTimeUp';

declare global {
  interface Window {
    gtag?: (...args: any[]) => void;
    dataLayer?: any[];
    VITE_GOOGLE_ANALYTICS_ID?: string;
  }
}

router.on('navigate', () => {
  if (window.gtag && window.VITE_GOOGLE_ANALYTICS_ID) {
    window.gtag('config', window.VITE_GOOGLE_ANALYTICS_ID, {
      page_path: window.location.pathname,
    });
  }
});

createInertiaApp({
  title: (title) => title ? `FlowTimeUp - ${title}` : appName,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./Pages/**/*.vue')
    ),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(pinia)
      .mount(el);
  },
  progress: {
    color: '#2563eb',
    showSpinner: true,
  },
});
