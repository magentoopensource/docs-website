<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ isset($title) ? $title . ' - ' : null }}Magento 2 Merchant Documentation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    @if (isset($canonical))
    <link rel="canonical" href="{{ url($canonical) }}">
    @endif

    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $metaTitle }}">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">

    <!-- Algolia Site Verification -->
    <meta name="algolia-site-verification" content="2D7B67D7596729A9" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical ? url($canonical) : url('/') }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ url('/img/merchant-docs-og.png') }}">

    <!-- Twitter / X -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $canonical ? url($canonical) : url('/') }}">
    <meta property="twitter:title" content="{{ $metaTitle }}">
    <meta property="twitter:description" content="{{ $metaDescription }}">
    <meta property="twitter:image" content="{{ url('/img/merchant-docs-og.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
    <link rel="manifest" href="/img/favicon/site.webmanifest">
    <meta name="msapplication-TileColor" content="#FF6700">
    <meta name="msapplication-config" content="/img/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <meta name="color-scheme" content="light">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5DBRTJMQ');</script>
    <!-- End Google Tag Manager -->

    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://{{ config('algolia.connections.main.id') }}-dsn.algolia.net" crossorigin />
    <link rel="preconnect" href="https://www.googletagmanager.com">

    <!-- DNS prefetch as fallback for older browsers -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://{{ config('algolia.connections.main.id') }}-dsn.algolia.net">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://www.google-analytics.com">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        const alwaysLightMode = true;
    </script>

    @include('partials.theme')
</head>
<body
    x-data="{
        navIsOpen: false,
        closeNav() { this.navIsOpen = false; },
        toggleNav() { this.navIsOpen = !this.navIsOpen; },
    }"
    class="w-full h-full font-sans antialiased text-gray-900 language-php bg-white"
>

{{-- Fixed Header from Figma --}}
@include('partials.header')

{{-- Main content with top padding to accommodate fixed header and max-width constraint --}}
<main class="">
    <div class="max-w-[1440px] mx-auto">
        @yield('content')
    </div>
</main>

@include('partials.footer')

{{-- Hidden DocSearch container for Algolia integration --}}
<div
    id="docsearch"
    style="display: none;"
    data-algolia-app-id="{{ config('algolia.connections.main.id', '') }}"
    data-algolia-search-key="{{ config('algolia.connections.main.search_key', '') }}"
    data-algolia-index-name="{{ config('algolia.connections.main.index_name', 'devmage-os') }}"
    data-version="main"
></div>

</body>
</html>
