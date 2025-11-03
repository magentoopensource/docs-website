import Alpine from 'alpinejs';
import Focus from '@alpinejs/focus';

import './clipboard';
import './components/search';
import { initLazyLoading } from './components/lazyImages';
import { initErrorLogging } from './components/errorLogging';

window.Alpine = Alpine;

Alpine.plugin(Focus);
Alpine.start();

// Initialize error logging first to catch any subsequent errors
initErrorLogging();

document.addEventListener('DOMContentLoaded', () => {
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

    import('./components/accessibility');
});
