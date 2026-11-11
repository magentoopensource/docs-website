{{-- Main Header - White header with logo and navigation from Figma --}}
<div class="sticky top-0 z-50 bg-white flex items-center justify-center h-16 w-full border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between w-full max-w-7xl xl:max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Magento Logo --}}
        <div class="flex items-center">
            <a href="/" class="inline-flex items-center no-underline" aria-label="Magento Merchant Docs home">
                {{-- Same logo build as the landing page and developer docs: mark, name, section label --}}
                <span class="inline-flex items-center gap-3">
                    <svg width="30" height="33" viewBox="0 0 30 33" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0" aria-hidden="true">
                        <path d="M0 4.06492H29.6763V31.8882C29.6763 32.502 29.1713 33 28.5487 33H1.12762C0.505079 33 0 32.502 0 31.8882V4.06492Z" fill="#34323A"/>
                        <path d="M1.26857 0H28.4078C29.1066 0 29.6763 0.561678 29.6763 1.25075V4.06492H0V1.25075C0 0.561678 0.569682 0 1.26857 0Z" fill="#C9C9C9"/>
                        <path d="M2.37269 3.0458C2.94031 3.0458 3.40046 2.59211 3.40046 2.03246C3.40046 1.47281 2.94031 1.01913 2.37269 1.01913C1.80506 1.01913 1.34491 1.47281 1.34491 2.03246C1.34491 2.59211 1.80506 3.0458 2.37269 3.0458Z" fill="#848484"/>
                        <path d="M5.28571 3.0458C5.85334 3.0458 6.31349 2.59211 6.31349 2.03246C6.31349 1.47281 5.85334 1.01913 5.28571 1.01913C4.71809 1.01913 4.25793 1.47281 4.25793 2.03246C4.25793 2.59211 4.71809 3.0458 5.28571 3.0458Z" fill="#848484"/>
                        <path d="M14.7883 7.46973L4.90405 13.0923V24.349L7.54104 25.8487V14.5978L14.7883 10.4692L22.0415 14.5978V25.8487L24.6785 24.349V13.0923L14.7883 7.46973Z" fill="#F1BC1B"/>
                        <path d="M16.0862 26.2367L14.7883 26.9779L13.4492 26.2135V14.233L10.178 16.0975V27.3485L13.4492 29.213L14.7883 29.9773L16.0862 29.2362L19.4045 27.3485V16.0975L16.0862 14.2098V26.2367Z" fill="#F1BC1B"/>
                    </svg>
                    <span class="text-xl font-bold text-charcoal leading-none">Magento</span>
                    <span class="hidden sm:inline-block text-[11px] font-semibold uppercase tracking-wider text-charcoal-300 border-l border-gray-300 pl-2.5 pt-1">Merchant Docs</span>
                </span>
            </a>
        </div>

        {{-- Mobile: search icon + burger, same as the landing page and developer docs --}}
        <div class="lg:hidden flex items-center gap-2">
        <button
            type="button"
            class="flex items-center justify-center w-[2.5rem] h-[2.5rem] rounded-lg hover:bg-off-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-opacity-50"
            aria-label="Search the documentation"
            id="mobile-header-search"
        >
            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 16C9.77498 15.9996 11.4988 15.4054 12.897 14.312L17.293 18.708L18.707 17.294L14.311 12.898C15.405 11.4997 15.9996 9.77544 16 8C16 3.589 12.411 0 8 0C3.589 0 0 3.589 0 8C0 12.411 3.589 16 8 16ZM8 2C11.309 2 14 4.691 14 8C14 11.309 11.309 14 8 14C4.691 14 2 11.309 2 8C2 4.691 4.691 2 8 2Z" fill="#F26423"/>
            </svg>
        </button>
        <button
            data-mobile-menu-toggle
            class="flex items-center justify-center w-[2.5rem] h-[2.5rem] text-charcoal hover:text-orange transition-all focus:outline-none focus:ring-2 focus:ring-orange"
            aria-label="Toggle navigation menu"
            aria-expanded="false"
        >
            {{-- Hamburger icon --}}
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        </div>

        {{-- Desktop Navigation and Search --}}
        <div class="hidden lg:flex items-center gap-8">
            {{-- Main Navigation --}}
            <nav class="flex flex-row gap-x-6 lg:gap-x-7 xl:gap-x-8 items-center justify-center py-1.5" aria-label="Documentation sections">
                <a href="/merchant/getting-started" class="font-inter-tight text-sm font-medium no-underline leading-none text-charcoal hover:text-orange transition-colors whitespace-nowrap">Getting Started</a>
                <a href="/merchant/start-selling" class="font-inter-tight text-sm font-medium no-underline leading-none text-charcoal hover:text-orange transition-colors whitespace-nowrap">Start Selling</a>
                <a href="/merchant/manage-catalog" class="font-inter-tight text-sm font-medium no-underline leading-none text-charcoal hover:text-orange transition-colors whitespace-nowrap">Manage Catalog</a>
                <a href="/merchant/handle-orders" class="font-inter-tight text-sm font-medium no-underline leading-none text-charcoal hover:text-orange transition-colors whitespace-nowrap">Handle Orders</a>
                <a href="/merchant" class="font-inter-tight text-sm font-medium no-underline leading-none text-charcoal hover:text-orange transition-colors whitespace-nowrap">More</a>
            </nav>

             {{-- Search Icon (triggers Algolia) --}}
            <button
                class="flex items-center justify-center w-[2.5rem] h-[2.5rem] rounded-lg hover:bg-off-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-opacity-50"
                aria-label="Search the documentation"
                id="header-search"
            >
                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 16C9.77498 15.9996 11.4988 15.4054 12.897 14.312L17.293 18.708L18.707 17.294L14.311 12.898C15.405 11.4997 15.9996 9.77544 16 8C16 3.589 12.411 0 8 0C3.589 0 0 3.589 0 8C0 12.411 3.589 16 8 16ZM8 2C11.309 2 14 4.691 14 8C14 11.309 11.309 14 8 14C4.691 14 2 11.309 2 8C2 4.691 4.691 2 8 2Z" fill="#F26423"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- Mobile Menu Overlay --}}
<div
    data-mobile-menu-overlay
    class="hidden fixed inset-0 bg-charcoal/80 z-40 lg:hidden transition-opacity duration-200"
    aria-hidden="true"
></div>

{{-- Mobile Menu Panel --}}
<div
    data-mobile-menu-panel
    aria-hidden="true"
    class="hidden fixed top-0 right-0 h-full w-[26rem] max-w-[90%] bg-white shadow-2xl z-50 lg:hidden overflow-y-auto transform translate-x-full transition-transform duration-300 ease-out border-t-4 border-yellow"
>
    <div class="flex flex-col h-full">
        {{-- Mobile Menu Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-gradient-to-br from-off-white to-white">
            <div class="text-xl font-bold text-charcoal m-0 font-inter-tight">Menu</div>
            <button
                data-mobile-menu-close
                class="flex items-center justify-center w-10 h-10 text-charcoal hover:text-orange hover:bg-off-white rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                aria-label="Close navigation menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Mobile Navigation Links --}}
        <nav class="flex flex-col py-2" role="navigation" aria-label="Main navigation">
            <a href="/merchant/getting-started" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Getting Started</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
            <a href="/merchant/start-selling" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Start Selling</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
            <a href="/merchant/manage-catalog" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Manage Catalog</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
            <a href="/merchant/handle-orders" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Handle Orders</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
            <a href="/merchant/grow-store" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Grow Store</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
            <a href="/merchant/improve-ux" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Improve UX</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
            <a href="/merchant/reports-and-analytics" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Reports &amp; Analytics</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
            <a href="/merchant/stay-compliant" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Stay Compliant</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
            <a href="/merchant/support-and-resources" class="group relative px-6 py-4 font-inter-tight text-base font-medium text-charcoal hover:bg-gradient-to-r hover:from-off-white hover:to-white hover:text-orange transition-all duration-200 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange active:bg-yellow/10">
                <span class="relative z-10">Support &amp; Resources</span>
                <span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span>
            </a>
        </nav>

        {{-- Mobile Search --}}
        <div class="px-6 py-4 mt-auto border-t border-gray-200">
            <button
                id="mobile-menu-search"
                class="w-full flex items-center gap-3 px-4 py-3 bg-off-white hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                aria-label="Search the documentation"
            >
                <svg class="w-5 h-5 text-orange flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span class="text-gray-500 font-inter-tight text-base">Search documentation...</span>
                <kbd class="hidden sm:inline-flex ml-auto px-2 py-1 text-xs font-mono bg-white border border-gray-300 text-gray-400">⌘K</kbd>
            </button>
        </div>
    </div>
</div>
