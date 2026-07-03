/*
 * Shared site header for the Developer Documentation pages.
 *
 * Mirrors the merchant docs header (ecosystem bar + sticky main header + mobile
 * menu) so a visitor inside the dev-docs knows they are on the Magento
 * Association site and can navigate the wider documentation set.
 *
 * The dev-docs pages are static HTML loading Tailwind via CDN, so the merchant
 * blade markup is adapted here: the merchant theme's custom utilities
 * (font-inter-tight, text-medium, max-w-8xl, w-logo-*) are replaced with classes
 * available in the dev-docs Tailwind config. The sticky main bar is fixed to
 * h-16 (64px) to line up with the existing `sticky top-16` quick-jump nav.
 *
 * This script is referenced by every dev-docs page as:
 *   <script src="includes/header.js"></script>
 */
(function () {
    // Magento box mark (icon only) + text wordmark + section label. The icon is
    // the recognisable Magento hexagon-in-box; the text identifies it as the
    // Magento Open Source Developer Documentation.
    var MAGENTO_LOGO = '' +
        '<span class="inline-flex items-center gap-2.5">' +
        '<svg class="h-8 w-auto flex-shrink-0" viewBox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Magento" role="img">' +
        '<path d="M0 4.06492H29.6763V31.8882C29.6763 32.502 29.1713 33 28.5487 33H1.12762C0.505079 33 0 32.502 0 31.8882V4.06492Z" fill="#34323A"/>' +
        '<path d="M1.26857 0H28.4078C29.1066 0 29.6763 0.561678 29.6763 1.25075V4.06492H0V1.25075C0 0.561678 0.569682 0 1.26857 0Z" fill="#C9C9C9"/>' +
        '<path d="M14.7883 7.46973L4.90405 13.0923V24.349L7.54104 25.8487V14.5978L14.7883 10.4692L22.0415 14.5978V25.8487L24.6785 24.349V13.0923L14.7883 7.46973Z" fill="#F1BC1B"/>' +
        '<path d="M16.0862 26.2367L14.7883 26.9779L13.4492 26.2135V14.233L10.178 16.0975V27.3485L13.4492 29.213L14.7883 29.9773L16.0862 29.2362L19.4045 27.3485V16.0975L16.0862 14.2098V26.2367Z" fill="#F1BC1B"/>' +
        '</svg>' +
        '<span class="text-xl font-bold text-charcoal leading-none">Magento</span>' +
        '<span class="hidden sm:inline-block text-[11px] font-semibold uppercase tracking-wider text-charcoal-300 border-l border-gray-300 pl-2.5 pt-1">Developer Docs</span>' +
        '</span>';

    var ECOSYSTEM_LINKS = [
        { label: 'Magento Open Source', href: 'https://github.com/magento/magento2' },
        { label: 'Magento Association', href: 'https://www.magentoassociation.org/home' },
        { label: 'Meet Magento', href: 'https://www.meet-magento.com/' },
        { label: 'Development Resources', href: 'https://devdocs.mage-os.org/' }
    ];

    var NAV_LINKS = [
        { label: 'Getting Started', href: '/merchant/getting-started' },
        { label: 'Start Selling', href: '/merchant/start-selling' },
        { label: 'Manage Catalog', href: '/merchant/manage-catalog' },
        { label: 'Handle Orders', href: '/merchant/handle-orders' },
        { label: 'More', href: '/merchant' }
    ];

    var arrowSvg = '<svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.75 11C1.50391 11 1.28516 10.918 1.12109 10.7539C0.765625 10.4258 0.765625 9.85156 1.12109 9.52344L4.86719 5.75L1.12109 2.00391C0.765625 1.67578 0.765625 1.10156 1.12109 0.773438C1.44922 0.417969 2.02344 0.417969 2.35156 0.773438L6.72656 5.14844C7.08203 5.47656 7.08203 6.05078 6.72656 6.37891L2.35156 10.7539C2.1875 10.918 1.96875 11 1.75 11Z" fill="#F26423"/></svg>';

    function ecosystemItems() {
        return ECOSYSTEM_LINKS.map(function (l, i) {
            var sep = i > 0 ? '<div class="h-10 w-px bg-gray-600"></div>' : '';
            return sep +
                '<div class="bg-charcoal flex gap-2.5 items-center justify-start px-5 py-2.5 text-sm">' +
                '<a href="' + l.href + '" target="_blank" rel="noopener" class="text-white font-bold no-underline hover:text-orange transition-colors duration-200">' + l.label + '</a>' +
                arrowSvg + '</div>';
        }).join('');
    }

    function desktopNav() {
        return NAV_LINKS.map(function (l) {
            return '<a href="' + l.href + '" class="text-sm font-medium no-underline leading-none text-charcoal hover:text-orange transition-colors whitespace-nowrap">' + l.label + '</a>';
        }).join('');
    }

    function mobileNav() {
        return NAV_LINKS.map(function (l) {
            return '<a href="' + l.href + '" class="group relative px-6 py-4 text-base font-medium text-charcoal hover:bg-off-white hover:text-orange transition-all duration-200 border-b border-gray-100 no-underline">' +
                '<span class="relative z-10">' + l.label + '</span>' +
                '<span class="absolute left-0 top-0 h-full w-1 bg-orange scale-y-0 group-hover:scale-y-100 transition-transform duration-200 origin-center"></span></a>';
        }).join('');
    }

    var html = '' +
        // Ecosystem bar (desktop only) — scrolls away with the page.
        '<div class="hidden lg:flex bg-charcoal items-center justify-center h-10 w-full">' +
            '<div class="max-w-7xl xl:max-w-[90rem] w-full flex items-center justify-between px-4 sm:px-6 lg:px-8">' +
                '<div class="text-sm leading-[1.42] text-white font-bold">Explore the Magento<span class="text-[9px] align-super">&reg;</span> Open Source Ecosystem</div>' +
                '<div class="flex items-center">' + ecosystemItems() + '</div>' +
            '</div>' +
        '</div>' +
        // Main header — sticky, fixed h-16 to align with `sticky top-16` quick-jump nav.
        '<div class="sticky top-0 z-50 bg-white flex items-center h-16 w-full border-b border-gray-200 shadow-sm">' +
            '<div class="flex items-center justify-between w-full max-w-7xl xl:max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">' +
                '<a href="/" class="inline-flex items-center" aria-label="Magento home">' + MAGENTO_LOGO + '</a>' +
                '<button data-mobile-menu-toggle class="lg:hidden flex items-center justify-center w-10 h-10 text-charcoal hover:text-orange transition-all focus:outline-none focus:ring-2 focus:ring-orange" aria-label="Toggle navigation menu" aria-expanded="false">' +
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>' +
                '</button>' +
                '<div class="hidden lg:flex items-center gap-8">' +
                    '<nav class="flex flex-row gap-x-6 lg:gap-x-7 xl:gap-x-8 items-center">' + desktopNav() + '</nav>' +
                '</div>' +
            '</div>' +
        '</div>' +
        // Mobile overlay + panel.
        '<div data-mobile-menu-overlay class="hidden fixed inset-0 bg-charcoal/80 z-40 lg:hidden transition-opacity duration-200" aria-hidden="true"></div>' +
        '<div data-mobile-menu-panel aria-hidden="true" class="hidden fixed top-0 right-0 h-full w-[26rem] max-w-[90%] bg-white shadow-2xl z-50 lg:hidden overflow-y-auto transform translate-x-full transition-transform duration-300 ease-out border-t-4 border-yellow">' +
            '<div class="flex flex-col h-full">' +
                '<div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-off-white">' +
                    '<h2 class="text-xl font-bold text-charcoal m-0">Menu</h2>' +
                    '<button data-mobile-menu-close class="flex items-center justify-center w-10 h-10 text-charcoal hover:text-orange hover:bg-off-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange" aria-label="Close navigation menu">' +
                        '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' +
                    '</button>' +
                '</div>' +
                '<nav class="flex flex-col py-2" role="navigation" aria-label="Main navigation">' + mobileNav() + '</nav>' +
            '</div>' +
        '</div>';

    function init() {
        document.body.insertAdjacentHTML('afterbegin', html);

        var toggle = document.querySelector('[data-mobile-menu-toggle]');
        var overlay = document.querySelector('[data-mobile-menu-overlay]');
        var panel = document.querySelector('[data-mobile-menu-panel]');
        var close = document.querySelector('[data-mobile-menu-close]');

        function openMenu() {
            overlay.classList.remove('hidden');
            panel.classList.remove('hidden');
            // allow the element to paint before transitioning in
            requestAnimationFrame(function () {
                panel.classList.remove('translate-x-full');
            });
            toggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            panel.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            window.setTimeout(function () { panel.classList.add('hidden'); }, 300);
        }

        if (toggle) { toggle.addEventListener('click', openMenu); }
        if (close) { close.addEventListener('click', closeMenu); }
        if (overlay) { overlay.addEventListener('click', closeMenu); }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && panel && !panel.classList.contains('hidden')) { closeMenu(); }
        });
    }

    if (document.body) {
        init();
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }
})();
