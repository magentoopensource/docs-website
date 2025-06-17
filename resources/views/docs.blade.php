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

            <section class="flex-1">
                <div class="max-w-screen-lg px-8 sm:px-16 lg:px-24">
                    <div class="flex flex-col items-end border-b border-gray-200 py-1 transition-colors lg:mt-8 lg:flex-row-reverse">
                        <div class="hidden lg:flex items-center justify-center ml-8">
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
                        </div>
                        <div class="relative mt-8 flex items-center justify-end w-full h-10 lg:mt-0">
                            <div class="flex-1 flex items-center">
                                <button id="docsearch"
                                        class="text-gray-800 transition-colors w-full"></button>
                            </div>
                        </div>
                    </div>

                    <section class="mt-8 md:mt-16">
                        <section class="docs_main max-w-prose">
                            {!! $content !!}
                        </section>
                    </section>
                </div>
            </section>
        </div>
    </div>
@endsection
