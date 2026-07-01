<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Magento Documentation — Magento Association</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="description" content="Official Magento documentation hub. Merchant guides for store owners and developer documentation for engineers building on Magento 2.">

    <link rel="canonical" href="{{ url('/') }}">

    <!-- Primary Meta Tags -->
    <meta name="title" content="Magento Documentation — Magento Association">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Magento Documentation — Magento Association">
    <meta property="og:description" content="Official Magento documentation hub. Merchant guides for store owners and developer documentation for engineers building on Magento 2.">
    <meta property="og:image" content="{{ url('/img/merchant-docs-og.png') }}">

    <!-- Twitter / X -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="Magento Documentation — Magento Association">
    <meta property="twitter:description" content="Official Magento documentation hub. Merchant guides for store owners and developer documentation for engineers building on Magento 2.">
    <meta property="twitter:image" content="{{ url('/img/merchant-docs-og.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
    <link rel="manifest" href="/img/favicon/site.webmanifest">
    <meta name="msapplication-TileColor" content="#FF6700">
    <meta name="theme-color" content="#ffffff">
    <meta name="color-scheme" content="light">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5DBRTJMQ');</script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .hex-clip {
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        }
        .doc-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .doc-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
    </style>
</head>

<body class="font-sans bg-off-white text-charcoal antialiased">

    {{-- Ecosystem Bar --}}
    @include('partials.ecosystem-menu')

    {{-- Minimal Header --}}
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="inline-flex items-center no-underline flex-shrink-0">
                    <svg width="30" height="33" viewBox="0 0 30 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 4.06492H29.6763V31.8882C29.6763 32.502 29.1713 33 28.5487 33H1.12762C0.505079 33 0 32.502 0 31.8882V4.06492Z" fill="#34323A"/>
                        <path d="M1.26857 0H28.4078C29.1066 0 29.6763 0.561678 29.6763 1.25075V4.06492H0V1.25075C0 0.561678 0.569682 0 1.26857 0Z" fill="#C9C9C9"/>
                        <path d="M2.37269 3.0458C2.94031 3.0458 3.40046 2.59211 3.40046 2.03246C3.40046 1.47281 2.94031 1.01913 2.37269 1.01913C1.80506 1.01913 1.34491 1.47281 1.34491 2.03246C1.34491 2.59211 1.80506 3.0458 2.37269 3.0458Z" fill="#848484"/>
                        <path d="M5.28571 3.0458C5.85334 3.0458 6.31349 2.59211 6.31349 2.03246C6.31349 1.47281 5.85334 1.01913 5.28571 1.01913C4.71809 1.01913 4.25793 1.47281 4.25793 2.03246C4.25793 2.59211 4.71809 3.0458 5.28571 3.0458Z" fill="#848484"/>
                        <path d="M14.7883 7.46973L4.90405 13.0923V24.349L7.54104 25.8487V14.5978L14.7883 10.4692L22.0415 14.5978V25.8487L24.6785 24.349V13.0923L14.7883 7.46973Z" fill="#F1BC1B"/>
                        <path d="M16.0862 26.2367L14.7883 26.9779L13.4492 26.2135V14.233L10.178 16.0975V27.3485L13.4492 29.213L14.7883 29.9773L16.0862 29.2362L19.4045 27.3485V16.0975L16.0862 14.2098V26.2367Z" fill="#F1BC1B"/>
                    </svg>
                    <span class="ml-3 text-xl font-bold text-charcoal">Documentation</span>
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a href="https://www.magentoassociation.org/home" target="_blank" rel="noopener" class="text-sm font-medium text-charcoal-300 hover:text-orange transition-colors no-underline">Magento Association</a>
                    <a href="https://github.com/magento/magento2" target="_blank" rel="noopener" class="text-sm font-medium text-charcoal-300 hover:text-orange transition-colors no-underline">GitHub</a>
                    <a href="https://community.magento.com/" target="_blank" rel="noopener" class="text-sm font-medium text-charcoal-300 hover:text-orange transition-colors no-underline">Community</a>
                </nav>
            </div>
        </div>
    </header>

    {{-- ================================================================ --}}
    {{-- HERO — Bauhaus: 3 stacked hex rings, deliberate overlap          --}}
    {{-- ================================================================ --}}
    <section class="relative bg-white overflow-hidden">
        <div class="relative min-h-[420px] sm:min-h-[500px] lg:min-h-[560px] flex flex-col items-center justify-center">

            <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" class="w-auto min-h-[140%] opacity-[0.40]" preserveAspectRatio="xMidYMid meet">
                    <path fill-rule="evenodd" fill="#F1BC1B" d="M400,-30 L694.4,140 L694.4,480 L400,650 L105.6,480 L105.6,140 Z M400,120 L564.5,215 L564.5,405 L400,500 L235.5,405 L235.5,215 Z"/>
                    <path fill-rule="evenodd" fill="#F26423" d="M400,99 L656.3,247 L656.3,543 L400,691 L143.7,543 L143.7,247 Z M400,249 L526.4,322 L526.4,468 L400,541 L273.6,468 L273.6,322 Z"/>
                    <path fill-rule="evenodd" fill="#2C2C2C" d="M400,228 L618.2,354 L618.2,606 L400,732 L181.8,606 L181.8,354 Z M400,338 L522.9,409 L522.9,551 L400,622 L277.1,551 L277.1,409 Z"/>
                </svg>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-1.5" style="background: linear-gradient(to right, #F1BC1B, #F26423, #2C2C2C);"></div>

            <div class="relative z-10 max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24 text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold tracking-tight text-charcoal mb-6 leading-[1.05]">
                    Magento<br class="sm:hidden"> Documentation
                </h1>
                <p class="text-lg sm:text-xl text-charcoal-300 leading-relaxed max-w-2xl mx-auto mb-8">
                    Built by the community, for the community. Choose your path.
                </p>
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-charcoal text-sm text-gray-300">
                    <svg class="w-3.5 h-3.5 text-orange" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                    Magento Open Source 2.4.8
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- DOCUMENTATION CARDS                                               --}}
    {{-- ================================================================ --}}
    <section class="relative pt-16 pb-16 sm:pt-20 sm:pb-24 bg-off-white">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-6 lg:gap-8 max-w-5xl mx-auto">

                {{-- Merchant Documentation Card --}}
                <a href="/merchant" class="doc-card group block bg-white border-2 border-gray-200 hover:border-gold-600 no-underline relative overflow-hidden">
                    <div class="h-1.5 bg-gold"></div>
                    <div class="p-8 sm:p-10">
                        <div class="w-16 h-16 mb-6 relative">
                            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                <polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#F1BC1B"/>
                                <polygon points="32,10 52,21 52,43 32,54 12,43 12,21" fill="#FFFFFF"/>
                                <g transform="translate(20,20)" fill="#2C2C2C">
                                    <rect x="2" y="8" width="20" height="14" rx="1" fill="none" stroke="#2C2C2C" stroke-width="1.5"/>
                                    <path d="M0 8l4-6h16l4 6" fill="none" stroke="#2C2C2C" stroke-width="1.5" stroke-linejoin="round"/>
                                    <line x1="12" y1="8" x2="12" y2="2" stroke="#2C2C2C" stroke-width="1.5"/>
                                    <circle cx="8" cy="14" r="1.5" fill="#F1BC1B"/>
                                    <circle cx="16" cy="14" r="1.5" fill="#F1BC1B"/>
                                </g>
                            </svg>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-charcoal mb-3 group-hover:text-charcoal-400 transition-colors">
                            Merchant Documentation
                        </h2>
                        <p class="text-charcoal-300 leading-relaxed mb-6">
                            Store setup, product management, order processing, customer accounts, and day-to-day operations for store owners and administrators.
                        </p>
                        <ul class="space-y-2.5 mb-8">
                            <li class="flex items-center gap-3 text-sm text-charcoal-400">
                                <svg class="w-4 h-4 text-charcoal-300 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                                Getting Started Guides
                            </li>
                            <li class="flex items-center gap-3 text-sm text-charcoal-400">
                                <svg class="w-4 h-4 text-charcoal-300 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                                Catalog &amp; Product Management
                            </li>
                            <li class="flex items-center gap-3 text-sm text-charcoal-400">
                                <svg class="w-4 h-4 text-charcoal-300 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                                Sales &amp; Order Processing
                            </li>
                            <li class="flex items-center gap-3 text-sm text-charcoal-400">
                                <svg class="w-4 h-4 text-charcoal-300 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                                Store Configuration
                            </li>
                        </ul>
                        <div class="flex items-center gap-2 text-charcoal font-bold text-sm group-hover:gap-3 transition-all">
                            Explore Merchant Docs
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </div>
                    </div>
                    <div class="absolute -bottom-8 -right-8 w-40 h-40 opacity-[0.04] pointer-events-none" aria-hidden="true">
                        <svg viewBox="0 0 64 64"><polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#F1BC1B"/></svg>
                    </div>
                </a>

                {{-- Developer Documentation Card --}}
                <a href="/developer" class="doc-card group block bg-white border-2 border-gray-200 hover:border-orange no-underline relative overflow-hidden">
                    <div class="h-1.5 bg-orange"></div>
                    <div class="p-8 sm:p-10">
                        <div class="w-16 h-16 mb-6 relative">
                            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                <polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#F26423"/>
                                <polygon points="32,10 52,21 52,43 32,54 12,43 12,21" fill="#FFFFFF"/>
                                <g transform="translate(19,20)" fill="none" stroke="#2C2C2C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="4,6 10,12 4,18"/>
                                    <line x1="14" y1="18" x2="22" y2="18"/>
                                </g>
                            </svg>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-charcoal mb-3 group-hover:text-orange-600 transition-colors">
                            Developer Documentation
                        </h2>
                        <p class="text-charcoal-300 leading-relaxed mb-6">
                            Tutorials, architecture deep dives, module references, and production-ready patterns for engineers building on Magento 2.4.7+.
                        </p>
                        <ul class="space-y-2.5 mb-8">
                            <li class="flex items-center gap-3 text-sm text-charcoal-400">
                                <svg class="w-4 h-4 text-orange flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                                Tutorials &amp; How-To Guides
                            </li>
                            <li class="flex items-center gap-3 text-sm text-charcoal-400">
                                <svg class="w-4 h-4 text-orange flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                                Architecture Deep Dives
                            </li>
                            <li class="flex items-center gap-3 text-sm text-charcoal-400">
                                <svg class="w-4 h-4 text-orange flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                                Module References (5 modules)
                            </li>
                            <li class="flex items-center gap-3 text-sm text-charcoal-400">
                                <svg class="w-4 h-4 text-orange flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 22,8 22,16 12,22 2,16 2,8"/></svg>
                                Learning Paths &amp; Certifications
                            </li>
                        </ul>
                        <div class="flex items-center gap-2 text-orange font-bold text-sm group-hover:gap-3 transition-all">
                            Explore Developer Docs
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </div>
                    </div>
                    <div class="absolute -bottom-8 -right-8 w-40 h-40 opacity-[0.04] pointer-events-none" aria-hidden="true">
                        <svg viewBox="0 0 64 64"><polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#F26423"/></svg>
                    </div>
                </a>

            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- STATS BAR                                                         --}}
    {{-- ================================================================ --}}
    <section class="bg-charcoal border-t-4 border-gold">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <span class="block text-4xl sm:text-5xl font-extrabold text-white">90+</span>
                    <span class="text-sm text-gray-400 uppercase tracking-wider font-medium mt-1 block">Pages of Docs</span>
                </div>
                <div>
                    <span class="block text-4xl sm:text-5xl font-extrabold text-orange">5</span>
                    <span class="text-sm text-gray-400 uppercase tracking-wider font-medium mt-1 block">Core Modules</span>
                </div>
                <div>
                    <span class="block text-4xl sm:text-5xl font-extrabold text-white">9</span>
                    <span class="text-sm text-gray-400 uppercase tracking-wider font-medium mt-1 block">Doc Types Per Module</span>
                </div>
                <div>
                    <span class="block text-4xl sm:text-5xl font-extrabold text-orange">5</span>
                    <span class="text-sm text-gray-400 uppercase tracking-wider font-medium mt-1 block">Learning Paths</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- QUICK LINKS GRID                                                  --}}
    {{-- ================================================================ --}}
    <section class="py-16 sm:py-20 bg-off-white">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-charcoal mb-4">Popular Starting Points</h2>
                <p class="text-charcoal-300 text-lg max-w-2xl mx-auto">Jump straight into what you need.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">

                <a href="/developer/guide-tutorial-docker-development-environment.html" class="group flex items-start gap-4 p-6 bg-white border-2 border-gray-200 hover:border-orange hover:shadow-md transition-all no-underline">
                    <div class="w-10 h-10 flex-shrink-0 hex-clip bg-orange flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-charcoal group-hover:text-orange transition-colors mb-1">Docker Dev Environment</h3>
                        <p class="text-sm text-charcoal-300">Set up a full Magento dev stack in Docker</p>
                    </div>
                </a>

                <a href="/developer/guide-tutorial-plugin-system-deep-dive.html" class="group flex items-start gap-4 p-6 bg-white border-2 border-gray-200 hover:border-orange hover:shadow-md transition-all no-underline">
                    <div class="w-10 h-10 flex-shrink-0 hex-clip bg-charcoal flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-charcoal group-hover:text-orange transition-colors mb-1">Plugin System Deep Dive</h3>
                        <p class="text-sm text-charcoal-300">Before/after/around interceptors explained</p>
                    </div>
                </a>

                <a href="/developer/module-catalog.html" class="group flex items-start gap-4 p-6 bg-white border-2 border-gray-200 hover:border-orange hover:shadow-md transition-all no-underline">
                    <div class="w-10 h-10 flex-shrink-0 hex-clip bg-gold flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-charcoal group-hover:text-orange transition-colors mb-1">Catalog Module</h3>
                        <p class="text-sm text-charcoal-300">Architecture, plugins, execution flows</p>
                    </div>
                </a>

                <a href="/developer/guide-explanation-eav-system.html" class="group flex items-start gap-4 p-6 bg-white border-2 border-gray-200 hover:border-charcoal hover:shadow-md transition-all no-underline">
                    <div class="w-10 h-10 flex-shrink-0 hex-clip bg-charcoal flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-charcoal group-hover:text-charcoal-400 transition-colors mb-1">EAV System Architecture</h3>
                        <p class="text-sm text-charcoal-300">How Magento stores entity attributes</p>
                    </div>
                </a>

                <a href="/developer/learning-paths.html" class="group flex items-start gap-4 p-6 bg-white border-2 border-gray-200 hover:border-orange hover:shadow-md transition-all no-underline">
                    <div class="w-10 h-10 flex-shrink-0 hex-clip bg-orange flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-charcoal group-hover:text-orange transition-colors mb-1">Learning Paths</h3>
                        <p class="text-sm text-charcoal-300">Guided progression from beginner to expert</p>
                    </div>
                </a>

                <a href="/developer/certifications.html" class="group flex items-start gap-4 p-6 bg-white border-2 border-gray-200 hover:border-charcoal hover:shadow-md transition-all no-underline">
                    <div class="w-10 h-10 flex-shrink-0 hex-clip bg-gold flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-charcoal group-hover:text-charcoal-400 transition-colors mb-1">Certifications</h3>
                        <p class="text-sm text-charcoal-300">Study guides for all certification tracks</p>
                    </div>
                </a>

            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- COMMUNITY CALLOUT                                                 --}}
    {{-- ================================================================ --}}
    <section class="bg-white border-t border-gray-200">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="max-w-3xl mx-auto text-center">
                <div class="flex justify-center mb-6">
                    <div class="flex items-center gap-1" aria-hidden="true">
                        <svg class="w-6 h-6" viewBox="0 0 64 64"><polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#2C2C2C"/></svg>
                        <svg class="w-8 h-8" viewBox="0 0 64 64"><polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#F26423"/></svg>
                        <svg class="w-10 h-10" viewBox="0 0 64 64"><polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#F1BC1B"/></svg>
                        <svg class="w-8 h-8" viewBox="0 0 64 64"><polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#F26423"/></svg>
                        <svg class="w-6 h-6" viewBox="0 0 64 64"><polygon points="32,2 60,17 60,47 32,62 4,47 4,17" fill="#2C2C2C"/></svg>
                    </div>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-charcoal mb-4">Built by the Community</h2>
                <p class="text-lg text-charcoal-300 leading-relaxed mb-8">
                    This documentation is a Magento Association initiative. Every page is generated from real Magento source code, reviewed by domain experts, and continuously improved. Want to contribute? We need reviewers and module specialists.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="https://www.magentoassociation.org/home" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 bg-charcoal text-white font-semibold hover:bg-charcoal-600 transition-colors no-underline text-sm">
                        Join the Magento Association
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <a href="https://github.com/magento/magento2" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-charcoal font-semibold border-2 border-charcoal hover:bg-charcoal hover:text-white transition-colors no-underline text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        Contribute on GitHub
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-charcoal text-white">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-3 gap-8 items-center">
                <div class="flex items-center gap-3">
                    <svg width="30" height="33" viewBox="0 0 30 33" fill="none">
                        <path d="M14.7883 7.46973L4.90405 13.0923V24.349L7.54104 25.8487V14.5978L14.7883 10.4692L22.0415 14.5978V25.8487L24.6785 24.349V13.0923L14.7883 7.46973Z" fill="#F1BC1B"/>
                        <path d="M16.0862 26.2367L14.7883 26.9779L13.4492 26.2135V14.233L10.178 16.0975V27.3485L13.4492 29.213L14.7883 29.9773L16.0862 29.2362L19.4045 27.3485V16.0975L16.0862 14.2098V26.2367Z" fill="#F1BC1B"/>
                    </svg>
                    <span class="font-bold">Magento Documentation</span>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Magento Association. Magento&reg; is a registered trademark of Adobe Inc.</p>
                </div>
                <div class="flex justify-end gap-6">
                    <a href="https://github.com/magento/magento2" class="text-gray-400 hover:text-white transition-colors" aria-label="GitHub">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
