{{-- Category Header Component --}}
@props([
    'title' => 'Category Title',
    'description' => 'Category description goes here.',
    'centerContent' => true
])

<div class="flex flex-col items-center justify-center gap-16 px-4 sm:px-6 lg:px-8 pt-24 pb-0 w-full">
    <div class="flex flex-col gap-6 items-start justify-start {{ $centerContent ? 'text-center' : '' }} text-charcoal">
        <h1 class="text-5xl font-inter-tight font-extrabold leading-none {{ $centerContent ? 'w-full' : '' }}">
            {{ $title }}
        </h1>
        <p class="text-xl font-inter-tight font-medium leading-snug {{ $centerContent ? 'w-full' : '' }}">
            {{ $description }}
        </p>
    </div>
</div>