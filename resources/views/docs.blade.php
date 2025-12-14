@extends('partials.layout')

@section('content')
<div class="relative bg-off-white">
    <div class="mx-auto max-w-7xl xl:max-w-8xl px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb Navigation --}}
        <nav class="docs-breadcrumb pt-6 mb-6" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <span>›</span>
            <a href="/merchant/{{ $category }}">{{ ucwords(str_replace('-', ' ', $category)) }}</a>
            <span>›</span>
            <span class="current">{{ $title }}</span>
        </nav>

        <div class="docs-layout-grid">
            {{-- Left Sidebar: Category Articles --}}
            <aside class="hidden lg:block">
                <div class="sticky top-24">
                    {{-- Category Header --}}
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 mt-0">
                        {{ ucwords(str_replace('-', ' ', $category)) }}
                    </h3>

                    {{-- Category Articles Navigation --}}
                    <nav class="space-y-1" aria-label="Category navigation">
                        @foreach($categoryArticles as $article)
                            <a
                                href="/merchant/{{ $article['path'] }}"
                                class="group flex items-start gap-3 -mx-3 px-3 py-2.5 text-sm transition-colors duration-150 no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset
                                    {{ $article['slug'] === $page ? 'bg-yellow text-charcoal font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
                            >
                                <span class="flex-1">{{ $article['title'] }}</span>
                                @if($article['slug'] === $page)
                                    <svg class="w-5 h-5 text-charcoal flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </nav>

                    {{-- View All in Category --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a
                            href="/merchant/{{ $category }}"
                            class="flex items-center gap-2 -mx-3 px-3 py-2 text-sm font-medium text-orange-600 hover:text-orange-700 hover:bg-orange-50 transition-colors no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            View all in category
                        </a>
                    </div>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <main class="min-w-0 max-w-full overflow-hidden">
                {{-- Documentation Content with optimal reading width --}}
                <article class="docs-content max-w-none lg:max-w-4xl mx-auto lg:mx-0 {{ ($isStyledHtml ?? false) ? 'styled-html' : '' }}">
                    {!! $content !!}
                </article>

                {{-- Edit Link and Contributors Footer --}}
                <x-github-contributors :edit-url="$edit_link" />
            </main>

            {{-- Right Sidebar: Table of Contents --}}
            @if(count($tableOfContents) > 0)
            <aside class="hidden xl:block">
                <div class="sticky top-24">
                    <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 mt-0">
                        On this page
                    </h3>

                    <nav class="space-y-1.5" aria-label="Table of contents">
                        @foreach($tableOfContents as $heading)
                            <a
                                href="#{{ $heading['slug'] }}"
                                class="block py-1.5 text-sm transition-colors duration-150 no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset
                                    {{ $heading['level'] === 2 ? 'font-medium text-gray-700 hover:text-orange-600' : 'pl-3 text-gray-600 hover:text-gray-900' }}"
                            >
                                {{ $heading['text'] }}
                            </a>
                        @endforeach
                    </nav>

                    {{-- Back to Top --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <a
                            href="#"
                            class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-orange-600 transition-colors no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            Back to top
                        </a>
                    </div>
                </div>
            </aside>
            @endif
        </div>
    </div>
</div>

{{-- Mobile Navigation Toggle (for smaller screens) --}}
<div class="lg:hidden fixed bottom-6 right-4 z-50">
    <button
        data-mobile-menu-toggle
        aria-label="Toggle navigation menu"
        aria-expanded="false"
        class="flex items-center gap-1.5 sm:gap-2 px-3 py-2.5 sm:px-4 sm:py-3 bg-orange-600 text-white shadow-lg hover:bg-orange-700 active:bg-orange-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2 rounded-md active:scale-95 transform"
    >
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <span class="text-xs sm:text-sm font-medium">Menu</span>
    </button>
</div>

{{-- Mobile Navigation Overlay --}}
<div
    data-mobile-menu-overlay
    class="hidden lg:hidden fixed inset-0 z-40 bg-gray-800 bg-opacity-50 transition-opacity duration-200"
    aria-hidden="true"
>
    <div
        data-mobile-menu-panel
        aria-hidden="true"
        class="hidden absolute right-0 top-0 bottom-0 w-80 max-w-full bg-white shadow-xl overflow-y-auto transform translate-x-full transition-transform duration-200 ease-out"
    >
        <div class="p-6">
            {{-- Close Button --}}
            <button
                data-mobile-menu-close
                aria-label="Close navigation menu"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Category Articles --}}
            <div class="mb-8 px-3">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 mt-0">
                    {{ ucwords(str_replace('-', ' ', $category)) }}
                </h3>
                <nav class="space-y-1">
                    @foreach($categoryArticles as $article)
                        <a
                            href="/merchant/{{ $article['path'] }}"
                            class="block -mx-3 px-3 py-2.5 text-sm transition-colors no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset
                                {{ $article['slug'] === $page ? 'bg-yellow text-charcoal font-medium' : 'text-gray-700 hover:bg-gray-50' }}"
                        >
                            {{ $article['title'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Table of Contents --}}
            @if(count($tableOfContents) > 0)
            <div class="pt-8 border-t border-gray-200 px-3">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 mt-0">
                    On this page
                </h3>
                <nav class="space-y-1.5">
                    @foreach($tableOfContents as $heading)
                        <a
                            href="#{{ $heading['slug'] }}"
                            class="block py-1.5 text-sm transition-colors no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset
                                {{ $heading['level'] === 2 ? 'font-medium text-gray-700 hover:text-orange-600' : 'pl-3 text-gray-600 hover:text-gray-900' }}"
                        >
                            {{ $heading['text'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
