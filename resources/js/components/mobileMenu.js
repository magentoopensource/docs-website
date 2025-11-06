/**
 * Mobile Menu Module
 * Vanilla JS implementation for mobile navigation menu
 * No dependencies, pure performance
 */

export function initMobileMenu() {
    // Get all the elements we need
    const toggleButton = document.querySelector('[data-mobile-menu-toggle]');
    const overlay = document.querySelector('[data-mobile-menu-overlay]');
    const panel = document.querySelector('[data-mobile-menu-panel]');
    const closeButtons = document.querySelectorAll('[data-mobile-menu-close]');

    // If we don't have the required elements, bail early
    if (!toggleButton || !overlay || !panel) {
        return;
    }

    let isOpen = false;

    /**
     * Open the mobile menu
     */
    function openMenu() {
        isOpen = true;

        // Show overlay and panel
        overlay.classList.remove('hidden');
        panel.classList.remove('hidden', 'translate-x-full');

        // Update ARIA
        toggleButton.setAttribute('aria-expanded', 'true');
        panel.setAttribute('aria-hidden', 'false');

        // Lock body scroll
        document.body.style.overflow = 'hidden';

        // Focus the close button for accessibility
        const firstCloseButton = panel.querySelector('[data-mobile-menu-close]');
        if (firstCloseButton) {
            // Small delay to ensure transition starts first
            setTimeout(() => firstCloseButton.focus(), 100);
        }
    }

    /**
     * Close the mobile menu
     */
    function closeMenu() {
        isOpen = false;

        // Hide panel (translate it off-screen)
        panel.classList.add('translate-x-full');

        // Update ARIA
        toggleButton.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');

        // Unlock body scroll
        document.body.style.overflow = '';

        // Return focus to toggle button
        toggleButton.focus();

        // Hide overlay after animation completes
        setTimeout(() => {
            overlay.classList.add('hidden');
            panel.classList.add('hidden');
        }, 200); // Match transition duration
    }

    /**
     * Toggle menu open/closed
     */
    function toggleMenu() {
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    }

    /**
     * Handle escape key press
     */
    function handleEscape(event) {
        if (event.key === 'Escape' && isOpen) {
            closeMenu();
        }
    }

    /**
     * Handle clicks on menu links - close menu when navigating
     */
    function handleMenuLinkClick(event) {
        // Check if clicked element is a link within the panel
        const clickedLink = event.target.closest('a');
        if (clickedLink && panel.contains(clickedLink)) {
            // Small delay to allow the click to register before closing
            setTimeout(closeMenu, 50);
        }
    }

    // Event Listeners
    // Toggle button
    toggleButton.addEventListener('click', toggleMenu);

    // Close buttons (could be multiple)
    closeButtons.forEach(button => {
        button.addEventListener('click', closeMenu);
    });

    // Overlay click (click outside to close)
    overlay.addEventListener('click', closeMenu);

    // Escape key
    document.addEventListener('keydown', handleEscape);

    // Menu links (close on navigation)
    panel.addEventListener('click', handleMenuLinkClick);

    // Cleanup function (optional, for SPA use cases)
    return function cleanup() {
        toggleButton.removeEventListener('click', toggleMenu);
        closeButtons.forEach(button => {
            button.removeEventListener('click', closeMenu);
        });
        overlay.removeEventListener('click', closeMenu);
        document.removeEventListener('keydown', handleEscape);
        panel.removeEventListener('click', handleMenuLinkClick);
    };
}
