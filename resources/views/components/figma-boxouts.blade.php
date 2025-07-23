{{-- Standard Boxout Component --}}
@props(['type' => 'info'])

@php
$classes = [
    'info' => 'bg-blue-50 border-l-4 border-blue-400',
    'warning' => 'bg-lightningYellow-300 border-l-4 border-lightningYellow-500',
    'tip' => 'bg-sweetCorn-400 border-l-4 border-sweetCorn-600',
    'featured' => 'bg-flamingo-50 border-l-4 border-flamingo-400',
];
$typeClass = $classes[$type] ?? $classes['info'];
@endphp

<div class="mb-8 p-6 rounded-r-lg {{ $typeClass }}">
    @if($type === 'warning')
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-6 h-6 mt-0.5">
                <svg class="w-full h-full text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex-1">
                {{ $slot }}
            </div>
        </div>
    @elseif($type === 'tip')
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-6 h-6 mt-0.5">
                <svg class="w-full h-full text-sweetCorn-700" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex-1">
                {{ $slot }}
            </div>
        </div>
    @else
        {{ $slot }}
    @endif
</div>