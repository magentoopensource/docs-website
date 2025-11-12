{{-- Tip Box Component --}}
@props([
    'icon' => 'info',
    'title' => '',
    'content' => ''
])

@php
    $icons = [
        'info' => '<svg class="w-full h-full text-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
        'aperture' => '<svg class="w-full h-full text-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>',
        'lightbulb' => '<svg class="w-full h-full text-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21h6"></path><path d="M12 21V14"></path><path d="M12 3a6 6 0 0 0-6 6c0 1.657.672 3.157 1.757 4.243L9 14.5V17h6v-2.5l1.243-1.257A5.98 5.98 0 0 0 18 9a6 6 0 0 0-6-6Z"></path></svg>',
        'gear' => '<svg class="w-full h-full text-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
    ];
    $iconSvg = $icons[$icon] ?? $icons['info'];
@endphp

<div class="bg-white box-border content-stretch flex flex-col gap-6 items-center justify-center p-6 relative shrink-0 w-full border-4 border-yellow" data-name="Tips Box">
    <div class="content-stretch flex flex-col gap-6 items-start justify-start relative shrink-0 w-full">
        <div class="content-stretch flex gap-6 items-center justify-start relative shrink-0 w-full">
            <div class="relative shrink-0 size-8">
                {!! $iconSvg !!}
            </div>
            <div class="font-inter-tight leading-[0] not-italic relative shrink-0 text-charcoal text-base text-nowrap">
                @if($title)
                    <p class="leading-relaxed whitespace-pre">{{ $title }}</p>
                @elseif($content)
                    <p class="leading-relaxed whitespace-pre">{{ $content }}</p>
                @else
                    {{ $slot }}
                @endif
            </div>
        </div>
    </div>
</div>