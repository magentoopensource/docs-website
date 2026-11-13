@extends('partials.layout')

@push('jsonld')
@if(!empty($canonical))
@php
    $categoryName = ucwords(str_replace('-', ' ', $category));
    $siteRoot = rtrim(url('/'), '/');
@endphp
@include('partials.jsonld', ['data' => \App\Services\StructuredData::graph(
    \App\Services\StructuredData::breadcrumb([
        ['Home', $siteRoot . '/'],
        [$categoryName, url('merchant/' . $category)],
        [$title, url($canonical)],
    ]),
    \App\Services\StructuredData::techArticle($title, $metaDescription, url($canonical), $lastUpdated ?? null)
)])
@endif
@endpush

@section('content')
<style>
    .docs-last-updated { margin: -0.75rem 0 1.5rem; font-size: 0.875rem; color: #4b5563; }
    /*
     * Sticky "On this page" panel (D9): caps the TOC list so the heading, "Back to top"
     * and the "Suggest an idea" button stay on screen on long pages — only the list itself
     * scrolls. max-height/overflow-y-auto as flex children isn't compiled in the current
     * build (public/build/assets/app-*.css is stale against this source tree — checked
     * with a word-boundary-safe grep before adding this), so it's scoped CSS here rather
     * than an arbitrary-value Tailwind class.
     */
    .docs-toc-panel { display: flex; flex-direction: column; max-height: calc(100vh - 7rem); }
    .docs-toc-scroll { flex: 1 1 auto; min-height: 0; overflow-y: auto; }
</style>
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
                    <p class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 mt-0">
                        {{ ucwords(str_replace('-', ' ', $category)) }}
                    </p>

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
            @php
                // Sticky "Suggest an idea" button (D9): single action, the idea-content.yml
                // contract URL (MA-DOCS-IDEAS-WIDGET.md §6), server-rendered.
                $stickyIdeaRepo = 'magentoopensource/docs';
                $stickyIdeaUrl = 'https://github.com/' . $stickyIdeaRepo . '/issues/new?' . http_build_query([
                    'template' => 'idea-content.yml',
                    'title' => '[Docs topic] ',
                    'page_url' => url()->current(),
                ]);
            @endphp
            @if(count($tableOfContents) > 0)
            <aside class="hidden xl:block">
                <div class="docs-toc-panel sticky top-24">
                    <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 mt-0 flex-none">
                        On this page
                    </h3>

                    {{-- Capped so the heading, "Back to top" and the idea button below stay on
                         screen on long pages; only this list scrolls. --}}
                    <nav class="docs-toc-scroll space-y-1.5" aria-label="Table of contents">
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

                    {{-- Sticky "Suggest an idea" (D9). At the foot of the page this divider lines up with the
                         contributor row's top border and the button with "Edit this page on GitHub":
                         77px = the row's 32px top padding + its 89px contributor card - the 44px button. --}}
                    <div class="mt-8 pt-[77px] border-t border-gray-200 flex-none">
                        <a
                            data-gh-sticky-link
                            href="{{ $stickyIdeaUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 h-11 px-3 bg-red-600 border-2 border-red-600 text-white text-sm font-semibold no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                        >
                            <svg class="w-4 h-4 flex-none" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
                            Suggest an idea<span class="sr-only"> (opens GitHub in a new tab)</span>
                        </a>
                    </div>

                    {{-- Back to Top: underneath the idea button, right-aligned, arrow after the words (Carl, 2026-10-05) --}}
                    <div class="mt-3 flex-none">
                        <a
                            href="#"
                            class="flex items-center justify-end gap-2 text-sm font-medium text-gray-600 hover:text-orange-600 transition-colors no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                        >
                            Back to top
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </aside>
            @endif

            {{-- Floating "Suggest an idea" (Vijay's feedback, Carl 2026-10-04): shown wherever the
                 panel button above is not (below xl, or no "On this page" panel), and hidden while
                 the contributors row at the bottom is on screen. Same docs-topic form. --}}
            <a
                data-gh-cbox-float
                href="{{ $stickyIdeaUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                style="display:none"
                class="fixed right-4 bottom-4 z-30 flex items-center justify-center gap-2 px-3 py-2.5 bg-red-600 border-2 border-red-600 text-white text-sm font-semibold no-underline shadow-lg focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
            >
                <svg class="w-4 h-4 flex-none" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
                Suggest an idea<span class="sr-only"> (opens GitHub in a new tab)</span>
            </a>
            <script>
                (function () {
                    var link = document.querySelector('[data-gh-cbox-float]');
                    if (!link) { return; }
                    function onScreen(el) {
                        if (!el || !el.getClientRects().length) { return false; }
                        var r = el.getBoundingClientRect();
                        return r.bottom > 0 && r.top < window.innerHeight;
                    }
                    function update() {
                        var panel = document.querySelector('[data-gh-sticky-link]');
                        var panelShown = !!panel && panel.getClientRects().length > 0;
                        link.style.display = panelShown || onScreen(document.querySelector('[aria-label="Top Contributors"]')) ? 'none' : '';
                    }
                    var queued = false;
                    function schedule() {
                        if (queued) { return; }
                        queued = true;
                        window.requestAnimationFrame(function () { queued = false; update(); });
                    }
                    window.addEventListener('scroll', schedule, { passive: true });
                    window.addEventListener('resize', schedule);
                    update();
                })();
            </script>
        </div>
    </div>
</div>

{{-- Mobile Navigation Toggle - HIDDEN: now using header hamburger menu instead --}}
{{-- <div class="lg:hidden fixed bottom-6 right-4 z-50">
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
</div> --}}

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
