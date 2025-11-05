import docsearch from '@docsearch/js';

// Initialize DocSearch
function initDocSearch() {
    const docsearchContainer = document.getElementById('docsearch');

    if (!docsearchContainer) {
        console.warn('DocSearch container not found.');
        return;
    }

    // Get Algolia credentials from data attributes
    const algoliaAppId = docsearchContainer.dataset.algoliaAppId;
    const algoliaSearchKey = docsearchContainer.dataset.algoliaSearchKey;
    const algoliaIndexName = docsearchContainer.dataset.algoliaIndexName || 'devmage-os';

    // Check if Algolia credentials are available
    if (!algoliaAppId || !algoliaSearchKey) {
        console.warn('Algolia credentials not found. Search functionality will be disabled.');
        return;
    }

    // Initialize DocSearch
    const docSearchInstance = docsearch({
        container: '#docsearch',
        appId: algoliaAppId,
        apiKey: algoliaSearchKey,
        indexName: algoliaIndexName,
        searchParameters: {},
    });

    // Function to trigger search modal
    const triggerSearch = () => {
        // Try to find and click the DocSearch button
        const searchButton = document.querySelector('.DocSearch-Button') ||
                             document.querySelector('[data-docsearch-placeholder]');

        if (searchButton) {
            searchButton.click();
        } else {
            // If DocSearch button not found, try to trigger it manually
            setTimeout(() => {
                const retryButton = document.querySelector('.DocSearch-Button');
                if (retryButton) {
                    retryButton.click();
                }
            }, 100);
        }
    };

    // Handle homepage search button
    const homepageSearchBtn = document.getElementById('homepage-search');
    if (homepageSearchBtn) {
        homepageSearchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            triggerSearch();
        });
    }

    // Handle header search button
    const headerSearchBtn = document.getElementById('header-search');
    if (headerSearchBtn) {
        headerSearchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            triggerSearch();
        });
    }

    // Add keyboard shortcut (CMD+K on Mac, CTRL+K on Windows/Linux)
    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            triggerSearch();
        }
    });
}

// Check if DOM is already loaded (module scripts defer by default)
// If so, run immediately; otherwise wait for DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDocSearch);
} else {
    // DOM is already ready, run immediately
    initDocSearch();
}
