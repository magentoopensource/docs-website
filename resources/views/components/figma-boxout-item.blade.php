{{-- Boxout Item Component for list items within boxouts --}}
@props(['text' => 'Boxout text example'])

<div class="flex flex-row gap-2.5 items-center justify-center w-full">
    <div class="size-3">
        <svg class="w-full h-full text-slate-600" fill="currentColor" viewBox="0 0 12 12">
            <circle cx="6" cy="6" r="2"/>
        </svg>
    </div>
    <div class="basis-0 grow min-h-px min-w-px font-alegreya text-slate-600 text-base leading-6">
        {{ $text }}
    </div>
</div>