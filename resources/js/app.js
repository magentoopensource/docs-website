import Alpine from 'alpinejs';
import Focus from '@alpinejs/focus';

import './clipboard';
import './components/search';
import { initLazyLoading } from './components/lazyImages';
import { initErrorLogging } from './components/errorLogging';

window.Alpine = Alpine;

Alpine.plugin(Focus);

// Initialize error logging first to catch any subsequent errors
initErrorLogging();

// Main initialization function
function initApp() {
    // Start Alpine after DOM is loaded to ensure x-data elements exist
    Alpine.start();

    // Initialize lazy loading for all images
    initLazyLoading();

    if (document.querySelector('#docsScreen')) {
        import('./docs.js');
    }

    // Only load Mermaid if there are actually mermaid diagrams on the page
    if (document.querySelector('.language-mermaid') || document.querySelector('[class*="mermaid"]')) {
        import('mermaid').then(({ default: mermaid }) => {
            mermaid.initialize({
                startOnLoad: true,
                theme: 'default'
            });
        });
    }

    // Connect mobile menu search to desktop search functionality
    const desktopSearchBtn = document.getElementById('header-search');
    const mobileMenuSearch = document.getElementById('mobile-menu-search');

    if (mobileMenuSearch && desktopSearchBtn) {
        mobileMenuSearch.addEventListener('click', () => {
            desktopSearchBtn.click();
        });
    }

    import('./components/accessibility');
}

// Check if DOM is already loaded (module scripts defer by default)
// If so, run immediately; otherwise wait for DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    // DOM is already ready, run immediately
    initApp();
}
