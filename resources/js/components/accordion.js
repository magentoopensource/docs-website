/**
 * Vanilla JS Accordion for Issues Panel
 * Replaces Alpine.js x-collapse with smooth CSS transitions
 */

export function initAccordion() {
    const accordions = document.querySelectorAll('.issues-accordion');

    accordions.forEach(accordion => {
        const buttons = accordion.querySelectorAll('.issue-header-btn');

        buttons.forEach((button, index) => {
            const panelId = button.getAttribute('aria-controls');
            const panel = document.getElementById(panelId);
            const chevron = button.querySelector('.expand-btn');

            if (!panel) return;

            // Set initial state - closed
            panel.style.maxHeight = '0';
            panel.style.overflow = 'hidden';
            panel.style.opacity = '0';
            panel.style.transition = 'max-height 0.3s ease, opacity 0.3s ease';
            button.setAttribute('aria-expanded', 'false');

            button.addEventListener('click', () => {
                const isOpen = button.getAttribute('aria-expanded') === 'true';

                // Close all other panels in this accordion
                buttons.forEach((otherButton, otherIndex) => {
                    if (otherIndex !== index) {
                        const otherPanelId = otherButton.getAttribute('aria-controls');
                        const otherPanel = document.getElementById(otherPanelId);
                        const otherChevron = otherButton.querySelector('.expand-btn');

                        if (otherPanel) {
                            otherPanel.style.maxHeight = '0';
                            otherPanel.style.opacity = '0';
                            otherButton.setAttribute('aria-expanded', 'false');
                            if (otherChevron) {
                                otherChevron.classList.remove('open');
                            }
                        }
                    }
                });

                // Toggle current panel
                if (isOpen) {
                    // Close
                    panel.style.maxHeight = '0';
                    panel.style.opacity = '0';
                    button.setAttribute('aria-expanded', 'false');
                    if (chevron) {
                        chevron.classList.remove('open');
                    }
                } else {
                    // Open
                    panel.style.maxHeight = panel.scrollHeight + 'px';
                    panel.style.opacity = '1';
                    button.setAttribute('aria-expanded', 'true');
                    if (chevron) {
                        chevron.classList.add('open');
                    }
                }
            });
        });
    });
}
