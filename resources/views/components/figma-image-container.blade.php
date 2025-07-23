{{-- Image Container Component from Figma --}}
@props(['src', 'alt', 'caption' => null, 'size' => 'full'])

@php
$sizeClasses = [
    'small' => 'max-w-md',
    'medium' => 'max-w-2xl',
    'large' => 'max-w-4xl',
    'full' => 'w-full',
];
$containerClass = $sizeClasses[$size] ?? $sizeClasses['full'];
@endphp

<figure class="mb-8 {{ $containerClass }} mx-auto">
    <div class="rounded-lg overflow-hidden shadow-sm border border-gray-200">
        <img src="{{ $src }}" alt="{{ $alt }}" class="w-full h-auto">
    </div>
    @if($caption)
    <figcaption class="mt-3 text-center text-gray-600 font-alegreya text-sm leading-5">
        {{ $caption }}
    </figcaption>
    @endif
</figure>