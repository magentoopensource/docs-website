@php
    $links = [
        [
            'title' => 'Documentation',
            'links' => [
                'Start Selling' => '/merchant/start-selling',
                'Manage Catalog' => '/merchant/manage-catalog',
                'Handle Orders' => '/merchant/handle-orders',
                'Grow Store' => '/merchant/grow-store',
                'Improve UX' => '/merchant/improve-ux',
                'Stay Compliant' => '/merchant/stay-compliant',
            ],
        ],
        [
            'title' => 'Resources',
            'links' => [
                'Getting Started' => '/merchant/getting-started',
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
             ],
         ],
        [
            'title' => 'Support',
            'links' => [
                'Contact Support' => 'https://github.com/magentoopensource/docs/issues',
                'Report Issues' => 'https://github.com/magentoopensource/docs/issues',
                'Community Resources' => 'https://community.magento.com/',
            ],
        ],
    ];
@endphp

<footer class="relative pt-12 bg-white border-t border-charcoal">
    <div class="max-w-7xl mx-auto w-full px-8">
        <div>
            <a href="/" class="inline-flex items-center">
                <svg width="30" height="33" viewBox="0 0 30 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 4.06492H29.6763V31.8882C29.6763 32.502 29.1713 33 28.5487 33H1.12762C0.505079 33 0 32.502 0 31.8882V4.06492Z" fill="#34323A"/>
                    <path d="M1.26857 0H28.4078C29.1066 0 29.6763 0.561678 29.6763 1.25075V4.06492H0V1.25075C0 0.561678 0.569682 0 1.26857 0Z" fill="#C9C9C9"/>
                    <path d="M2.37269 3.0458C2.94031 3.0458 3.40046 2.59211 3.40046 2.03246C3.40046 1.47281 2.94031 1.01913 2.37269 1.01913C1.80506 1.01913 1.34491 1.47281 1.34491 2.03246C1.34491 2.59211 1.80506 3.0458 2.37269 3.0458Z" fill="#848484"/>
                    <path d="M5.28571 3.0458C5.85334 3.0458 6.31349 2.59211 6.31349 2.03246C6.31349 1.47281 5.85334 1.01913 5.28571 1.01913C4.71809 1.01913 4.25793 1.47281 4.25793 2.03246C4.25793 2.59211 4.71809 3.0458 5.28571 3.0458Z" fill="#848484"/>
                    <path d="M14.7883 7.46973L4.90405 13.0923V24.349L7.54104 25.8487V14.5978L14.7883 10.4692L22.0415 14.5978V25.8487L24.6785 24.349V13.0923L14.7883 7.46973Z" fill="#F1BC1B"/>
                    <path d="M16.0862 26.2367L14.7883 26.9779L13.4492 26.2135V14.233L10.178 16.0975V27.3485L13.4492 29.213L14.7883 29.9773L16.0862 29.2362L19.4045 27.3485V16.0975L16.0862 14.2098V26.2367Z" fill="#F1BC1B"/>
                </svg>

                <span class="ml-3 text-xl font-inter-tight font-bold text-charcoal">Merchant Documentation</span> </a>
        </div>

        <div class="mt-6 grid grid-cols-12 md:gap-x-8 gap-y-12 sm:mt-12">
            <div class="col-span-12 lg:col-span-4">
                <p class="max-w-sm text-xs font-inter-tight text-charcoal sm:text-sm">Your comprehensive guide to managing and growing your Magento 2 store. From setting up your first product to advanced marketing strategies, we provide clear, actionable documentation for merchants of all experience levels.</p>
                <ul class="mt-6 flex items-center space-x-3">
                    <li>
                        <a href="https://github.com/magento/magento2" class="p-2 md:p-0">
                            <img class="w-6 h-6" src="/img/social/github.min.svg" alt="GitHub" width="24" height="24" loading="lazy">
                        </a>
                    </li>
                </ul>
            </div>
            @foreach ($links as $column)
                <div class="text-xs col-span-6 md:col-span-3 lg:col-span-2">
                    <span class="uppercase font-inter-tight text-charcoal font-semibold">{{ $column['title'] }}</span>
                    <div class="mt-6">
                        <ul class="space-y-3.5 md:space-y-3 font-inter-tight text-gray-darkest list-none">
                            @foreach ($column['links'] as $title => $href)
                                <li>
                                    <a href="{{ $href }}" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white rounded px-2 -mx-2 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2">{{ $title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-12 border-t pt-6 pb-16 border-charcoal">
            <p class="text-xs font-inter-tight text-charcoal">
                © {{ now()->format('Y') }} Magento 2 Merchant Documentation
                Magento® is a registered trademark of Adobe Inc.
            </p>
            <p class="mt-6 text-xs font-inter-tight text-charcoal">
                This documentation provides guidance for Magento 2 merchants and store owners. For developer documentation, visit the <a href="https://developer.adobe.com/commerce/" class="text-red hover:text-orange transition-colors">official developer documentation</a>.
            </p>
        </div>
    </div>
</footer>
