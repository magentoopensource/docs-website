{{-- Article Card Component --}}
@props([
    'title' => '',
    'description' => '',
    'readTime' => '5 minute read',
    'difficulty' => 'beginner', // beginner, intermediate, advanced
    'icon' => 'default',
    'link' => '#'
])

@php
    $difficultyConfig = [
        'beginner' => [
            'bg' => 'bg-lightning-yellow-500',
            'text' => 'text-lightning-yellow-950',
            'label' => 'Beginner'
        ],
        'intermediate' => [
            'bg' => 'bg-figma-orange-500',
            'text' => 'text-white',
            'label' => 'Intermediate'
        ],
        'advanced' => [
            'bg' => 'bg-mine-shaft-500',
            'text' => 'text-white',
            'label' => 'Advanced'
        ]
    ];
    
    $config = $difficultyConfig[$difficulty] ?? $difficultyConfig['beginner'];
@endphp

<div class="bg-white flex flex-col gap-5 p-6 rounded border-t-4 border-lightning-yellow-500 shadow-sm hover:shadow-lg transition-all duration-300 hover:border-figma-orange-500 focus-within:ring-2 focus-within:ring-figma-orange-500 focus-within:ring-opacity-20">
    {{-- Header with Icon and Difficulty Badge --}}
    <div class="flex items-center justify-between w-full">
        <div class="w-8 h-8 flex-shrink-0">
            @if($icon === 'product')
                <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            @elseif($icon === 'shipping')
                <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            @elseif($icon === 'payment')
                <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            @elseif($icon === 'cart')
                <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13v6a2 2 0 002 2h8a2 2 0 002-2v-6m-8 0V9a2 2 0 012-2h4a2 2 0 012 2v4.01"/>
                </svg>
            @elseif($icon === 'tax')
                <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3-4h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            @elseif($icon === 'download')
                <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            @else
                <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            @endif
        </div>
        
        <div class="{{ $config['bg'] }} {{ $config['text'] }} flex items-center justify-center px-2 py-1.5 rounded text-xs font-inter-tight leading-[1.333]">
            {{ $config['label'] }}
        </div>
    </div>
    
    {{-- Title --}}
    <div class="flex items-center justify-start w-full">
        <h3 class="text-3xl font-inter-tight font-normal leading-[1.2] text-mine-shaft-500">
            {{ $title }}
        </h3>
    </div>
    
    {{-- Description and Actions --}}
    <div class="flex flex-col gap-6 items-start justify-start w-full">
        <p class="text-base font-inter-tight font-normal leading-[1.5] text-mine-shaft-500">
            {{ $description }}
        </p>
        
        <div class="flex items-center justify-between w-full">
            <a href="{{ $link }}" class="text-base font-inter-tight leading-[1.5] text-figma-orange-700 underline decoration-solid hover:text-figma-orange-500 transition-colors">
                Read more
            </a>
            
            <div class="flex gap-2 items-center justify-start">
                <svg class="w-6 h-6 text-mine-shaft-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12,6 12,12 16,14"></polyline>
                </svg>
                <span class="text-base font-inter-tight leading-[1.5] text-mine-shaft-300">
                    {{ $readTime }}
                </span>
            </div>
        </div>
    </div>
</div>