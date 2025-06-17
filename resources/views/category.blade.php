@extends('partials.layout')

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-white">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center">
                            <img class="h-8 w-auto" src="/img/Mage-OSLogoMark.svg" alt="Magento">
                            <div class="ml-4">
                                <h1 class="text-xl font-bold text-gray-900">{{ $category_title }}</h1>
                                <p class="text-sm text-gray-600">{{ $category_description }}</p>
                            </div>
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Breadcrumb -->
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-4">
                                <li>
                                    <div>
                                        <a href="/" class="text-gray-400 hover:text-gray-500">
                                            <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                            </svg>
                                            <span class="sr-only">Home</span>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                        </svg>
                                        <span class="ml-4 text-sm font-medium text-gray-500">{{ $category_title }}</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>

                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Category Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 {{ $category_color }} rounded-lg mb-6">
                    {!! $category_icon !!}
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $category_title }}</h2>
                <p class="text-base text-gray-600 max-w-3xl mx-auto">{{ $category_description }}</p>
            </div>

            <!-- Articles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($articles as $article)
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="{{ $article['url'] }}" class="hover:text-indigo-600">
                                    {{ $article['title'] }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm line-clamp-3">{{ $article['description'] }}</p>
                        </div>
                        @if($article['difficulty'])
                        <span class="ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($article['difficulty'] === 'Beginner') bg-green-100 text-green-800
                            @elseif($article['difficulty'] === 'Intermediate') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $article['difficulty'] }}
                        </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $article['read_time'] }} min read
                        </div>
                        <a href="{{ $article['url'] }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Read more
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Quick Start Section -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white mb-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <h3 class="text-2xl font-bold mb-4">New to {{ $category_title }}?</h3>
                        <p class="text-indigo-100 mb-6">Start with our step-by-step guide that walks you through everything from the basics to advanced techniques.</p>
                        <a href="{{ $quick_start_url ?? '#' }}" class="inline-flex items-center bg-white text-indigo-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Get Started Guide
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                    <div class="hidden lg:block">
                        <div class="bg-white bg-opacity-20 rounded-lg p-6">
                            <div class="space-y-3">
                                <div class="h-4 bg-white bg-opacity-30 rounded w-3/4"></div>
                                <div class="h-4 bg-white bg-opacity-30 rounded w-1/2"></div>
                                <div class="h-4 bg-white bg-opacity-30 rounded w-2/3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Categories -->
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Explore Other Categories</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($related_categories as $related)
                    <a href="{{ $related['url'] }}" class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                        <div class="w-10 h-10 {{ $related['color'] }} rounded-lg flex items-center justify-center mr-4">
                            {!! $related['icon'] !!}
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $related['title'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $related['count'] }} articles</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
@endsection
