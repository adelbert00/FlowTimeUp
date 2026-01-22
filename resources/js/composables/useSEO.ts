import { useHead } from '@unhead/vue';
import { computed } from 'vue';

interface SEOOptions {
  title?: string;
  description?: string;
  image?: string;
  type?: string;
  canonical?: string;
}

export function useSEO(options: SEOOptions = {}) {
  const appName = 'FlowTimeUp';
  const siteUrl = import.meta.env.VITE_APP_URL || 'https://flowtimeup.com';
  
  const title = computed(() => {
    return options.title ? `${appName} - ${options.title}` : appName;
  });
  
  const description = computed(() => {
    return options.description || 
      'FlowTimeUp - Modern time tracking application. Track your time across tasks and projects with an intuitive interface. Boost productivity with precise time tracking and insightful reports.';
  });
  
  const image = computed(() => {
    return options.image || `${siteUrl}/images/og-image.png`;
  });
  
  const canonical = computed(() => {
    return options.canonical || window.location.href;
  });
  
  useHead({
    title: title.value,
    meta: [
      { name: 'description', content: description.value },
      { name: 'keywords', content: 'time tracking, task management, productivity, project management, time tracker, task tracker, time management, workflow' },
      { name: 'author', content: appName },
      { name: 'robots', content: 'index, follow' },
      
      // Open Graph
      { property: 'og:type', content: options.type || 'website' },
      { property: 'og:url', content: canonical.value },
      { property: 'og:title', content: title.value },
      { property: 'og:description', content: description.value },
      { property: 'og:image', content: image.value },
      { property: 'og:site_name', content: appName },
      
      // Twitter
      { name: 'twitter:card', content: 'summary_large_image' },
      { name: 'twitter:url', content: canonical.value },
      { name: 'twitter:title', content: title.value },
      { name: 'twitter:description', content: description.value },
      { name: 'twitter:image', content: image.value },
    ],
    link: [
      { rel: 'canonical', href: canonical.value },
    ],
    script: [
      {
        type: 'application/ld+json',
        children: JSON.stringify({
          '@context': 'https://schema.org',
          '@type': 'WebApplication',
          name: appName,
          description: description.value,
          url: siteUrl,
          applicationCategory: 'ProductivityApplication',
          operatingSystem: 'Web',
          offers: {
            '@type': 'Offer',
            price: '0',
            priceCurrency: 'USD',
          },
        }),
      },
    ],
  });
}
