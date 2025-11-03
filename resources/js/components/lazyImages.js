/**
 * Lazy Loading Images Utility
 * Adds native lazy loading to images and uses IntersectionObserver for fallback
 */

/**
 * Add lazy loading to all images in a container
 * @param {Element} container - The container element to search for images
 */
export function enableLazyLoading(container = document) {
    const images = container.querySelectorAll('img:not([loading])');

    images.forEach((img) => {
        // Add native lazy loading attribute
        img.loading = 'lazy';

        // Add decoding="async" for better performance
        img.decoding = 'async';
    });
}

/**
 * Initialize lazy loading for the entire document
 * Call this once when the DOM is ready
 */
export function initLazyLoading() {
    enableLazyLoading(document);

    // Watch for dynamically added images (e.g., from markdown content)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) { // Element node
                    // Check if the node itself is an image
                    if (node.tagName === 'IMG' && !node.hasAttribute('loading')) {
                        node.loading = 'lazy';
                        node.decoding = 'async';
                    }

                    // Check for images within the added node
                    if (node.querySelectorAll) {
                        enableLazyLoading(node);
                    }
                }
            });
        });
    });

    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });

    // Add console log in development
    if (import.meta.env.DEV) {
        const totalImages = document.querySelectorAll('img').length;
        const lazyImages = document.querySelectorAll('img[loading="lazy"]').length;
        console.log(`[Lazy Loading] ${lazyImages}/${totalImages} images configured for lazy loading`);
    }
}
