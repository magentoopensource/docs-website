{{-- Figma Boxout Components matching node-id=61-192 --}}
@props(['type' => 'standard'])

{{-- Standard Boxout (with border) --}}
@if($type === 'standard')
<div class="flex flex-col gap-6 items-center justify-center p-6 rounded-[5px] mb-8 w-full border border-orange">
    <div class="flex flex-col gap-6 items-start justify-start w-full">
        <div class="font-alegreya font-bold text-slate-900 text-2xl leading-snug w-full">
            This is a standard boxout with a standard heading
        </div>
        <div class="flex flex-col gap-2 items-start justify-start w-full">
            {{ $slot }}
        </div>
    </div>
</div>

{{-- Featured Boxout (white background with lighter border) --}}
@elseif($type === 'featured')
<div class="bg-white flex flex-col gap-6 items-center justify-center p-6 rounded-[5px] mb-8 w-full border border-orange">
    <div class="flex flex-col gap-6 items-start justify-start w-full">
        <div class="font-alegreya font-bold text-slate-900 text-2xl leading-snug w-full">
            This is a featured boxout with a standard heading
        </div>
        <div class="flex flex-col gap-2 items-start justify-start w-full">
            {{ $slot }}
        </div>
    </div>
</div>

{{-- Tips Boxout (with icon) --}}
@elseif($type === 'tip')
<div class="bg-white flex flex-col gap-6 items-center justify-center p-6 rounded-[5px] mb-8 w-full border border-orange">
    <div class="flex flex-col gap-6 items-start justify-start w-full">
        <div class="flex flex-row gap-6 items-center justify-start w-full">
            <div class="h-7 w-[23px]">
                <svg class="w-full h-full text-orange" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/>
                    <path d="M11 11h2v6h-2zm0-4h2v2h-2z"/>
                </svg>
            </div>
            <div class="font-alegreya text-slate text-base leading-snug">
                <span class="font-bold">Did you know: </span>
                <span class="font-medium">this is a tips boxout with an icon</span>
            </div>
        </div>
    </div>
</div>

{{-- Warning Boxout (yellow background) --}}
@elseif($type === 'warning')
<div class="bg-yellow flex flex-row gap-6 items-center justify-center p-6 rounded-[5px] mb-8 w-full border border-yellow">
    <div class="h-7 w-[30px]">
        <svg class="w-full h-full text-brown" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L1 21h22L12 2zm0 3.5L19.5 19h-15L12 5.5zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
        </svg>
    </div>
    <div class="basis-0 flex flex-col gap-6 grow items-start justify-start min-h-px min-w-px">
        <div class="font-alegreya font-bold text-brown text-base leading-snug w-full">
            This is a warning boxout
        </div>
    </div>
</div>

@endif