// Initialize docs page functionality
wrapHeadingsInAnchors();
setupNavCurrentLinkHandling();
replaceBlockquotesWithCalloutsInDocs();
highlightSupportPolicyTable();
generateTableOfContents();

function wrapHeadingsInAnchors() {
    [...document.querySelector('.docs_main').querySelectorAll('a[name]')].forEach(anchor => {
        const heading = anchor.parentNode.nextElementSibling;
        heading.id = anchor.name;
        anchor.href = `#${anchor.name}`;
        anchor.removeAttribute('name');
        [...heading.childNodes].forEach(node => anchor.appendChild(node));
        heading.appendChild(anchor);
    });
}

function setupNavCurrentLinkHandling() {
    // Can return two, one for mobile nav and one for desktop nav
    [...document.querySelectorAll('.docs_sidebar ul')].forEach(nav => {
        const pathLength = window.location.pathname.split('/').length;
        const current = nav.querySelector('li a[href="' + (pathLength === 3 ? window.location.pathname+"/installation" : window.location.pathname) + '"]');

        if (current) {
            current.parentNode.parentNode.parentNode.classList.add('sub--on');
            current.parentNode.classList.add('active');
        }
    });

    [...document.querySelectorAll('.docs_sidebar h2')].forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();

            const active = el.parentNode.classList.contains('sub--on');

            [...document.querySelectorAll('.docs_sidebar ul li')].forEach(el => el.classList.remove('sub--on'));

            if(! active) {
                el.parentNode.classList.add('sub--on');
            }
        });
    });

    // Add .has-children class to parent elements
    [...document.querySelectorAll('.docs_sidebar ul li')].forEach(el => {
        if (el.querySelector('ul')) {
            el.classList.add('has-children');
        }
    });

    // Add .child-indicator class to child elements
    [...document.querySelectorAll('.docs_sidebar ul ul li')].forEach(el => {
        el.classList.add('child-indicator');
    });
}

function replaceBlockquotesWithCalloutsInDocs() {
    [...document.querySelectorAll('.docs_main blockquote p')].forEach(el => {
        // Legacy Laravel styled notes...
        replaceBlockquote(el, /\{(.*?)\}/, (type) => {
            switch (type) {
                case "note":
                    return ['/img/callouts/exclamation.min.svg', 'bg-red-600'];
                case "tip":
                    return ['/img/callouts/lightbulb.min.svg', 'bg-purple-600'];
                case "laracasts":
                case "video":
                    return ['/img/callouts/laracast.min.svg', 'bg-blue-600'];
                default:
                    return [null, null];
            }
        });

        // GitHub styled notes...
        replaceBlockquote(el, /^<strong>(.*?)<\/strong>(?:<br>\n?)?/, (type) => {
            switch (type) {
                case "Warning":
                    return ['/img/callouts/exclamation.min.svg', 'bg-red-600'];
                case "Note":
                    return ['/img/callouts/lightbulb.min.svg', 'bg-purple-600'];
                default:
                    return [null, null];
            }
        });
    });
}

function replaceBlockquote(el, regex, getImageAndColorByType) {
    var str = el.innerHTML;
    var match = str.match(regex);
    var img, color;

    if (match) {
        var type = match[1] || false;
    }

    if (type) {
        [img, color] = getImageAndColorByType(type);

        if (img === null && color === null) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.classList = 'mb-10 max-w-2xl mx-auto px-4 py-8 shadow-lg lg:flex lg:items-center';

        const imageWrapper = document.createElement('div');
        imageWrapper.classList = `w-20 h-20 mb-6 flex items-center justify-center shrink-0 ${color} lg:mb-0`;
        const image = document.createElement('img');
        image.src = img;
        image.classList = `opacity-75`;
        imageWrapper.appendChild(image);
        wrapper.appendChild(imageWrapper);

        el.parentNode.insertBefore(wrapper, el);

        el.innerHTML = str.replace(regex, '');

        el.classList = 'mb-0 lg:ml-6';
        wrapper.classList.add('callout');
        wrapper.appendChild(el);
    }
}

function highlightSupportPolicyTable() {

    function highlightCells(table) {
        const currentDate = new Date().valueOf();

        Array.from(table.rows).forEach((row, rowIndex) => {
            if (rowIndex > 0) {
                const cells = row.cells;
                const versionCell = cells[0];
                const bugDateCell = getCellDate(cells[cells.length - 2]);
                const securityDateCell = getCellDate(cells[cells.length - 1]);

                if (currentDate > securityDateCell) {
                    // End of life.
                    versionCell.classList.add('bg-red-500', 'support-policy-highlight');
                } else if ((currentDate <= securityDateCell) && (currentDate > bugDateCell)) {
                    // Security fixes only.
                    versionCell.classList.add('bg-orange-600', 'support-policy-highlight');
                }
            }
        });
    }

    const table = document.querySelector('.docs_main #support-policy ~ div table:first-of-type');

    if (table) {
        highlightCells(table);

        return;
    }

    // <=v9 documentation branches use the old dom structure which doesn't contain the table overflow fix. It's easier to maintain backwards compatibility than to go back and change all the <=v9 branches.
    const oldTable = document.querySelector('.docs_main #support-policy ~ table:first-of-type');

    if (oldTable) {
        highlightCells(oldTable);
    }

}

function getCellDate(cell) {
    return Date.parse(cell.innerHTML.replace(/(\d+)(st|nd|rd|th)/, '$1'));
}

// Generate Table of Contents for KB Pages
function generateTableOfContents() {
    const tocContainer = document.getElementById('toc');
    if (!tocContainer) return;

    const headings = document.querySelectorAll('.docs_main h2, .docs_main h3, .docs_main h4');
    if (headings.length === 0) {
        tocContainer.parentElement.style.display = 'none';
        return;
    }

    const tocList = document.createElement('ul');
    tocList.className = 'space-y-1';

    headings.forEach((heading, index) => {
        // Ensure heading has an id
        if (!heading.id) {
            heading.id = `heading-${index}`;
        }

        const listItem = document.createElement('li');
        const link = document.createElement('a');
        
        link.href = `#${heading.id}`;
        link.textContent = heading.textContent;
        link.className = `block py-1 px-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded transition-colors duration-200 ${
            heading.tagName === 'H3' ? 'ml-4' : heading.tagName === 'H4' ? 'ml-8' : ''
        }`;

        // Add scroll behavior
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.getElementById(heading.id);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.pushState(null, null, `#${heading.id}`);
            }
        });

        listItem.appendChild(link);
        tocList.appendChild(listItem);
    });

    tocContainer.appendChild(tocList);

    // Highlight current section on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const id = entry.target.id;
            const tocLink = tocContainer.querySelector(`a[href="#${id}"]`);
            if (tocLink) {
                if (entry.isIntersecting) {
                    // Remove active class from all links
                    tocContainer.querySelectorAll('a').forEach(link => {
                        link.classList.remove('text-primary-700', 'bg-primary-100', 'font-medium');
                        link.classList.add('text-gray-600');
                    });
                    // Add active class to current link
                    tocLink.classList.remove('text-gray-600');
                    tocLink.classList.add('text-primary-700', 'bg-primary-100', 'font-medium');
                }
            }
        });
    }, {
        rootMargin: '-20% 0px -70% 0px'
    });

    headings.forEach(heading => observer.observe(heading));
}

import { toDarkMode, toLightMode, toSystemMode } from './components/theme';
window.toDarkMode = toDarkMode;
window.toLightMode = toLightMode;
window.toSystemMode = toSystemMode;
