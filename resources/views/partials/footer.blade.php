@php
    $links = [
        [
            'title' => 'Documentation',
            'links' => [
                'Start Selling' => '/category/start-selling',
                'Manage Catalog' => '/category/manage-catalog',
                'Handle Orders' => '/category/handle-orders',
                'Grow Store' => '/category/grow-store',
                'Improve UX' => '/category/improve-ux',
                'Stay Compliant' => '/category/stay-compliant',
            ],
        ],
        [
            'title' => 'Resources',
            'links' => [
                'Getting Started Guide' => '/docs/main/getting-started',
                'FAQ' => '/docs/main/faq',
                'Video Tutorials' => '/docs/main/videos',
                'Magento User Guide' => 'https://docs.magento.com/user-guide/',
                'Magento StackExchange' => 'https://magento.stackexchange.com/',
            ],
        ],
        [
             'title' => 'Community',
             'links' => [
                 'Magento Community' => 'https://community.magento.com/',
                 'Magento Forums' => 'https://community.magento.com/forums/',
                 'Magento Marketplace' => 'https://marketplace.magento.com/',
                 'Magento Blog' => 'https://magento.com/blog',
                 'GitHub' => 'https://github.com/magento/magento2',
             ]
         ],
        [
            'title' => 'Support',
            'links' => [
                'Contact Support' => '/support',
                'Report Issues' => '/issues',
                'Feature Requests' => '/feature-requests',
                'Developer Resources' => 'https://developer.adobe.com/commerce/',
                'System Requirements' => '/docs/main/requirements',
            ],
        ],
    ];
@endphp

<footer class="relative pt-12 bg-white border-t border-gray-200">
    <div class="max-w-screen-2xl mx-auto w-full px-8">
        <div>
            <a href="/" class="inline-flex items-center">
                <img class="h-8 w-8" src="/img/Mage-OSLogoMark.svg" alt="Magento" loading="lazy">
                <span class="ml-3 text-xl font-bold text-gray-900">Merchant Documentation</span>
            </a>
        </div>

        <div class="mt-6 grid grid-cols-12 md:gap-x-8 gap-y-12 sm:mt-12">
            <div class="col-span-12 lg:col-span-4">
                <p class="max-w-sm text-xs text-gray-700 sm:text-sm">Your comprehensive guide to managing and growing your Magento 2 store. From setting up your first product to advanced marketing strategies, we provide clear, actionable documentation for merchants of all experience levels.</p>
                <ul class="mt-6 flex items-center space-x-3">
                    <li>
                        <a href="https://github.com/magento/magento2" class="p-2 md:p-0">
                            <img class="w-6 h-6" src="/img/social/github.min.svg" alt="GitHub" width="24" height="24" loading="lazy">
                        </a>
                    </li>
                    <li>
                        <a href="https://community.magento.com/" class="p-2 md:p-0 text-gray-700 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="https://marketplace.magento.com/" class="p-2 md:p-0 text-gray-700 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 6.5a1 1 0 0 0-1.8-.6L12 12 4.8 5.9a1 1 0 0 0-1.6 1.2l8 10a1 1 0 0 0 1.6 0l8-10A1 1 0 0 0 21 6.5z"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            @foreach ($links as $column)
                <div class="text-xs col-span-6 md:col-span-3 lg:col-span-2">
                    <span class="uppercase text-gray-900 font-semibold">{{ $column['title'] }}</span>
                    <div class="mt-5">
                        <ul class="space-y-3.5 md:space-y-3 text-gray-700">
                            @foreach ($column['links'] as $title => $href)
                                <li>
                                    <a href="{{ $href }}" class="transition-colors hover:text-gray-900 py-1.5 md:py-1">{{ $title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-10 border-t pt-6 pb-16 border-gray-200">
            <p class="text-xs text-gray-700">
                © {{ now()->format('Y') }} Magento 2 Merchant Documentation
                Magento® is a registered trademark of Adobe Inc.
            </p>
            <p class="mt-6 text-xs text-gray-700">
                This documentation provides guidance for Magento 2 merchants and store owners. For developer documentation, visit the <a href="https://developer.adobe.com/commerce/" class="text-indigo-600 hover:text-indigo-800">official developer documentation</a>.
            </p>
        </div>
    </div>
</footer>
