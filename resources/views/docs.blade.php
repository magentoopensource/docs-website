@extends('partials.layout')

@section('content')
    <x-accessibility.skip-to-content-link/>
    
    {{-- Frame 54 Header - Enhanced version --}}
    <x-frame54-header />
    
    <div class="relative overflow-auto bg-white" id="docsScreen">
        <div class="relative lg:flex lg:items-start">
            <aside class="hidden fixed top-16 bottom-0 left-0 z-20 h-full w-16 bg-gradient-to-b from-gray-100 to-white transition-all duration-300 overflow-hidden lg:sticky lg:w-80 lg:shrink-0 lg:flex lg:flex-col lg:justify-end lg:items-end 2xl:max-w-lg 2xl:w-full">
                <div class="relative min-h-0 flex-1 flex flex-col xl:w-80">
                    <div class="overflow-y-auto overflow-x-hidden px-4 lg:overflow-hidden lg:px-8 xl:px-12">
                        <nav id="indexed-nav" class="hidden lg:block lg:mt-4">
                            <div class="docs_sidebar">
                                {!! $index !!}
                            </div>
                        </nav>
                    </div>
                </div>
            </aside>

            <section class="flex-1 bg-gray-50 min-h-screen pt-16">
                <div class="max-w-7xl mx-auto">
                    <!-- Content Header (simplified since search is now in main header) -->
                    <div class="bg-white border-b border-gray-200 shadow-sm">
                        <div class="max-w-screen-lg px-8 sm:px-16 lg:px-24 mx-auto">
                            <div class="flex flex-col items-end py-4 transition-colors lg:flex-row-reverse">
                                <div class="hidden lg:flex items-center justify-center">
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
