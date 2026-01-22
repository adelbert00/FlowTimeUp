@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
])

@php
    $appName = config('app.name', 'FlowTimeUp');
    $siteUrl = config('app.url');
    // Default to just app name, Inertia will handle dynamic titles
    $pageTitle = $title ? "{$appName} - {$title}" : $appName;
    $pageDescription = $description ?? 'FlowTimeUp - Modern time tracking application. Track your time across tasks and projects with an intuitive interface. Boost productivity with precise time tracking and insightful reports.';
    $pageImage = $image ?? asset('images/og-image.png');
    $canonicalUrl = $canonical ?? url()->current();
@endphp

<!-- Primary Meta Tags -->
<!-- Title will be managed by Inertia.js and Timer Store -->
<title>{{ $appName }}</title>
<meta name="title" content="{{ $pageTitle }}">
<meta name="description" content="{{ $pageDescription }}">
<meta name="keywords" content="time tracking, task management, productivity, project management, time tracker, task tracker, time management, workflow">
<meta name="author" content="{{ $appName }}">
<meta name="robots" content="index, follow">
<meta name="language" content="{{ str_replace('_', '-', app()->getLocale()) }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:image" content="{{ $pageImage }}">
<meta property="og:site_name" content="{{ $appName }}">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $pageImage }}">

<!-- Additional SEO -->
<meta name="theme-color" content="#2563eb">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="{{ $appName }}">

<!-- Structured Data (JSON-LD) -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "{{ $appName }}",
    "description": "{{ $pageDescription }}",
    "url": "{{ $siteUrl }}",
    "applicationCategory": "ProductivityApplication",
    "operatingSystem": "Web",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "ratingCount": "1"
    }
}
</script>
