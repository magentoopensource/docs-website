import ClipboardJS from 'clipboard/dist/clipboard';

// Icons for copy button states
const clipboardIcon = `<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path><path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path></svg>`;
const clipboardCopiedIcon = `<svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`;

/**
 * Extract clean text from code element, removing line numbers and preserving formatting
 * @param {HTMLElement} codeElement - The code element to extract text from
 * @returns {string} Clean code text without line numbers or weird characters
 */
function getCleanCodeText(codeElement) {
    // Clone the element to avoid modifying the original
    const clone = codeElement.cloneNode(true);

    // Remove line number elements (Torchlight uses .line-number class)
    clone.querySelectorAll('.line-number').forEach(el => el.remove());

    // Remove any elements that should not be copied (like copy buttons, etc.)
    clone.querySelectorAll('[data-no-copy], .no-copy').forEach(el => el.remove());

    // Get text content and clean it up
    let text = clone.textContent || clone.innerText || '';

    // Normalize line endings and remove any zero-width characters
    text = text
        .replace(/\u200B/g, '')  // Zero-width space
        .replace(/\u00A0/g, ' ') // Non-breaking space to regular space
        .replace(/\r\n/g, '\n')  // Normalize Windows line endings
        .replace(/\r/g, '\n');   // Normalize old Mac line endings

    return text;
}

// Initialize copy to clipboard for all code blocks
function initializeClipboard() {
    const codeBlocks = document.querySelectorAll('#main-content pre');

    codeBlocks.forEach((element, key) => {
        // Skip if already wrapped
        if (element.parentNode.classList.contains('code-block-wrapper')) {
            return;
        }

        // Add wrapper to code block
        const wrapper = document.createElement('div');
        wrapper.classList.add('relative', 'code-block-wrapper');

        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);

        // Create copy button
        const copyBtn = document.createElement('button');
        copyBtn.innerHTML = clipboardIcon;
        copyBtn.id = `clipButton-${key}`;
        copyBtn.classList.add('copyBtn');
        copyBtn.setAttribute('aria-label', 'Copy code to clipboard');
        copyBtn.setAttribute('title', 'Copy to clipboard');
        copyBtn.setAttribute('type', 'button');

        wrapper.appendChild(copyBtn);

        // Get code element
        const codeElement = element.querySelector('code');
        if (!codeElement) return;

        codeElement.id = `clipText-${key}`;

        // Initialize ClipboardJS with custom text extraction
        const clipboard = new ClipboardJS(`#${copyBtn.id}`, {
            text: function() {
                return getCleanCodeText(codeElement);
            }
        });

        clipboard.on('success', () => {
            copyBtn.innerHTML = clipboardCopiedIcon;
            copyBtn.setAttribute('aria-label', 'Copied!');

            setTimeout(() => {
                copyBtn.innerHTML = clipboardIcon;
                copyBtn.setAttribute('aria-label', 'Copy code to clipboard');
            }, 2000);
        });

        clipboard.on('error', () => {
            copyBtn.setAttribute('aria-label', 'Failed to copy');
            setTimeout(() => {
                copyBtn.setAttribute('aria-label', 'Copy code to clipboard');
            }, 2000);
        });
    });
}

// Run on DOMContentLoaded and also export for dynamic content
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeClipboard);
} else {
    initializeClipboard();
}

export { initializeClipboard, getCleanCodeText };
