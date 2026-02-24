<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <x-seo-head />
        
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,600&family=jetbrains-mono:400&display=swap" as="style" media="(max-width: 768px)" onload="this.media='all';this.onload=null;this.rel='stylesheet'">
        <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=jetbrains-mono:400,500,600,700&display=swap" as="style" media="(min-width: 769px)" onload="this.media='all';this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=jetbrains-mono:400,500,600,700&display=swap" rel="stylesheet"></noscript>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2'><circle cx='12' cy='12' r='10'/><path d='M12 6v6l4 2'/></svg>">

        @if(config('services.google.analytics_id'))
        <script>
            window.VITE_GOOGLE_ANALYTICS_ID = '{{ config('services.google.analytics_id') }}';
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            window.gtag = gtag;
            function loadGA() {
                var s = document.createElement('script');
                s.async = true;
                s.src = 'https://www.googletagmanager.com/gtag/js?id=' + window.VITE_GOOGLE_ANALYTICS_ID;
                document.head.appendChild(s);
                gtag('js', new Date());
                gtag('config', window.VITE_GOOGLE_ANALYTICS_ID, { page_path: window.location.pathname });
            }
            if ('requestIdleCallback' in window) {
                requestIdleCallback(loadGA, { timeout: 2000 });
            } else {
                setTimeout(loadGA, 1500);
            }
        </script>
        @endif

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
        
        <!-- Hide default reCAPTCHA badge -->
        <style>
            .grecaptcha-badge {
                visibility: hidden !important;
                opacity: 0 !important;
                position: absolute !important;
                left: -9999px !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        @inertia
    </body>
</html>
