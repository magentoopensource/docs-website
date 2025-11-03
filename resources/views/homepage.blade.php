@extends('partials.layout')

@section('content')
    {{-- Hero Section - Commerce begins with Community --}}
    <section class="bg-white flex flex-col items-center justify-center py-12 sm:pt-16 md:pt-20 lg:pt-24 gap-8 sm:gap-12 md:gap-16 pb-0 mb-0">
        {{-- Main Hero Content --}}
        <div class="flex flex-col items-start justify-start gap-5 text-mine-shaft-500">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-inter-tight font-normal leading-none text-center sm:text-left">
                Commerce begins with Community.
            </h1>
            <p class="text-lg sm:text-xl font-inter-tight font-normal leading-[1.4] text-center w-full">
                Your comprehensive guide to building, managing, and growing a successful Magento 2 store.
            </p>
        </div>

        <div class="w-full">
            <svg width="1728" height="680" viewBox="0 0 1728 680" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                <rect x="759" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="779" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="799" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="819" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="839" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="859" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="879" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="899" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="919" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="939" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="959" y="191" width="10" height="190" fill="#2C2C2C"/>
                <path d="M859.833 80.2274C862.621 78.7439 865.967 78.7438 868.756 80.2274L869.032 80.3807L966.327 136.372C969.273 138.067 971.088 141.207 971.088 144.605V256.564C971.088 259.963 969.273 263.102 966.327 264.798L869.032 320.788C866.099 322.476 862.489 322.476 859.556 320.788L762.262 264.798C759.316 263.103 757.5 259.963 757.5 256.564V144.605C757.5 141.207 759.316 138.067 762.262 136.372L859.556 80.3807L859.833 80.2274ZM805.131 166.552V234.616L864.294 268.663L923.458 234.615V166.553L864.294 132.505L805.131 166.552Z" fill="#FAFAFA" stroke="#FAFAFA" stroke-width="11"/>
                <path d="M963.289 57.1386C964.53 57.8524 965.294 59.1744 965.294 60.6055V172.564C965.294 173.995 964.53 175.317 963.289 176.031L865.995 232.021C864.76 232.732 863.24 232.732 862.005 232.021L764.711 176.031C763.47 175.317 762.706 173.995 762.706 172.564V60.6055C762.706 59.1744 763.47 57.8524 764.711 57.1386L862.005 1.14808C863.24 0.437294 864.76 0.437294 865.995 1.14808L963.289 57.1386ZM801.341 78.2187C800.101 78.9325 799.337 80.2546 799.337 81.6856V151.483C799.337 152.915 800.101 154.237 801.341 154.95L862.005 189.861C863.24 190.572 864.76 190.572 865.995 189.861L926.658 154.95C927.899 154.237 928.663 152.915 928.663 151.483V81.6856C928.663 80.2546 927.899 78.9325 926.658 78.2187L865.995 43.3081C864.76 42.5973 863.24 42.5973 862.005 43.3081L801.341 78.2187Z" fill="#F1BC1B"/>
                <path d="M963.29 99.0441C964.53 99.7581 965.294 101.08 965.294 102.51V214.575C965.294 216.006 964.53 217.327 963.29 218.041L865.996 274.082C864.76 274.793 863.239 274.793 862.003 274.082L764.709 218.041C763.47 217.327 762.706 216.006 762.706 214.575V102.51C762.706 101.08 763.47 99.7581 764.709 99.0441L862.003 43.0035C863.239 42.2917 864.76 42.2917 865.996 43.0035L963.29 99.0441ZM801.34 120.143C800.1 120.857 799.337 122.179 799.337 123.609V193.476C799.337 194.906 800.1 196.228 801.34 196.942L862.003 231.884C863.239 232.595 864.76 232.595 865.996 231.884L926.66 196.942C927.899 196.228 928.663 194.906 928.663 193.476V123.609C928.663 122.179 927.899 120.857 926.66 120.143L865.996 85.2012C864.76 84.4894 863.239 84.4894 862.003 85.2012L801.34 120.143Z" fill="#F26423"/>
                <path d="M963.289 141.054C964.53 141.768 965.294 143.09 965.294 144.521V256.48C965.294 257.911 964.53 259.233 963.289 259.947L865.995 315.937C864.76 316.648 863.24 316.648 862.005 315.937L764.711 259.947C763.47 259.233 762.706 257.911 762.706 256.48V144.521C762.706 143.09 763.47 141.768 764.711 141.054L862.005 85.0639C863.24 84.3531 864.76 84.3531 865.995 85.0639L963.289 141.054ZM801.341 162.135C800.101 162.848 799.337 164.17 799.337 165.601V235.399C799.337 236.83 800.101 238.152 801.341 238.866L862.005 273.777C863.24 274.488 864.76 274.488 865.995 273.777L926.658 238.866C927.899 238.152 928.663 236.83 928.663 235.399V165.601C928.663 164.17 927.899 162.848 926.658 162.135L865.995 127.224C864.76 126.513 863.24 126.513 862.005 127.224L801.341 162.135Z" fill="#2C2C2C"/>
                <rect x="1728" y="670" width="10.0001" height="1728" transform="rotate(90 1728 670)" fill="#2C2C2C"/>
                <path d="M759 381H769V571H759V381Z" fill="#2C2C2C"/>
                <path d="M779 381H789V601H779V381Z" fill="#2C2C2C"/>
                <path d="M799 381H809V621H799V381Z" fill="#2C2C2C"/>
                <path d="M819 381H829V641H819V381Z" fill="#2C2C2C"/>
                <path d="M839 381H849V661H839V381Z" fill="#2C2C2C"/>
                <path d="M859 381H869V680H859V381Z" fill="#2C2C2C"/>
                <path d="M879 381H889V661H879V381Z" fill="#2C2C2C"/>
                <path d="M899 381H909V641H899V381Z" fill="#2C2C2C"/>
                <path d="M919 381H929V621H919V381Z" fill="#2C2C2C"/>
                <path d="M939 381H949V601H939V381Z" fill="#2C2C2C"/>
                <path d="M959 381H969V571H959V381Z" fill="#2C2C2C"/>
                <path d="M1728 571V581L959 581V571H1728Z" fill="#2C2C2C"/>
                <path d="M769 571V581L0 581L4.37116e-07 571L769 571Z" fill="#2C2C2C"/>
                <path d="M789 591V601H0L4.37116e-07 591L789 591Z" fill="#2C2C2C"/>
                <path d="M849 651V661H0L4.37116e-07 651L849 651Z" fill="#2C2C2C"/>
                <path d="M829 631V641H0L4.37116e-07 631L829 631Z" fill="#2C2C2C"/>
                <path d="M809 611V621H0L4.37116e-07 611L809 611Z" fill="#2C2C2C"/>
                <path d="M1728 591V601L939 601V591L1728 591Z" fill="#2C2C2C"/>
                <path d="M1728 651V661L879 661V651L1728 651Z" fill="#2C2C2C"/>
                <path d="M1728 631V641L899 641V631L1728 631Z" fill="#2C2C2C"/>
                <path d="M1728 611V621L919 621V611L1728 611Z" fill="#2C2C2C"/>
            </svg>

        </div>
    </section>

    {{-- Search Section with Search Content --}}
    <section class="relative flex flex-col gap-2.5 items-center justify-start">
        {{-- Search Content --}}
        <div class="flex flex-col items-center justify-start gap-6 sm:gap-8 md:gap-10 px-6 sm:px-8 md:px-12 py-12 sm:py-16 md:py-20 lg:py-24 relative w-full ">
            {{-- Search Header --}}
            <div class="flex flex-col items-center justify-start gap-6 sm:gap-8 w-full">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-inter-tight font-bold leading-none text-mine-shaft-500 text-center">
                    How can we help you today?
                </h2>
                <p class="text-lg sm:text-xl font-inter-tight font-medium leading-[1.4] text-center text-mine-shaft-500 max-w-4xl">
                    Find step-by-step guides, best practices, and expert tips to unlock your store's full potential.
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="px-6 sm:px-12 md:px-24 lg:px-48 xl:px-64 w-full">
                <button class="flex flex-col gap-3 sm:gap-4 w-full pt-3 sm:pt-4 cursor-pointer hover:bg-alabaster-500 transition-colors duration-200 rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-figma-orange-500 focus:ring-opacity-50" id="homepage-search">
                    <div class="flex items-center justify-between w-full">
                        <span class="text-base sm:text-lg font-inter-tight leading-[1.42] text-mine-shaft-500">
                            Search the documentation
                        </span>
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 16C9.77498 15.9996 11.4988 15.4054 12.897 14.312L17.293 18.708L18.707 17.294L14.311 12.898C15.405 11.4997 15.9996 9.77544 16 8C16 3.589 12.411 0 8 0C3.589 0 0 3.589 0 8C0 12.411 3.589 16 8 16ZM8 2C11.309 2 14 4.691 14 8C14 11.309 11.309 14 8 14C4.691 14 2 11.309 2 8C2 4.691 4.691 2 8 2Z" fill="#F26423"/>
                        </svg>

                    </div>
                    <div class="bg-mine-shaft-500 h-1 w-full"></div>
                </button>
            </div>
        </div>
    </section>

    {{-- Categories Grid Section --}}
    <section class="flex flex-col items-center justify-start gap-8 sm:gap-10 md:gap-12 px-6 sm:px-12 md:px-16 lg:px-24 xl:px-32 py-12 sm:py-16 md:py-20 lg:py-24">
        <div class="flex flex-col items-center justify-start gap-6 w-full">
            {{-- Top Row Categories --}}
            <div class="flex flex-col lg:flex-row gap-6 sm:gap-8 lg:gap-[30px] items-stretch justify-start w-full">
                {{-- Start Selling --}}
                <div class="flex-1 bg-white flex flex-col gap-[30px] items-start justify-start p-6 pt-8 pb-6 rounded border-t-4 border-lightning-yellow-500 shadow-lg hover:shadow-xl transition-all duration-300 hover:border-figma-orange-500 focus-within:ring-2 focus-within:ring-figma-orange-500 focus-within:ring-opacity-20">
                    <div class="flex items-center justify-start gap-[26px] w-full">
                        <h3 class="text-3xl font-inter-tight font-normal leading-[1.2] text-mine-shaft-500">
                            Start selling
                        </h3>
                    </div>
                    <div class="flex flex-col items-start justify-start gap-[15px] w-full">
                        <p class="text-base font-inter-tight font-normal leading-[1.5] text-mine-shaft-500">
                            Get your store up and running with your first products, payments and shipping setup.
                        </p>
                    </div>

                    {{-- Link Boxes --}}
                    <div class="flex flex-col gap-2 items-start justify-start w-full">
                        @foreach([
                            ['title' => 'Simple products', 'desc' => 'How to create your first simple product.'],
                            ['title' => 'Shipping rates', 'desc' => 'Set up shipping rates by country or region.'],
                            ['title' => 'Credit card payments', 'desc' => 'Enable credit card payments securely.'],
                            ['title' => 'Free shipping', 'desc' => 'Add free shipping over a minimum cart value']
                        ] as $link)
                            <a href="/docs/start-selling" class="bg-white flex flex-col gap-4 items-center justify-start pb-5 w-full hover:bg-alabaster-500 transition-colors duration-200 rounded-lg p-2 -mx-2">
                                <div class="bg-alabaster-600 h-1 w-full"></div>
                                <div class="flex gap-2.5 items-start justify-start px-2 w-full">
                                    <div class="w-6 h-6 flex-shrink-0">
                                        @if($link['title'] === 'Simple products')
                                            <img src="{{ asset('img/icons/simple-products.svg') }}" alt="Simple products icon" class="w-full h-full">
                                        @elseif($link['title'] === 'Shipping rates')
                                            <img src="{{ asset('img/icons/shipping-rates.svg') }}" alt="Shipping rates icon" class="w-full h-full">
                                        @elseif($link['title'] === 'Credit card payments')
                                            <img src="{{ asset('img/icons/credit-card.svg') }}" alt="Credit card icon" class="w-full h-full">
                                        @elseif($link['title'] === 'Free shipping')
                                            <img src="{{ asset('img/icons/free-shipping.svg') }}" alt="Free shipping icon" class="w-full h-full">
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-start justify-center flex-1">
                                        <div class="text-base font-inter-tight leading-[1.5] text-mine-shaft-500">
                                            {{ $link['title'] }}
                                        </div>
                                        <div class="text-xs font-inter-tight leading-[1.333] text-mine-shaft-400">
                                            {{ $link['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="/docs/start-selling" class="text-base font-inter-tight leading-[1.5] text-figma-orange-700 underline decoration-solid"> See all in this section </a>
                </div>

                {{-- Manage Catalog --}}
                <div class="flex-1 bg-white flex flex-col gap-[30px] items-start justify-start p-6 pt-8 pb-6 rounded border-t-4 border-lightning-yellow-500 shadow-lg hover:shadow-xl transition-all duration-300 hover:border-figma-orange-500 focus-within:ring-2 focus-within:ring-figma-orange-500 focus-within:ring-opacity-20">
                    <div class="flex items-center justify-start gap-[26px] w-full">
                        <h3 class="text-3xl font-inter-tight font-normal leading-[1.2] text-mine-shaft-500">
                            Manage catalog
                        </h3>
                    </div>
                    <div class="flex flex-col items-start justify-start gap-[15px] w-full">
                        <p class="text-base font-inter-tight font-normal leading-[1.5] text-mine-shaft-500">
                            Organise and maintain your product catalog with bulk operations and smart workflows.
                        </p>
                    </div>

                    {{-- Link Boxes --}}
                    <div class="flex flex-col gap-2 items-start justify-start w-full">
                        @foreach([
                            ['title' => 'Import products', 'desc' => 'Bulk import products via CSV'],
                            ['title' => 'Images', 'desc' => 'How to update product images quickly'],
                            ['title' => 'Variants', 'desc' => 'Create configurable products with variants'],
                            ['title' => 'Categories', 'desc' => 'Organise products into categories efficiently']
                        ] as $link)
                            <a href="/docs/manage-catalog" class="bg-white flex flex-col gap-4 items-center justify-start pb-5 w-full hover:bg-alabaster-500 transition-colors duration-200 rounded-lg p-2 -mx-2">
                                <div class="bg-alabaster-600 h-1 w-full"></div>
                                <div class="flex gap-2.5 items-start justify-start px-2 w-full">
                                    <div class="w-6 h-6 flex-shrink-0">
                                        @if($link['title'] === 'Import products')
                                            <img src="{{ asset('img/icons/import-products.svg') }}" alt="Import products icon" class="w-full h-full">
                                        @elseif($link['title'] === 'Images')
                                            <img src="{{ asset('img/icons/product-images.svg') }}" alt="Product images icon" class="w-full h-full">
                                        @elseif($link['title'] === 'Variants')
                                            <img src="{{ asset('img/icons/variants.svg') }}" alt="Variants icon" class="w-full h-full">
                                        @elseif($link['title'] === 'Categories')
                                            <img src="{{ asset('img/icons/categories.svg') }}" alt="Categories icon" class="w-full h-full">
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-start justify-center flex-1">
                                        <div class="text-base font-inter-tight leading-[1.5] text-mine-shaft-500">
                                            {{ $link['title'] }}
                                        </div>
                                        <div class="text-xs font-inter-tight leading-[1.333] text-mine-shaft-400">
                                            {{ $link['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="/docs/manage-catalog" class="text-base font-inter-tight leading-[1.5] text-figma-orange-700 underline decoration-solid"> See all in this section </a>
                </div>

                {{-- Handle Orders --}}
                <div class="flex-1 bg-white flex flex-col gap-[30px] items-start justify-start p-6 pt-8 pb-6 rounded border-t-4 border-lightning-yellow-500 shadow-lg hover:shadow-xl transition-all duration-300 hover:border-figma-orange-500 focus-within:ring-2 focus-within:ring-figma-orange-500 focus-within:ring-opacity-20">
                    <div class="flex items-center justify-start gap-[26px] w-full">
                        <h3 class="text-3xl font-inter-tight font-normal leading-[1.2] text-mine-shaft-500">
                            Handle orders
                        </h3>
                    </div>
                    <div class="flex flex-col items-start justify-start gap-[15px] w-full">
                        <p class="text-base font-inter-tight font-normal leading-[1.5] text-mine-shaft-500">
                            Process orders, manage fulfilment and handle customer service quickly and efficiently.
                        </p>
                    </div>

                    {{-- Link Boxes --}}
                    <div class="flex flex-col gap-2 items-start justify-start w-full">
                        @foreach([
                            ['title' => 'Orders', 'desc' => 'Process your first customer order'],
                            ['title' => 'Refunds', 'desc' => 'Issue a refund or partial refund'],
                            ['title' => 'Edit orders', 'desc' => 'Edit shipping details after purchase'],
                            ['title' => 'Label management', 'desc' => 'Print shipping labels directly from Magento']
                        ] as $link)
                            <a href="/docs/handle-orders" class="bg-white flex flex-col gap-4 items-center justify-start pb-5 w-full hover:bg-alabaster-500 transition-colors duration-200 rounded-lg p-2 -mx-2">
                                <div class="bg-alabaster-600 h-1 w-full"></div>
                                <div class="flex gap-2.5 items-start justify-start px-2 w-full">
                                    <div class="w-6 h-6 flex-shrink-0">
                                        @if($link['title'] === 'Orders')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Refunds')
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 3H3V21H21V19H5V3Z" fill="#F26423"/>
                                                <path d="M13 12.586L8.70697 8.29297L7.29297 9.70697L13 15.414L16 12.414L20.293 16.707L21.707 15.293L16 9.58597L13 12.586Z" fill="#F26423"/>
                                            </svg>
                                        @elseif($link['title'] === 'Edit orders')
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7 17.013L11.413 16.998L21.045 7.45802C21.423 7.08003 21.631 6.57802 21.631 6.04402C21.631 5.51002 21.423 5.00802 21.045 4.63002L19.459 3.04402C18.703 2.28802 17.384 2.29202 16.634 3.04102L7 12.583V17.013ZM18.045 4.45802L19.634 6.04102L18.037 7.62302L16.451 6.03802L18.045 4.45802ZM9 13.417L15.03 7.44402L16.616 9.03002L10.587 15.001L9 15.006V13.417Z" fill="#F26423"/>
                                                <path d="M5 21H19C20.103 21 21 20.103 21 19V10.332L19 12.332V19H8.158C8.132 19 8.105 19.01 8.079 19.01C8.046 19.01 8.013 19.001 7.979 19H5V5H11.847L13.847 3H5C3.897 3 3 3.897 3 5V19C3 20.103 3.897 21 5 21Z" fill="#F26423"/>
                                            </svg>

                                        @elseif($link['title'] === 'Label management')
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17 5V3C17 2.448 16.552 2 16 2H7C6.448 2 6 2.448 6 3V5C4.346 5 3 6.346 3 8V18C3 19.654 4.346 21 6 21H17C18.654 21 20 19.654 20 18V8C20 6.346 18.654 5 17 5ZM8 4H15V9H8V4ZM6 7V10C6 10.552 6.448 11 7 11H16C16.552 11 17 10.552 17 10V7C17.551 7 18 7.449 18 8V10.5C18 11.327 17.327 12 16.5 12H6.5C5.673 12 5 11.327 5 10.5V8C5 7.449 5.449 7 6 7ZM17 19H6C5.449 19 5 18.551 5 18V12.487C5.419 12.805 5.935 13 6.5 13H16.5C17.065 13 17.581 12.805 18 12.487V18C18 18.551 17.551 19 17 19ZM13.5 7H9.5C9.224 7 9 7.224 9 7.5C9 7.776 9.224 8 9.5 8H13.5C13.776 8 14 7.776 14 7.5C14 7.224 13.776 7 13.5 7ZM15 16H8C7.724 16 7.5 16.224 7.5 16.5C7.5 16.776 7.724 17 8 17H15C15.276 17 15.5 16.776 15.5 16.5C15.5 16.224 15.276 16 15 16ZM13.5 5H9.5C9.224 5 9 5.224 9 5.5C9 5.776 9.224 6 9.5 6H13.5C13.776 6 14 5.776 14 5.5C14 5.224 13.776 5 13.5 5Z" fill="#F26423"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-start justify-center flex-1">
                                        <div class="text-base font-inter-tight leading-[1.5] text-mine-shaft-500">
                                            {{ $link['title'] }}
                                        </div>
                                        <div class="text-xs font-inter-tight leading-[1.333] text-mine-shaft-400">
                                            {{ $link['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="/docs/handle-orders" class="text-base font-inter-tight leading-[1.5] text-figma-orange-700 underline decoration-solid"> See all in this section </a>
                </div>
            </div>

            {{-- Bottom Row Categories --}}
            <div class="flex flex-col lg:flex-row gap-6 sm:gap-8 lg:gap-[30px] items-stretch justify-start w-full">
                {{-- Grow Your Store --}}
                <div class="flex-1 bg-white flex flex-col gap-[30px] items-start justify-start p-6 pt-8 pb-6 rounded border-t-4 border-lightning-yellow-500 shadow-lg hover:shadow-xl transition-all duration-300 hover:border-figma-orange-500 focus-within:ring-2 focus-within:ring-figma-orange-500 focus-within:ring-opacity-20">
                    <div class="flex items-center justify-start gap-[26px] w-full">
                        <h3 class="text-3xl font-inter-tight font-normal leading-[1.2] text-mine-shaft-500">
                            Grow your store
                        </h3>
                    </div>
                    <div class="flex flex-col items-start justify-start gap-[15px] w-full">
                        <p class="text-base font-inter-tight font-normal leading-[1.5] text-mine-shaft-500">
                            Scale your business with marketing tools, analytics and customer retention strategies.
                        </p>
                    </div>

                    {{-- Link Boxes --}}
                    <div class="flex flex-col gap-2 items-start justify-start w-full">
                        @foreach([
                            ['title' => 'Analytics', 'desc' => 'Track sales performance and customer behavior'],
                            ['title' => 'Marketing tools', 'desc' => 'Set up email campaigns and promotions'],
                            ['title' => 'Customer retention', 'desc' => 'Build loyalty programs and repeat purchases'],
                            ['title' => 'Social media', 'desc' => 'Connect social channels to drive traffic']
                        ] as $link)
                            <a href="/docs/grow-store" class="bg-white flex flex-col gap-4 items-center justify-start pb-5 w-full hover:bg-alabaster-500 transition-colors duration-200 rounded-lg p-2 -mx-2">
                                <div class="bg-alabaster-600 h-1 w-full"></div>
                                <div class="flex gap-2.5 items-start justify-start px-2 w-full">
                                    <div class="w-6 h-6 flex-shrink-0">
                                        @if($link['title'] === 'Analytics')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Marketing tools')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Customer retention')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Social media')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-start justify-center flex-1">
                                        <div class="text-base font-inter-tight leading-[1.5] text-mine-shaft-500">
                                            {{ $link['title'] }}
                                        </div>
                                        <div class="text-xs font-inter-tight leading-[1.333] text-mine-shaft-400">
                                            {{ $link['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="/docs/grow-store" class="text-base font-inter-tight leading-[1.5] text-figma-orange-700 underline decoration-solid"> See all in this section </a>
                </div>

                {{-- Improve UX --}}
                <div class="flex-1 bg-white flex flex-col gap-[30px] items-start justify-start p-6 pt-8 pb-6 rounded border-t-4 border-lightning-yellow-500 shadow-lg hover:shadow-xl transition-all duration-300 hover:border-figma-orange-500 focus-within:ring-2 focus-within:ring-figma-orange-500 focus-within:ring-opacity-20">
                    <div class="flex items-center justify-start gap-[26px] w-full">
                        <h3 class="text-3xl font-inter-tight font-normal leading-[1.2] text-mine-shaft-500">
                            Improve UX
                        </h3>
                    </div>
                    <div class="flex flex-col items-start justify-start gap-[15px] w-full">
                        <p class="text-base font-inter-tight font-normal leading-[1.5] text-mine-shaft-500">
                            Enhance customer experience with design, navigation and performance optimizations.
                        </p>
                    </div>

                    {{-- Link Boxes --}}
                    <div class="flex flex-col gap-2 items-start justify-start w-full">
                        @foreach([
                            ['title' => 'Theme design', 'desc' => 'Customize your store\'s visual appearance'],
                            ['title' => 'Navigation', 'desc' => 'Optimize menu structure and user flow'],
                            ['title' => 'Performance', 'desc' => 'Speed up page loading and response times'],
                            ['title' => 'Mobile optimization', 'desc' => 'Ensure seamless mobile shopping experience']
                        ] as $link)
                            <a href="/docs/improve-ux" class="bg-white flex flex-col gap-4 items-center justify-start pb-5 w-full hover:bg-alabaster-500 transition-colors duration-200 rounded-lg p-2 -mx-2">
                                <div class="bg-alabaster-600 h-1 w-full"></div>
                                <div class="flex gap-2.5 items-start justify-start px-2 w-full">
                                    <div class="w-6 h-6 flex-shrink-0">
                                        @if($link['title'] === 'Theme design')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Navigation')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                        @elseif($link['title'] === 'Performance')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Mobile optimization')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-start justify-center flex-1">
                                        <div class="text-base font-inter-tight leading-[1.5] text-mine-shaft-500">
                                            {{ $link['title'] }}
                                        </div>
                                        <div class="text-xs font-inter-tight leading-[1.333] text-mine-shaft-400">
                                            {{ $link['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="/docs/improve-ux" class="text-base font-inter-tight leading-[1.5] text-figma-orange-700 underline decoration-solid"> See all in this section </a>
                </div>

                {{-- Stay Compliant --}}
                <div class="flex-1 bg-white flex flex-col gap-[30px] items-start justify-start p-6 pt-8 pb-6 rounded border-t-4 border-lightning-yellow-500 shadow-lg hover:shadow-xl transition-all duration-300 hover:border-figma-orange-500 focus-within:ring-2 focus-within:ring-figma-orange-500 focus-within:ring-opacity-20">
                    <div class="flex items-center justify-start gap-[26px] w-full">
                        <h3 class="text-3xl font-inter-tight font-normal leading-[1.2] text-mine-shaft-500">
                            Stay compliant
                        </h3>
                    </div>
                    <div class="flex flex-col items-start justify-start gap-[15px] w-full">
                        <p class="text-base font-inter-tight font-normal leading-[1.5] text-mine-shaft-500">
                            Ensure your store meets legal and industry standards for data protection and commerce.
                        </p>
                    </div>

                    {{-- Link Boxes --}}
                    <div class="flex flex-col gap-2 items-start justify-start w-full">
                        @foreach([
                            ['title' => 'GDPR compliance', 'desc' => 'Implement data protection and privacy controls'],
                            ['title' => 'Tax settings', 'desc' => 'Configure tax rules for different regions'],
                            ['title' => 'Legal pages', 'desc' => 'Set up terms, privacy policy, and disclaimers'],
                            ['title' => 'Accessibility', 'desc' => 'Make your store accessible to all users']
                        ] as $link)
                            <a href="/docs/stay-compliant" class="bg-white flex flex-col gap-4 items-center justify-start pb-5 w-full hover:bg-alabaster-500 transition-colors duration-200 rounded-lg p-2 -mx-2">
                                <div class="bg-alabaster-600 h-1 w-full"></div>
                                <div class="flex gap-2.5 items-start justify-start px-2 w-full">
                                    <div class="w-6 h-6 flex-shrink-0">
                                        @if($link['title'] === 'GDPR compliance')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Tax settings')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3-4h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Legal pages')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @elseif($link['title'] === 'Accessibility')
                                            <svg class="w-full h-full text-figma-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-start justify-center flex-1">
                                        <div class="text-base font-inter-tight leading-[1.5] text-mine-shaft-500">
                                            {{ $link['title'] }}
                                        </div>
                                        <div class="text-xs font-inter-tight leading-[1.333] text-mine-shaft-400">
                                            {{ $link['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="/docs/stay-compliant" class="text-base font-inter-tight leading-[1.5] text-figma-orange-700 underline decoration-solid"> See all in this section </a>
                </div>
            </div>
        </div>
    </section>
@endsection