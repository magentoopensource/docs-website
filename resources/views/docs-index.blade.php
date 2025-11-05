@extends('partials.layout')

@section('content')
<div class="flex flex-col gap-12 items-center justify-start w-full">
    {{-- Header --}}
    <div class="flex flex-col gap-2 items-center justify-center px-6 py-24 w-full max-w-[800px] text-center">
        <h1 class="text-5xl font-inter-tight font-bold leading-tight text-charcoal dark:text-white sm:text-6xl">
            Documentation Index
        </h1>
        <p class="text-xl font-inter-tight font-normal leading-[1.5] text-charcoal dark:text-gray-300">
            Browse all documentation categories and guides
        </p>
    </div>

    {{-- Categories Grid --}}
    <section class="flex flex-col gap-12 items-center justify-start px-6 sm:px-12 md:px-16 lg:px-24 xl:px-32 pb-24 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-[1440px]">
            @php
                $borderColors = ['border-yellow', 'border-orange', 'border-yellow', 'border-orange', 'border-yellow', 'border-orange'];
                $categoryDescriptions = [
                    'getting-started' => 'Essential guides for setting up and launching your Magento store',
                    'start-selling' => 'Learn to add products, set up payments, and begin selling online',
                    'manage-catalog' => 'Organize and optimize your product catalog with bulk operations',
                    'handle-orders' => 'Process orders, manage fulfillment, and handle customer purchases',
                    'grow-store' => 'Marketing tools, analytics, and strategies to expand your business',
                    'improve-ux' => 'Enhance design, navigation, and performance for better user experience',
                    'stay-compliant' => 'Legal requirements, data protection, and regulatory compliance',
                    'customer-management' => 'Build relationships and manage your customer base effectively',
                    'reports-and-analytics' => 'Track performance with data insights and reporting tools',
                    'troubleshooting' => 'Solutions to common issues and technical problems',
                    'support-and-resources' => 'Get help, find documentation, and connect with the community',
                ];
                $colorIndex = 0;
            @endphp
            @foreach($categories as $category)
            <div class="bg-white dark:bg-gray-800 flex flex-col gap-5 p-6 rounded border-t-4 {{ $borderColors[$colorIndex % count($borderColors)] }} shadow-sm hover:shadow-lg hover:border-orange transition-all duration-300 focus-within:ring-2 focus-within:ring-orange focus-within:ring-opacity-20">
                {{-- Icon --}}
                <div class="flex items-start justify-start w-full">
                    <div class="w-8 h-8 flex-shrink-0">
                        <svg class="w-full h-full text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>

                {{-- Title --}}
                <div class="flex items-center justify-start w-full">
                    <h3 class="text-3xl font-inter-tight font-bold leading-[1.2] text-charcoal dark:text-white">
                        {{ $category['name'] }}
                    </h3>
                </div>

                {{-- Description and Actions --}}
                <div class="flex flex-col gap-6 items-start justify-start w-full">
                    <p class="text-base font-inter-tight font-normal leading-[1.5] text-charcoal dark:text-gray-300">
                        {{ $categoryDescriptions[$category['slug']] ?? 'Explore guides and tutorials for ' . strtolower($category['name']) }}
                    </p>

                    <div class="flex items-center justify-between w-full">
                        <a href="/merchant/{{ $category['slug'] }}" class="text-base font-inter-tight leading-[1.5] text-red underline decoration-solid hover:text-orange transition-colors">
                            View category
                        </a>

                        <div class="flex gap-2 items-center justify-start">
                            <svg class="w-6 h-6 text-gray-darker" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-base font-inter-tight leading-[1.5] text-gray-darker">
                                {{ count($category['articles']) }} articles
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @php $colorIndex++; @endphp
            @endforeach
        </div>
    </section>
</div>
@endsection
