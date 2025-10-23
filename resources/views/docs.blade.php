@extends('partials.layout')

@section('content')
<div class="relative bg-white">
    <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">
        <div class="flex gap-8 py-8">
            {{-- Left Sidebar: Category Articles --}}
            <aside class="hidden lg:block w-64 flex-shrink-0">
                <div class="sticky top-24">
                    {{-- Category Header --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                            {{ ucwords(str_replace('-', ' ', $category)) }}
                        </h3>
                    </div>

                    {{-- Category Articles Navigation --}}
                    <nav class="space-y-1" aria-label="Category navigation">
                        @foreach($categoryArticles as $article)
                            <a
                                href="/docs/{{ $article['path'] }}"
                                class="group flex items-start gap-3 px-3 py-2 text-sm rounded-lg transition-colors duration-150
                                    {{ $article['slug'] === $page ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
                            >
                                <span class="flex-1">{{ $article['title'] }}</span>
                                @if($article['slug'] === $page)
                                    <svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </nav>

                    {{-- View All in Category --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a
                            href="/docs/{{ $category }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-orange-600 hover:text-orange-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            View all in category
                        </a>
                    </div>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <main class="flex-1 min-w-0">
                {{-- Community Note Banner --}}
                @if($communityNote)
                <div class="mb-8 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1 text-sm text-blue-900">
                            <p class="font-medium mb-1">Community-Maintained Documentation</p>
                            <p class="text-blue-800">
                                This documentation is maintained by the Magento community. Help improve it by
                                <a href="{{ $edit_link }}" target="_blank" rel="noopener" class="underline hover:text-blue-900">
                                    contributing on GitHub
                                </a>.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Documentation Content --}}
                <article class="docs-content max-w-none">
                    {!! $content !!}
                </article>

                {{-- Edit Link Footer --}}
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <a
                        href="{{ $edit_link }}"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="mb-4">
                        <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">
                            On this page
                        </h3>
                    </div>

                    <nav class="space-y-2" aria-label="Table of contents">
                        @foreach($tableOfContents as $heading)
                            <a
                                href="#{{ $heading['slug'] }}"
                                class="block text-sm transition-colors duration-150
                                    {{ $heading['level'] === 2 ? 'font-medium text-gray-700 hover:text-orange-600' : 'pl-4 text-gray-600 hover:text-gray-900' }}"
                            >
                                {{ $heading['text'] }}
                            </a>
                        @endforeach
                    </nav>

                    {{-- Back to Top --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <a
                            href="#"
                            class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-orange-600 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        @click="mobileNavOpen = !mobileNavOpen"
        class="flex items-center gap-2 px-4 py-3 bg-orange-600 text-white rounded-full shadow-lg hover:bg-orange-700 transition-colors"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <span class="text-sm font-medium">Menu</span>
    </button>
</div>

{{-- Mobile Navigation Overlay --}}
<div
    x-data="{ mobileNavOpen: false }"
    x-show="mobileNavOpen"
    @click.away="mobileNavOpen = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="lg:hidden fixed inset-0 z-40 bg-gray-800 bg-opacity-50"
    style="display: none;"
>
    <div
        x-show="mobileNavOpen"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute right-0 top-0 bottom-0 w-80 max-w-full bg-white shadow-xl overflow-y-auto"
    >
        <div class="p-6">
            {{-- Close Button --}}
            <button
                @click="mobileNavOpen = false"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            href="/docs/{{ $article['path'] }}"
                            class="block px-3 py-2 text-sm rounded-lg transition-colors
                                {{ $article['slug'] === $page ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-50' }}"
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
                <nav class="space-y-2">
                    @foreach($tableOfContents as $heading)
                        <a
                            href="#{{ $heading['slug'] }}"
                            @click="mobileNavOpen = false"
                            class="block text-sm transition-colors
                                {{ $heading['level'] === 2 ? 'font-medium text-gray-700' : 'pl-4 text-gray-600' }}"
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
