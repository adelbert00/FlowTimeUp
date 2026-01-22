<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- SEO Meta Tags -->
        <x-seo-head />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2'><circle cx='12' cy='12' r='10'/><path d='M12 6v6l4 2'/></svg>">

        <!-- Google Analytics -->
        @if(config('services.google.analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.google.analytics_id') }}', {
                page_path: window.location.pathname,
            });
            
            // Make gtag available globally for Inertia tracking
            window.gtag = gtag;
        </script>
        <script>
            // Set GA ID for client-side tracking
            window.VITE_GOOGLE_ANALYTICS_ID = '{{ config('services.google.analytics_id') }}';
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
    <body class="font-sans antialiased bg-slate-900">
        @inertia
    </body>
</html>
