import docsearch from '@docsearch/js';

// Wait for DOM to be ready and for algolia variables to be available
document.addEventListener('DOMContentLoaded', () => {
    // Check if Algolia credentials are available
    if (typeof algolia_app_id === 'undefined' || !algolia_app_id || !algolia_search_key) {
        console.warn('Algolia credentials not found. Search functionality will be disabled.');
        return;
    }

    // Initialize DocSearch
    const docSearchInstance = docsearch({
        container: '#docsearch',
        appId: algolia_app_id,
        apiKey: algolia_search_key,
        indexName: 'devmage-os',
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
});
