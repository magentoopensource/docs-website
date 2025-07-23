{{-- Comprehensive documentation layout from Figma design --}}
<div class="flex min-h-screen bg-white">
    {{-- Left Sidebar Navigation --}}
    <aside class="fixed top-16 left-0 bottom-0 w-[308px] bg-white border-r border-gray-200 overflow-y-auto z-10">
        <div class="px-6 py-8">
            {{-- Merchant Onboarding Section --}}
            <div class="mb-8">
                <h3 class="text-waterloo-900 font-alegreya font-semibold text-lg leading-6 mb-4">
                    Merchant onboarding
                </h3>
                <nav class="space-y-3">
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Getting started
                    </a>
                    <a href="#" class="block text-flamingo-400 font-alegreya text-base leading-6 font-medium">
                        First products
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Catalog basics
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Accepting payments
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Configuring checkout
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Setting up shipping
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Taxes and locations
                    </a>
                </nav>
            </div>

            {{-- Start Selling Section --}}
            <div class="mb-8">
                <h3 class="text-waterloo-900 font-alegreya font-semibold text-lg leading-6 mb-4">
                    Start selling
                </h3>
                <nav class="space-y-3">
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Add first products
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Configure payment methods
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Set up shipping options
                    </a>
                </nav>
            </div>

            {{-- Manage Catalog Section --}}
            <div class="mb-8">
                <h3 class="text-waterloo-900 font-alegreya font-semibold text-lg leading-6 mb-4">
                    Manage catalog
                </h3>
                <nav class="space-y-3">
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Product types
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Categories organization
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Bulk operations
                    </a>
                </nav>
            </div>

            {{-- Handle Orders Section --}}
            <div class="mb-8">
                <h3 class="text-waterloo-900 font-alegreya font-semibold text-lg leading-6 mb-4">
                    Handle orders
                </h3>
                <nav class="space-y-3">
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Order processing
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Fulfillment workflow
                    </a>
                    <a href="#" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                        Returns and refunds
                    </a>
                </nav>
            </div>
        </div>
    </aside>

    {{-- Main Content Area --}}
    <main class="flex-1 ml-[308px] mr-[308px]">
        <div class="max-w-4xl mx-auto px-8 py-12">
            {{-- Main Content --}}
            <div class="prose prose-lg max-w-none">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Right Sidebar - Page Navigation --}}
    <aside class="fixed top-16 right-0 bottom-0 w-[308px] bg-white border-l border-gray-200 overflow-y-auto z-10">
        <div class="px-6 py-8">
            <h3 class="text-waterloo-900 font-alegreya font-semibold text-lg leading-6 mb-6">
                On this page
            </h3>
            <nav class="space-y-4">
                <a href="#overview" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                    Overview
                </a>
                <a href="#before-you-begin" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                    Before you begin
                </a>
                <a href="#create-first-product" class="block text-flamingo-400 font-alegreya text-base leading-6 font-medium">
                    Create your first product
                </a>
                <a href="#product-types" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors pl-4">
                    Product types
                </a>
                <a href="#required-fields" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors pl-4">
                    Required fields
                </a>
                <a href="#images-media" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors pl-4">
                    Images and media
                </a>
                <a href="#pricing-inventory" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                    Pricing and inventory
                </a>
                <a href="#seo-optimization" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                    SEO optimization
                </a>
                <a href="#next-steps" class="block text-waterloo-900 font-alegreya text-base leading-6 hover:text-flamingo-400 transition-colors">
                    Next steps
                </a>
            </nav>
        </div>
    </aside>
</div>