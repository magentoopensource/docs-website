/**
 * Smooth Scroll Module
 * Handles anchor link smooth scrolling with accessibility considerations
 */

export function initSmoothScroll() {
    // Check if user prefers reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Handle all anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Ignore empty anchors
            if (href === '#') {
                return;
            }

            const targetId = href.substring(1);
            const targetElement = document.getElementById(targetId);

            if (!targetElement) {
                return;
            }

            e.preventDefault();

            // Use instant scroll if user prefers reduced motion
            if (prefersReducedMotion) {
                targetElement.scrollIntoView();
            } else {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            // Update URL without triggering navigation
            if (history.pushState) {
                history.pushState(null, null, href);
            }

            // Set focus for accessibility (with slight delay for smooth scroll)
            setTimeout(() => {
                targetElement.setAttribute('tabindex', '-1');
                targetElement.focus({ preventScroll: true });
            }, prefersReducedMotion ? 0 : 500);
        });
    });
}
