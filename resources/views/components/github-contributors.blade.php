{{-- GitHub Contributors Widget --}}
@props(['editUrl' => ''])

@php
    $service = new \App\Services\GitHubContributorsService();
    $contributors = $service->getTopContributors(3);
    $contributorsUrl = $service->getContributorsUrl();
@endphp

<div class="mt-16 pt-8 border-t border-gray-200 max-w-4xl mx-auto lg:mx-0">
    {{-- Two-column layout: Contributors LEFT, Edit link RIGHT --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-8">
        {{-- Contributors Section (LEFT) --}}
        @if(count($contributors) > 0)
        <div class="flex-1" role="region" aria-label="Top Contributors">
            <div class="flex flex-wrap items-center gap-3">
                @foreach($contributors as $index => $contributor)
                    <a
                        href="{{ $contributor['html_url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group relative flex items-center gap-3 px-4 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 hover:border-gray-300 transition-all duration-150 focus:outline-none no-underline"
                        title="{{ $contributor['login'] }} - {{ number_format($contributor['contributions']) }} contributions"
                    >
                        {{-- Avatar (SQUARE) --}}
                        <img
                            src="{{ $contributor['avatar_url'] }}"
                            alt="{{ $contributor['login'] }}"
                            class="w-8 h-8 ring-2 ring-white"
                            loading="lazy"
                            width="32"
                            height="32"
                        />

                        {{-- Username --}}
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                            {{ $contributor['login'] }}
                        </span>

                        {{-- Contribution count badge --}}
                        <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 font-medium">
                            {{ number_format($contributor['contributions']) }}
                        </span>

                        {{-- Rank badge for top 3 --}}
                        <span class="absolute -top-2 -right-2 flex items-center justify-center w-5 h-5 text-[10px] font-bold shadow-sm
                            @if($index === 0) bg-yellow-400 text-yellow-900 ring-2 ring-yellow-500
                            @elseif($index === 1) bg-gray-300 text-gray-700 ring-2 ring-gray-400
                            @else bg-orange-300 text-orange-800 ring-2 ring-orange-400
                            @endif
                        ">
                            {{ $index + 1 }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- View all link --}}
            <div class="mt-4">
                <a
                    href="{{ $contributorsUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors duration-150 focus:outline-none no-underline"
                >
                    View all contributors
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
        @endif

        {{-- Edit Link (RIGHT) --}}
        @if($editUrl)
        <div class="flex-shrink-0 sm:text-right">
            <a
                href="{{ $editUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-150 focus:outline-none no-underline"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit this page on GitHub
            </a>
        </div>
        @endif
    </div>
</div>
