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
    // Captured now: document.currentScript is only set while this classic script runs.
    var SCRIPT_SRC = (document.currentScript && document.currentScript.src) || '';

    /* --- Algolia DocSearch (public, search-only credentials) --------------------
     * Mirrors the merchant config in config/algolia.php. The index is shared:
     * the Algolia app contains exactly one index. Keep these in step with the
     * merchant side (config/algolia.php) when rotating the key.
     *
     * DOCSEARCH_VERSION is pinned exactly (never a range) and is the only place
     * the version lives; it resolves to:
     *   https://cdn.jsdelivr.net/npm/@docsearch/js@3.9.0/dist/umd/index.js
     *   https://cdn.jsdelivr.net/npm/@docsearch/css@3.9.0/dist/style.css
     * ------------------------------------------------------------------------ */
    var DOCSEARCH_APP_ID     = '4K4YE687PF';
    var DOCSEARCH_API_KEY    = 'abad9b46e045bb213667d569ce70e8f3';
    var DOCSEARCH_INDEX_NAME = 'Documentation';
    var DOCSEARCH_VERSION    = '3.9.0';
    var DOCSEARCH_JS_URL  = 'https://cdn.jsdelivr.net/npm/@docsearch/js@' + DOCSEARCH_VERSION + '/dist/umd/index.js';
    var DOCSEARCH_CSS_URL = 'https://cdn.jsdelivr.net/npm/@docsearch/css@' + DOCSEARCH_VERSION + '/dist/style.css';
    // Our colour tokens sit next to this file and must load after the vendor CSS
    // so they win at equal specificity.
    var DOCSEARCH_TOKENS_URL = SCRIPT_SRC ?
        SCRIPT_SRC.replace(/[^\/?#]*([?#].*)?$/, '') + 'docsearch-tokens.css' :
        'includes/docsearch-tokens.css';

    // Header search trigger: the same control as the merchant docs header
    // (resources/views/partials/main-header.blade.php, #header-search). Hidden until
    // DocSearch has mounted, so a failed CDN load never leaves a dead icon.
    var SEARCH_ICON =
        '<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<path d="M8 16C9.77498 15.9996 11.4988 15.4054 12.897 14.312L17.293 18.708L18.707 17.294L14.311 12.898C15.405 11.4997 15.9996 9.77544 16 8C16 3.589 12.411 0 8 0C3.589 0 0 3.589 0 8C0 12.411 3.589 16 8 16ZM8 2C11.309 2 14 4.691 14 8C14 11.309 11.309 14 8 14C4.691 14 2 11.309 2 8C2 4.691 4.691 2 8 2Z" fill="#F26423"/>' +
        '</svg>';
    var SEARCH_TRIGGER_CLASS = 'flex items-center justify-center w-10 h-10 rounded-lg hover:bg-off-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-opacity-50';

    function searchTrigger(id, extraClass) {
        return '<button type="button" id="' + id + '" class="' + (extraClass ? extraClass + ' ' : '') + SEARCH_TRIGGER_CLASS + '" aria-label="Search the documentation" style="display:none">' + SEARCH_ICON + '</button>';
    }

    // Magento box mark (icon only) + text wordmark + section label. The icon is
    // the recognisable Magento hexagon-in-box; the text identifies it as the
    // Magento Open Source Developer Documentation.
    var MAGENTO_LOGO = '' +
        '<span class="inline-flex items-center gap-3">' +
        '<svg width="30" height="33" viewBox="0 0 30 33" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0" aria-hidden="true">' +
        '<path d="M0 4.06492H29.6763V31.8882C29.6763 32.502 29.1713 33 28.5487 33H1.12762C0.505079 33 0 32.502 0 31.8882V4.06492Z" fill="#34323A"/>' +
        '<path d="M1.26857 0H28.4078C29.1066 0 29.6763 0.561678 29.6763 1.25075V4.06492H0V1.25075C0 0.561678 0.569682 0 1.26857 0Z" fill="#C9C9C9"/>' +
        '<path d="M2.37269 3.0458C2.94031 3.0458 3.40046 2.59211 3.40046 2.03246C3.40046 1.47281 2.94031 1.01913 2.37269 1.01913C1.80506 1.01913 1.34491 1.47281 1.34491 2.03246C1.34491 2.59211 1.80506 3.0458 2.37269 3.0458Z" fill="#848484"/>' +
        '<path d="M5.28571 3.0458C5.85334 3.0458 6.31349 2.59211 6.31349 2.03246C6.31349 1.47281 5.85334 1.01913 5.28571 1.01913C4.71809 1.01913 4.25793 1.47281 4.25793 2.03246C4.25793 2.59211 4.71809 3.0458 5.28571 3.0458Z" fill="#848484"/>' +
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
        { label: 'Guides', href: '/developer/#guides' },
        { label: 'Modules', href: '/developer/modules.html' },
        { label: 'Learning Paths', href: '/developer/learning-paths.html' },
        { label: 'The Core', href: '/the-core/' }
    ];

    // Last ecosystem link ends in "+", as on the merchant docs and the landing page (partials/ecosystem-menu.blade.php).
    var plusSvg = '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.8125 5.75C11.8125 6.24219 11.4023 6.65234 10.9375 6.65234H7V10.5898C7 11.0547 6.58984 11.4375 6.125 11.4375C5.63281 11.4375 5.25 11.0547 5.25 10.5898V6.65234H1.3125C0.820312 6.65234 0.4375 6.24219 0.4375 5.75C0.4375 5.28516 0.820312 4.90234 1.3125 4.90234H5.25V0.964844C5.25 0.472656 5.63281 0.0625 6.125 0.0625C6.58984 0.0625 7 0.472656 7 0.964844V4.90234H10.9375C11.4023 4.875 11.8125 5.28516 11.8125 5.75Z" fill="#F26423"/></svg>';
    var arrowSvg = '<svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.75 11C1.50391 11 1.28516 10.918 1.12109 10.7539C0.765625 10.4258 0.765625 9.85156 1.12109 9.52344L4.86719 5.75L1.12109 2.00391C0.765625 1.67578 0.765625 1.10156 1.12109 0.773438C1.44922 0.417969 2.02344 0.417969 2.35156 0.773438L6.72656 5.14844C7.08203 5.47656 7.08203 6.05078 6.72656 6.37891L2.35156 10.7539C2.1875 10.918 1.96875 11 1.75 11Z" fill="#F26423"/></svg>';

    function ecosystemItems() {
        return ECOSYSTEM_LINKS.map(function (l, i) {
            var sep = i > 0 ? '<div class="h-10 w-px bg-gray-600"></div>' : '';
            return sep +
                '<div class="bg-charcoal flex gap-2.5 items-center justify-start px-5 py-2.5 text-sm">' +
                '<a href="' + l.href + '" target="_blank" rel="noopener" class="text-white font-bold no-underline whitespace-nowrap hover:text-orange transition-colors duration-200">' + l.label + '</a>' +
                (i === ECOSYSTEM_LINKS.length - 1 ? plusSvg : arrowSvg) + '</div>';
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
        '<div style="letter-spacing:0.6px" class="hidden lg:flex bg-charcoal items-center justify-center h-10 w-full">' +
            '<div class="max-w-7xl xl:max-w-[96rem] w-full flex items-center justify-between px-4 sm:px-6 lg:px-8">' +
                '<div class="hidden xl:block text-sm leading-[1.42] text-white font-bold">Explore the Magento<span class="text-[9px] align-super">&reg;</span> Open Source Ecosystem</div>' +
                '<div class="flex items-center ml-auto">' + ecosystemItems() + '</div>' +
            '</div>' +
        '</div>' +
        // Main header — sticky, fixed h-16 to align with `sticky top-16` quick-jump nav.
        '<div style="letter-spacing:0.6px" class="sticky top-0 z-50 bg-white flex items-center h-16 w-full border-b border-gray-200 shadow-sm">' +
            '<div class="flex items-center justify-between w-full max-w-7xl xl:max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8">' +
                '<a href="/" class="inline-flex items-center" aria-label="Magento Developer Docs home">' + MAGENTO_LOGO + '</a>' +
                '<div class="flex items-center gap-2 lg:gap-8">' +
                    // DocSearch renders its own button into this hidden slot; the visible
                    // triggers below open it, as on the merchant docs.
                    '<div id="docsearch" style="display:none"></div>' +
                    searchTrigger('mobile-header-search', 'lg:hidden') +
                    '<button data-mobile-menu-toggle class="lg:hidden flex items-center justify-center w-10 h-10 text-charcoal hover:text-orange transition-all focus:outline-none focus:ring-2 focus:ring-orange" aria-label="Toggle navigation menu" aria-expanded="false">' +
                        '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>' +
                    '</button>' +
                    '<div class="hidden lg:flex items-center gap-8">' +
                        '<nav aria-label="Main navigation" class="flex flex-row gap-x-6 lg:gap-x-7 xl:gap-x-8 items-center">' + desktopNav() + '</nav>' +
                        searchTrigger('header-search') +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
        // Mobile overlay + panel.
        '<div data-mobile-menu-overlay class="hidden fixed inset-0 bg-charcoal/80 z-40 lg:hidden transition-opacity duration-200" aria-hidden="true"></div>' +
        '<div data-mobile-menu-panel style="letter-spacing:0.6px" aria-hidden="true" class="hidden fixed top-0 right-0 h-full w-[26rem] max-w-[90%] bg-white shadow-2xl z-50 lg:hidden overflow-y-auto transform translate-x-full transition-transform duration-300 ease-out border-t-4 border-yellow">' +
            '<div class="flex flex-col h-full">' +
                '<div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-off-white">' +
                    '<div class="text-xl font-bold text-charcoal m-0">Menu</div>' +
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
            // While the search modal is open, Escape belongs to DocSearch only.
            if (e.key === 'Escape' && !document.body.classList.contains('DocSearch--active') &&
                panel && !panel.classList.contains('hidden')) { closeMenu(); }
        });
    }

    // DocSearch 3.9 already wraps Tab and handles Cmd/Ctrl+K, '/' and Escape. It does
    // not return focus to the trigger when it closes, lets Shift+Tab leave the modal
    // after a click on blank modal space, and gives the modal no dialog role. This
    // complements it from outside, with no key handling of our own.
    function setupSearchFocus() {
        var slot = document.getElementById('docsearch');
        if (!slot || typeof MutationObserver !== 'function') { return; }
        var opener = null;

        function visible(el) {
            return !!el && el.isConnected && el.getClientRects().length > 0;
        }

        function guard(e) {
            var modal = document.querySelector('.DocSearch-Modal');
            var input = document.querySelector('.DocSearch-Input');
            if (modal && input && !modal.contains(e.target)) { input.focus(); }
        }

        function onOpen(container) {
            var active = document.activeElement;
            if (!opener && active && active !== document.body && !container.contains(active)) { opener = active; }
            var modal = container.querySelector('.DocSearch-Modal');
            if (modal) {
                modal.setAttribute('role', 'dialog');
                modal.setAttribute('aria-modal', 'true');
                modal.setAttribute('aria-label', 'Search the documentation');
            }
            document.addEventListener('focusin', guard, true);
        }

        // Restore on the next tick: a click on the overlay closes DocSearch on mousedown,
        // and the browser then moves focus to BODY as the default action of that mousedown.
        function onClose() {
            document.removeEventListener('focusin', guard, true);
            var from = opener;
            opener = null;
            window.setTimeout(function () {
                var active = document.activeElement;
                if (active && active !== document.body) { return; }
                var target = [from, document.getElementById('header-search'), document.getElementById('mobile-header-search')]
                    .filter(visible)[0];
                if (target) { target.focus({ preventScroll: true }); }
            }, 0);
        }

        function isContainer(node) {
            return node.nodeType === 1 && node.classList.contains('DocSearch-Container');
        }

        ['header-search', 'mobile-header-search'].forEach(function (id) {
            var trigger = document.getElementById(id);
            if (!trigger) { return; }
            trigger.addEventListener('click', function () {
                var button = slot.querySelector('.DocSearch-Button');
                if (!button) { return; }
                opener = trigger;
                button.click();
            });
        });

        new MutationObserver(function (records) {
            records.forEach(function (record) {
                Array.prototype.forEach.call(record.addedNodes, function (n) { if (isContainer(n)) { onOpen(n); } });
                Array.prototype.forEach.call(record.removedNodes, function (n) { if (isContainer(n)) { onClose(); } });
            });
        }).observe(document.body, { childList: true });
    }

    // Render DocSearch's own button into the #docsearch slot. No searchParameters
    // or transformItems: the index only facets on type/lang, and filtering on
    // anything else returns zero hits with no error.
    function mountDocSearch() {
        var slot = document.getElementById('docsearch');
        if (!slot || slot.hasChildNodes() || typeof window.docsearch !== 'function') { return; }
        window.docsearch({
            container: slot,
            appId: DOCSEARCH_APP_ID,
            apiKey: DOCSEARCH_API_KEY,
            indexName: DOCSEARCH_INDEX_NAME,
            placeholder: 'Search the developer documentation',
            translations: {
                button: {
                    buttonText: 'Search the documentation',
                    buttonAriaLabel: 'Search the documentation'
                }
            }
        });
        if (slot.querySelector('.DocSearch-Button')) {
            ['header-search', 'mobile-header-search'].forEach(function (id) {
                var trigger = document.getElementById(id);
                if (trigger) { trigger.style.display = ''; }
            });
        }
    }

    function mountDocSearchSafely() {
        try {
            mountDocSearch();
        } catch (e) {
            // Search is optional; the header works without it.
        }
    }

    // Append the DocSearch stylesheet, our tokens, then the library to <head>,
    // and mount once the library has loaded. If the CDN is unreachable the tags
    // fail to load, nothing is mounted and the slot stays empty.
    function loadDocSearch() {
        if (typeof window.docsearch === 'function') {
            mountDocSearchSafely();
            return;
        }
        var head = document.head || document.getElementsByTagName('head')[0];
        if (!head || head.querySelector('script[src="' + DOCSEARCH_JS_URL + '"]')) { return; }

        var css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = DOCSEARCH_CSS_URL;
        head.appendChild(css);

        var tokens = document.createElement('link');
        tokens.rel = 'stylesheet';
        tokens.href = DOCSEARCH_TOKENS_URL;
        head.appendChild(tokens);

        var js = document.createElement('script');
        js.src = DOCSEARCH_JS_URL;
        js.async = true;
        js.addEventListener('load', mountDocSearchSafely);
        head.appendChild(js);
    }

    function start() {
        init();
        try {
            setupSearchFocus();
        } catch (e) {
            // Search is optional; never let it affect the header.
        }
        try {
            loadDocSearch();
        } catch (e) {
            // Search is optional; never let it affect the header.
        }
    }

    if (document.body) {
        start();
    } else {
        document.addEventListener('DOMContentLoaded', start);
    }
})();
