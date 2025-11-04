{{-- Grouped Links Component --}}
@props([
    'title' => '',
    'links' => []
])

@php
    $icons = [
        'plugin' => '<svg class="w-full h-full text-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="8" y="8" width="8" height="8" rx="2" ry="2"></rect><path d="m8 8-2-2-2-2"></path><path d="m16 8 2-2 2-2"></path><path d="m8 16-2 2-2 2"></path><path d="m16 16 2 2 2 2"></path></svg>',
        'link' => '<svg class="w-full h-full text-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>',
        'github' => '<svg class="w-full h-full text-orange" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>',
        'doc' => '<svg class="w-full h-full text-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14,2 14,8 20,8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10,9 9,9 8,9"></polyline></svg>',
    ];
@endphp

<div class="content-stretch flex flex-col gap-5 items-start justify-start relative shrink-0 w-full">
    @if($title)
        <div class="font-inter-tight font-bold leading-[0] min-w-full not-italic relative shrink-0 text-charcoal text-2xl" style="width: min-content">
            <p class="leading-[1.333]">{{ $title }}</p>
        </div>
    @endif
    
    @if(count($links) > 0)
        @foreach(array_chunk($links, 2) as $linkPair)
            <div class="content-stretch flex flex-col gap-6 items-center justify-center relative rounded-[5px] shrink-0 w-full">
                <div class="content-stretch flex flex-col gap-6 items-start justify-start relative shrink-0 w-full">
                    <div class="content-stretch flex gap-2.5 items-start justify-start relative shrink-0 w-full">
                        @foreach($linkPair as $link)
                            <div class="basis-0 box-border content-stretch flex flex-col gap-4 grow items-center justify-start min-h-px min-w-px pb-5 pt-0 px-0 relative rounded-br-[4px] shrink-0">
                                <div class="bg-gray-light h-1 shrink-0 w-full"></div>
                                <div class="box-border content-stretch flex gap-2.5 items-start justify-start px-2 py-0 relative shrink-0 w-full">
                                    <div class="relative shrink-0 size-6">
                                        @if(isset($link['icon']) && isset($icons[$link['icon']]))
                                            {!! $icons[$link['icon']] !!}
                                        @else
                                            {!! $icons['link'] !!}
                                        @endif
                                    </div>
                                    <div class="basis-0 content-stretch flex flex-col font-inter-tight grow items-start justify-center leading-[0] min-h-px min-w-px not-italic relative shrink-0">
                                        <div class="relative shrink-0 text-charcoal text-base w-full">
                                            @if(isset($link['url']))
                                                <a href="{{ $link['url'] }}" class="leading-[1.5] text-charcoal hover:text-orange">{{ $link['title'] ?? 'Link' }}</a>
                                            @else
                                                <p class="leading-[1.5]">{{ $link['title'] ?? 'Link' }}</p>
                                            @endif
                                        </div>
                                        @if(isset($link['description']))
                                            <div class="relative shrink-0 text-gray-darkest text-xs w-full">
                                                <p class="leading-[1.333]">{{ $link['description'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        {{-- Fill empty space if odd number of links --}}
                        @if(count($linkPair) === 1)
                            <div class="basis-0 grow min-h-px min-w-px"></div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>