@extends('partials.layout')

@section('content')
    <x-accessibility.skip-to-content-link/>
    <div class="relative overflow-auto bg-white" id="docsScreen">
        <div class="relative lg:flex lg:items-start">
            <aside class="hidden fixed top-0 bottom-0 left-0 z-20 h-full w-16 bg-gradient-to-b from-gray-100 to-white transition-all duration-300 overflow-hidden lg:sticky lg:w-80 lg:shrink-0 lg:flex lg:flex-col lg:justify-end lg:items-end 2xl:max-w-lg 2xl:w-full">
                <div class="relative min-h-0 flex-1 flex flex-col xl:w-80">
                    <a href="/" class="flex items-center py-8 px-4 lg:px-8 xl:px-12">
                        <img
                                class="w-8 h-8 shrink-0 transition-all duration-300 lg:w-64 lg:h-12"
                                src="/img/Mage-OSLogoOrange.svg"
                                alt="mage-os"
                        >
                    </a>
                    <div class="overflow-y-auto overflow-x-hidden px-4 lg:overflow-hidden lg:px-8 xl:px-12">
                        <nav id="indexed-nav" class="hidden lg:block lg:mt-4">
                            <div class="docs_sidebar">
                                {!! $index !!}
                            </div>
                        </nav>
                    </div>
                </div>
            </aside>

            <header
                    class="lg:hidden"
                    @keydown.window.escape="navIsOpen = false"
                    @click.away="navIsOpen = false"
            >
                <div class="relative mx-auto w-full py-10 bg-white transition duration-200">
                    <div class="mx-auto px-8 sm:px-16 flex items-center justify-between">
                        <a href="/" class="flex items-center">
                            <img class="max-h-8 sm:max-h-10" src="/img/Mage-OSLogoMark.svg" alt="Mage-OS">
                            <img class="hidden ml-6 sm:max-h-10 sm:block" src="/img/Mage-OSLogoType.svg" alt="Mage-OS">
                        </a>
                        <div class="flex-1 flex items-center justify-end">
                            <a href="{!! $edit_link !!}" target="_blank" title="edit this site">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     data-name="Layer 1"
                                     viewBox="0 0 100 100"
                                     x="0px"
                                     y="0px"
                                     width="40"
                                     height="40">
                                    <defs>
                                        <style>.cls-1, .cls-2 {
                                                fill: none;
                                                stroke: #000;
                                                stroke-linejoin: round;
                                            }

                                            .cls-1 {
                                                stroke-linecap: round;
                                            }</style>
                                    </defs>
                                    <path class="cls-1"
                                          d="M42.16,66,30,70l3.23-12.33L63.38,25.78a.87.87,0,0,1,1.23,0L72.23,33a.87.87,0,0,1,.05,1.23Z"/>
                                    <line class="cls-1" x1="58.58" y1="30.85" x2="67.48" y2="39.26"/>
                                    <line class="cls-1" x1="31.2" y1="65.5" x2="34.44" y2="68.56"/>
                                    <line class="cls-2" x1="27.49" y1="74.49" x2="64.48" y2="74.49"/>
                                </svg>
                            </a>
                            <button class="ml-2 relative w-10 h-10 p-2 text-gray-600 lg:hidden focus:outline-none focus:shadow-outline"
                                    aria-label="Menu"
                                    @click.prevent="navIsOpen = !navIsOpen">
                                <svg x-show="! navIsOpen"
                                     x-transition.opacity
                                     class="absolute inset-0 mt-2 ml-2 w-6 h-6"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     fill="none"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <line x1="3" y1="12" x2="21" y2="12"></line>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <line x1="3" y1="18" x2="21" y2="18"></line>
                                </svg>
                                <svg x-show="navIsOpen"
                                     x-transition.opacity
                                     x-cloak
                                     class="absolute inset-0 mt-2 ml-2 w-6 h-6"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     fill="none"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <span :class="{ 'shadow-sm': navIsOpen }" class="absolute inset-0 z-20 pointer-events-none"></span>
                </div>
                <div
                        x-show="navIsOpen"
                        x-transition:enter="duration-150"
                        x-transition:leave="duration-100 ease-in"
                        x-cloak
                >
                    <nav
                            x-show="navIsOpen"
                            x-cloak
                            class="absolute w-full transform origin-top shadow-sm z-10"
                            x-transition:enter="duration-150 ease-out"
                            x-transition:enter-start="opacity-0 -translate-y-8 scale-75"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="duration-100 ease-in"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-8 scale-75"
                    >
                        <div class="relative p-8 bg-white docs_sidebar">
                            {!! $index !!}
                        </div>
                    </nav>
                </div>
            </header>

            <section class="flex-1 bg-gray-50 min-h-screen">
                <div class="max-w-7xl mx-auto">
                    <!-- Header Section -->
                    <div class="bg-white border-b border-gray-200 shadow-sm">
                        <div class="max-w-screen-lg px-8 sm:px-16 lg:px-24 mx-auto">
                            <div class="flex flex-col items-end py-6 transition-colors lg:flex-row-reverse">
                                <div class="hidden lg:flex items-center justify-center ml-8">
                                    <a href="{!! $edit_link !!}" 
                                       target="_blank" 
                                       title="Edit this page"
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 hover:text-gray-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit page
                                    </a>
                                </div>
                                <div class="relative mt-4 flex items-center justify-end w-full lg:mt-0">
                                    <div class="flex-1 flex items-center">
                                        <button id="docsearch"
                                                class="text-gray-800 transition-colors w-full bg-gray-100 rounded-lg px-4 py-3 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Area -->
                    <div class="flex flex-col lg:flex-row">
                        <!-- Content Section -->
                        <main class="flex-1 bg-white">
                            <div class="max-w-screen-lg px-8 sm:px-16 lg:px-24 mx-auto">
                                <!-- Breadcrumb (if needed) -->
                                <nav class="flex py-4 text-sm text-gray-500" aria-label="Breadcrumb">
                                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                        <li class="inline-flex items-center">
                                            <a href="/" class="text-gray-500 hover:text-primary-600 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                                </svg>
                                                Home
                                            </a>
                                        </li>
                                        <li>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="ml-1 text-gray-700 font-medium">Documentation</span>
                                            </div>
                                        </li>
                                    </ol>
                                </nav>

                                <!-- Article Content -->
                                <article class="pb-16">
                                    <div class="docs_main prose prose-lg max-w-none">
                                        {!! $content !!}
                                    </div>

                                    <!-- Article Footer -->
                                    <footer class="mt-16 pt-8 border-t border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <span class="text-sm text-gray-500">Was this helpful?</span>
                                                <div class="flex space-x-2">
                                                    <button class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-green-100 hover:text-green-700 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V8a2 2 0 00-2-2H4.5a2 2 0 00-1.838 1.235l-1.714 4A2 2 0 002.486 14H4.5V8.5A2.5 2.5 0 017 6h4a2 2 0 012 2v2.07zM7 20H4.5a2.5 2.5 0 01-2.5-2.5v-6A2.5 2.5 0 014.5 9H7v11z"/>
                                                        </svg>
                                                        Yes
                                                    </button>
                                                    <button class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-red-100 hover:text-red-700 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h8.5a2 2 0 001.838-1.235l1.714-4A2 2 0 0021.514 10H19.5V15.5a2.5 2.5 0 01-2.5 2.5H13a2 2 0 01-2-2v-2.07zm7-10H19.5a2.5 2.5 0 012.5 2.5v6a2.5 2.5 0 01-2.5 2.5H17V4z"/>
                                                        </svg>
                                                        No
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Last updated: {{ date('M j, Y') }}
                                            </div>
                                        </div>
                                    </footer>
                                </article>
                            </div>
                        </main>

                        <!-- Table of Contents Sidebar (optional) -->
                        <aside class="hidden xl:block w-64 flex-shrink-0">
                            <div class="sticky top-8 px-6 py-8">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">On this page</h3>
                                <nav class="space-y-2" id="toc">
                                    <!-- Table of contents will be populated by JavaScript -->
                                </nav>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
