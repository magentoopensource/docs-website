{{-- Warning Box Component --}}
@props([
    'title' => '',
    'content' => ''
])

<div class="bg-[#fff147] box-border content-stretch flex gap-6 items-center justify-center p-6 relative shrink-0 w-full border-4 border-[#e09900]" data-name="Warning Box">
    <div class="relative shrink-0 size-8">
        <svg class="w-full h-full text-[#e09900]" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
        </svg>
    </div>
    <div class="basis-0 content-stretch flex flex-col gap-6 grow items-start justify-start min-h-px min-w-px relative shrink-0">
        @if($title)
            <div class="font-inter-tight font-bold leading-[0] not-italic relative shrink-0 text-[#442204] text-base w-full">
                <p class="leading-relaxed">{{ $title }}</p>
            </div>
        @endif
        @if($content)
            <div class="font-inter-tight leading-[0] not-italic relative shrink-0 text-[#442204] text-base w-full">
                <p class="leading-relaxed">{{ $content }}</p>
            </div>
        @elseif($slot->isNotEmpty())
            <div class="font-inter-tight leading-[0] not-italic relative shrink-0 text-[#442204] text-base w-full">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>