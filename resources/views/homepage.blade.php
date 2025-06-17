@extends('partials.layout')

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-white">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <img class="h-8 w-auto" src="/img/Mage-OSLogoMark.svg" alt="Magento">
                        <div class="ml-4">
                            <h1 class="text-xl font-bold text-gray-900">Merchant Documentation</h1>
                            <p class="text-sm text-gray-600">Your complete guide to Magento 2</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Hero Section -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    How can we help you today?
                </h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Everything you need to know about managing your Magento 2 store, from your first product to advanced growth strategies.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto mb-12">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Search documentation..."
                           class="block w-full pl-10 pr-3 py-4 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <!-- Start Selling -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 ml-4">
                            <a href="/category/start-selling" class="hover:text-indigo-600">Start Selling</a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-6">Get your store up and running with your first products, payments, and shipping setup.</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/category/start-selling" class="text-indigo-600 hover:underline">How to create your first simple product</a></li>
                        <li><a href="/category/start-selling" class="text-indigo-600 hover:underline">Set up shipping rates by country or region</a></li>
                        <li><a href="/category/start-selling" class="text-indigo-600 hover:underline">Enable credit card payments securely</a></li>
                        <li><a href="/category/start-selling" class="text-indigo-600 hover:underline">Add free shipping over a minimum cart value</a></li>
                    </ul>
                </div>

                <!-- Manage Catalog -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 ml-4">
                            <a href="/category/manage-catalog" class="hover:text-indigo-600">Manage Catalog</a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-6">Organize and maintain your product catalog efficiently with bulk operations and smart workflows.</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/category/manage-catalog" class="text-indigo-600 hover:underline">Bulk import products via CSV</a></li>
                        <li><a href="/category/manage-catalog" class="text-indigo-600 hover:underline">How to update product images quickly</a></li>
                        <li><a href="/category/manage-catalog" class="text-indigo-600 hover:underline">Create configurable products with variants</a></li>
                        <li><a href="/category/manage-catalog" class="text-indigo-600 hover:underline">Organize products into categories efficiently</a></li>
                    </ul>
                </div>

                <!-- Handle Orders -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 ml-4">
                            <a href="/category/handle-orders" class="hover:text-indigo-600">Handle Orders</a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-6">Process orders, manage fulfillment, and handle customer service efficiently.</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/category/handle-orders" class="text-indigo-600 hover:underline">Process your first customer order</a></li>
                        <li><a href="/category/handle-orders" class="text-indigo-600 hover:underline">Issue a refund or partial refund</a></li>
                        <li><a href="/category/handle-orders" class="text-indigo-600 hover:underline">Edit shipping details after purchase</a></li>
                        <li><a href="/category/handle-orders" class="text-indigo-600 hover:underline">Print shipping labels directly from Magento</a></li>
                    </ul>
                </div>

                <!-- Grow Store -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 ml-4">
                            <a href="/category/grow-store" class="hover:text-indigo-600">Grow Store</a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-6">Scale your business with marketing tools, analytics, and customer retention strategies.</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/category/grow-store" class="text-indigo-600 hover:underline">Set up coupon codes for specific products</a></li>
                        <li><a href="/category/grow-store" class="text-indigo-600 hover:underline">Understand your best-selling products with built-in reports</a></li>
                        <li><a href="/category/grow-store" class="text-indigo-600 hover:underline">Use SEO settings to improve your store visibility</a></li>
                        <li><a href="/category/grow-store" class="text-indigo-600 hover:underline">Integrate Google Analytics with your Magento store</a></li>
                    </ul>
                </div>

                <!-- Improve UX -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 ml-4">
                            <a href="/category/improve-ux" class="hover:text-indigo-600">Improve UX</a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-6">Enhance customer experience with better design, navigation, and performance optimizations.</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/category/improve-ux" class="text-indigo-600 hover:underline">Customize your homepage layout easily</a></li>
                        <li><a href="/category/improve-ux" class="text-indigo-600 hover:underline">Set up easy navigation menus</a></li>
                        <li><a href="/category/improve-ux" class="text-indigo-600 hover:underline">Simplify your checkout experience</a></li>
                        <li><a href="/category/improve-ux" class="text-indigo-600 hover:underline">Enable product reviews to build trust</a></li>
                    </ul>
                </div>

                <!-- Stay Compliant -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 ml-4">
                            <a href="/category/stay-compliant" class="hover:text-indigo-600">Stay Compliant</a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-6">Ensure your store meets legal requirements and industry standards for data protection and commerce.</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/category/stay-compliant" class="text-indigo-600 hover:underline">Configure GDPR compliance settings</a></li>
                        <li><a href="/category/stay-compliant" class="text-indigo-600 hover:underline">Show correct VAT rates for EU businesses</a></li>
                        <li><a href="/category/stay-compliant" class="text-indigo-600 hover:underline">How to create and manage cookie notices</a></li>
                        <li><a href="/category/stay-compliant" class="text-indigo-600 hover:underline">Generate compliant invoices and documents</a></li>
                    </ul>
                </div>
            </div>

            <!-- Quick Links Section -->
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Popular Resources</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Getting Started Guide</h4>
                        <p class="text-gray-600 text-sm">Complete setup guide for new Magento stores</p>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">FAQ</h4>
                        <p class="text-gray-600 text-sm">Answers to the most common questions</p>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75c0-5.385-4.365-9.75-9.75-9.75z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Video Tutorials</h4>
                        <p class="text-gray-600 text-sm">Step-by-step video guides</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
