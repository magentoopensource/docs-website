@extends('partials.layout')

@section('content')
<div class="relative max-w-screen-xl mx-auto px-5 py-12">
    <div class="max-w-3xl mx-auto text-center mb-16">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white sm:text-5xl mb-4">
            Documentation Index
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-400">
            Browse all documentation categories and guides
        </p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
            <a href="/docs/{{ $category['slug'] }}"
               class="group relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    {{ $category['name'] }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ count($category['articles']) }} articles
                </p>
                <div class="flex items-center text-blue-600 dark:text-blue-400 font-medium">
                    View category
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
