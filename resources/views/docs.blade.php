@extends('partials.layout')

@section('content')
<div class="relative bg-white">
    <div class="mx-auto max-w-7xl xl:max-w-8xl px-4 sm:px-6 lg:px-8">
        <div class="flex gap-8 lg:gap-12 py-8 lg:py-12">
            {{-- Left Sidebar: Category Articles --}}
            <aside class="hidden lg:block w-64 flex-shrink-0">
                <div class="sticky top-24">
                    {{-- Category Header --}}
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        {{ ucwords(str_replace('-', ' ', $category)) }}
                    </h3>

                    {{-- Category Articles Navigation --}}
                    <nav class="space-y-1" aria-label="Category navigation">
                        @foreach($categoryArticles as $article)
                            <a
                                href="/merchant/{{ $article['path'] }}"
                                class="group flex items-start gap-3 px-3 py-2.5 text-sm rounded-lg transition-colors duration-150 no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset
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
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-orange-600 hover:text-orange-700 hover:bg-orange-50 rounded-lg transition-colors no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
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
            <main class="flex-1 min-w-0">
                {{-- Documentation Content with optimal reading width --}}
                <article class="docs-content prose prose-charcoal max-w-[75ch] mx-auto lg:mx-0">
                    {!! $content !!}
                </article>

                {{-- Edit Link Footer --}}
                <div class="mt-16 pt-8 border-t border-gray-200 max-w-[75ch] mx-auto lg:mx-0">
                    <a
                        href="{{ $edit_link }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors rounded focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit this page on GitHub
                    </a>
                </div>
            </main>

            {{-- Right Sidebar: Table of Contents --}}
            @if(count($tableOfContents) > 0)
            <aside class="hidden xl:block w-64 flex-shrink-0">
                <div class="sticky top-24">
                    <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        On this page
                    </h3>

                    <nav class="space-y-1.5" aria-label="Table of contents">
                        @foreach($tableOfContents as $heading)
                            <a
                                href="#{{ $heading['slug'] }}"
                                class="block py-1.5 text-sm transition-colors duration-150 no-underline rounded focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset
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
                            class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-orange-600 transition-colors no-underline rounded focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
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
<div class="lg:hidden fixed bottom-4 right-4 z-50">
    <button
        data-mobile-menu-toggle
        aria-label="Toggle navigation menu"
        aria-expanded="false"
        class="flex items-center gap-2 px-4 py-3 bg-orange-600 text-white rounded-full shadow-lg hover:bg-orange-700 transition-colors focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <span class="text-sm font-medium">Menu</span>
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
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Category Articles --}}
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                    {{ ucwords(str_replace('-', ' ', $category)) }}
                </h3>
                <nav class="space-y-1">
                    @foreach($categoryArticles as $article)
                        <a
                            href="/merchant/{{ $article['path'] }}"
                            class="block px-3 py-2.5 text-sm rounded-lg transition-colors no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset
                                {{ $article['slug'] === $page ? 'bg-yellow text-charcoal font-medium' : 'text-gray-700 hover:bg-gray-50' }}"
                        >
                            {{ $article['title'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Table of Contents --}}
            @if(count($tableOfContents) > 0)
            <div class="pt-8 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                    On this page
                </h3>
                <nav class="space-y-1.5">
                    @foreach($tableOfContents as $heading)
                        <a
                            href="#{{ $heading['slug'] }}"
                            class="block py-1.5 text-sm transition-colors no-underline rounded focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset
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
