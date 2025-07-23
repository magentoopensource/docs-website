{{-- Frame 54 Header Component - Enhanced version with active states --}}
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

                {{-- Enhanced Menu Items with Active States --}}
                <nav class="hidden lg:flex items-center gap-[30px] font-alegreya text-waterloo-900 text-base">
                    <a href="/docs" 
                       class="py-2.5 px-3 rounded-md transition-all duration-200 hover:text-flamingo-400 hover:bg-flamingo-50 {{ request()->is('docs*') ? 'text-flamingo-400 bg-flamingo-50 font-medium' : '' }}">
                        Documentation
                    </a>
                    <a href="/guides" 
                       class="py-2.5 px-3 rounded-md transition-all duration-200 hover:text-flamingo-400 hover:bg-flamingo-50 {{ request()->is('guides*') ? 'text-flamingo-400 bg-flamingo-50 font-medium' : '' }}">
                        Guides
                    </a>
                    <a href="/tutorials" 
                       class="py-2.5 px-3 rounded-md transition-all duration-200 hover:text-flamingo-400 hover:bg-flamingo-50 {{ request()->is('tutorials*') ? 'text-flamingo-400 bg-flamingo-50 font-medium' : '' }}">
                        Tutorials
                    </a>
                    <a href="/api" 
                       class="py-2.5 px-3 rounded-md transition-all duration-200 hover:text-flamingo-400 hover:bg-flamingo-50 {{ request()->is('api*') ? 'text-flamingo-400 bg-flamingo-50 font-medium' : '' }}">
                        API Reference
                    </a>
                </nav>
            </div>

            {{-- Right Section: Enhanced Search Bar --}}
            <div class="flex items-center gap-[30px] w-[368px] justify-end pl-2.5 py-2.5">
                <div class="flex-1 bg-white border border-flamingo-200 rounded-[50px] px-5 py-2.5 relative transition-all duration-200 hover:border-flamingo-400 focus-within:border-flamingo-400 focus-within:ring-2 focus-within:ring-flamingo-100">
                    <div class="flex items-center justify-between w-full">
                        <button 
                            id="docsearch" 
                            class="font-alegreya text-blackrock-900 text-base leading-6 bg-transparent border-none outline-none flex-1 text-left focus:outline-none"
                        >
                            Search the documentation
                        </button>
                        <div class="w-6 h-6 flex-shrink-0 ml-2 transition-transform duration-200 hover:scale-110">
                            <img src="/img/frame53-search.svg" alt="Search" class="w-full h-full">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enhanced Mobile menu button --}}
            <div class="lg:hidden">
                <button 
                    type="button" 
                    id="mobile-menu-button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-waterloo-900 hover:text-flamingo-400 hover:bg-flamingo-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-flamingo-400 transition-all duration-200"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                >
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6 block" id="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6 hidden" id="close-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Enhanced Mobile menu with animations --}}
    <div class="lg:hidden hidden" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200 shadow-lg">
            <a href="/docs" 
               class="block px-3 py-2 text-base font-alegreya text-waterloo-900 hover:text-flamingo-400 hover:bg-flamingo-50 rounded-md transition-all duration-200 {{ request()->is('docs*') ? 'text-flamingo-400 bg-flamingo-50 font-medium' : '' }}">
                Documentation
            </a>
            <a href="/guides" 
               class="block px-3 py-2 text-base font-alegreya text-waterloo-900 hover:text-flamingo-400 hover:bg-flamingo-50 rounded-md transition-all duration-200 {{ request()->is('guides*') ? 'text-flamingo-400 bg-flamingo-50 font-medium' : '' }}">
                Guides
            </a>
            <a href="/tutorials" 
               class="block px-3 py-2 text-base font-alegreya text-waterloo-900 hover:text-flamingo-400 hover:bg-flamingo-50 rounded-md transition-all duration-200 {{ request()->is('tutorials*') ? 'text-flamingo-400 bg-flamingo-50 font-medium' : '' }}">
                Tutorials
            </a>
            <a href="/api" 
               class="block px-3 py-2 text-base font-alegreya text-waterloo-900 hover:text-flamingo-400 hover:bg-flamingo-50 rounded-md transition-all duration-200 {{ request()->is('api*') ? 'text-flamingo-400 bg-flamingo-50 font-medium' : '' }}">
                API Reference
            </a>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced mobile menu functionality
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');
    
    if (mobileMenuButton && mobileMenu && menuIcon && closeIcon) {
        mobileMenuButton.addEventListener('click', function() {
            const isHidden = mobileMenu.classList.contains('hidden');
            
            if (isHidden) {
                // Show menu
                mobileMenu.classList.remove('hidden');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'true');
                
                // Add smooth slide-in animation
                mobileMenu.style.transform = 'translateY(-10px)';
                mobileMenu.style.opacity = '0';
                setTimeout(() => {
                    mobileMenu.style.transition = 'all 0.2s ease-out';
                    mobileMenu.style.transform = 'translateY(0)';
                    mobileMenu.style.opacity = '1';
                }, 10);
            } else {
                // Hide menu
                mobileMenu.style.transition = 'all 0.2s ease-in';
                mobileMenu.style.transform = 'translateY(-10px)';
                mobileMenu.style.opacity = '0';
                
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                    mobileMenu.style.transition = '';
                    mobileMenu.style.transform = '';
                    mobileMenu.style.opacity = '';
                }, 200);
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenuButton.click();
                }
            }
        });
    }
    
    // Enhanced search focus effects
    const searchButton = document.getElementById('docsearch');
    if (searchButton) {
        searchButton.addEventListener('focus', function() {
            this.closest('.border-flamingo-200').classList.add('border-flamingo-400', 'ring-2', 'ring-flamingo-100');
        });
        
        searchButton.addEventListener('blur', function() {
            this.closest('.border-flamingo-400').classList.remove('border-flamingo-400', 'ring-2', 'ring-flamingo-100');
        });
    }
    
    // Smooth scroll behavior for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<style>
/* Enhanced hover effects for Frame 54 */
.frame54-nav-item {
    position: relative;
}

.frame54-nav-item::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    width: 0;
    height: 2px;
    background-color: #f27945;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.frame54-nav-item:hover::after,
.frame54-nav-item.active::after {
    width: 100%;
}

/* Enhanced search bar glow effect */
.frame54-search:focus-within {
    box-shadow: 0 0 0 3px rgba(242, 121, 69, 0.1);
}

/* Improved mobile menu animations */
#mobile-menu {
    transform-origin: top;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-down {
    animation: slideDown 0.2s ease-out;
}
</style>