{{-- Frame 53 Header Component from Figma --}}
<header class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Left Section: Logo + Menu Items --}}
            <div class="flex items-center gap-8">
                {{-- Logo and Title --}}
                <div class="flex items-center gap-4 w-[269px]">
                    <div class="h-[35px] w-[30px] flex-shrink-0">
                        <img src="/img/frame53-logo.svg" alt="Magento" class="h-full w-full">
                    </div>
                    <div class="font-alegreya font-medium text-waterloo-900 text-base leading-6 whitespace-nowrap">
                        Magento Open Source Docs
                    </div>
                </div>

                {{-- Menu Items --}}
                <nav class="hidden lg:flex items-center gap-[30px] font-alegreya text-waterloo-900 text-base">
                    <a href="#" class="hover:text-flamingo-400 transition-colors duration-200 py-2.5">
                        Menu item
                    </a>
                    <a href="#" class="hover:text-flamingo-400 transition-colors duration-200 py-2.5">
                        Menu item
                    </a>
                    <a href="#" class="hover:text-flamingo-400 transition-colors duration-200 py-2.5">
                        Menu item
                    </a>
                    <a href="#" class="hover:text-flamingo-400 transition-colors duration-200 py-2.5">
                        Menu item
                    </a>
                </nav>
            </div>

            {{-- Right Section: Search Bar --}}
            <div class="flex items-center gap-[30px] w-[368px] justify-end pl-2.5 py-2.5">
                <div class="flex-1 bg-white border border-flamingo-200 rounded-[50px] px-5 py-2.5 relative">
                    <div class="flex items-center justify-between w-full">
                        <button 
                            id="docsearch" 
                            class="font-alegreya text-blackrock-900 text-base leading-6 bg-transparent border-none outline-none flex-1 text-left focus:outline-none"
                        >
                            Search the documentation
                        </button>
                        <div class="w-6 h-6 flex-shrink-0 ml-2">
                            <img src="/img/frame53-search.svg" alt="Search" class="w-full h-full">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile menu button --}}
            <div class="lg:hidden">
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-waterloo-900 hover:text-flamingo-400 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-flamingo-400">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu (hidden by default) --}}
    <div class="lg:hidden" id="mobile-menu" style="display: none;">
        <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
            <a href="#" class="block px-3 py-2 text-base font-alegreya text-waterloo-900 hover:text-flamingo-400 hover:bg-gray-50 rounded-md">
                Menu item
            </a>
            <a href="#" class="block px-3 py-2 text-base font-alegreya text-waterloo-900 hover:text-flamingo-400 hover:bg-gray-50 rounded-md">
                Menu item
            </a>
            <a href="#" class="block px-3 py-2 text-base font-alegreya text-waterloo-900 hover:text-flamingo-400 hover:bg-gray-50 rounded-md">
                Menu item
            </a>
            <a href="#" class="block px-3 py-2 text-base font-alegreya text-waterloo-900 hover:text-flamingo-400 hover:bg-gray-50 rounded-md">
                Menu item
            </a>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.querySelector('[aria-label="Open main menu"]')?.parentElement;
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            const isHidden = mobileMenu.style.display === 'none';
            mobileMenu.style.display = isHidden ? 'block' : 'none';
        });
    }
});
</script>