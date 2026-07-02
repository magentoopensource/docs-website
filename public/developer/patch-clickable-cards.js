#!/usr/bin/env node
/**
 * Make entire cards clickable on index/listing pages.
 * Uses the "stretched link" CSS pattern: the <a> inside h2 gets
 * position:static removed and an ::after pseudo-element covers the
 * whole card. The card gets position:relative and cursor:pointer.
 *
 * Targets: architecture.html, tutorials.html, how-to-guides.html,
 *          certifications.html, index.html
 */
const fs = require('fs');
const path = require('path');

const deployDir = __dirname;
const targetFiles = [
  'architecture.html',
  'tutorials.html',
  'how-to-guides.html',
  'certifications.html',
  'index.html'
];

// CSS for stretched link pattern
const stretchedCSS = `
        /* Clickable cards — stretched link pattern */
        .card-link {
            position: relative;
            cursor: pointer;
        }
        .card-link .card-title-link::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 1;
        }
        .card-link .card-title-link {
            position: static;
        }
        .card-link .card-title-link::before {
            content: none;
        }
        /* Ensure tags sit above the stretched link */
        .card-link .flex-wrap {
            position: relative;
            z-index: 2;
        }`;

let patched = 0;

targetFiles.forEach(file => {
  const filePath = path.join(deployDir, file);
  if (!fs.existsSync(filePath)) return;

  let html = fs.readFileSync(filePath, 'utf8');

  // Skip if already patched
  if (html.includes('card-link')) {
    console.log(`SKIP (already patched): ${file}`);
    return;
  }

  const original = html;

  // 1. Inject CSS before </style> or before </head>
  const styleClose = html.lastIndexOf('</style>');
  if (styleClose !== -1) {
    html = html.slice(0, styleClose) + stretchedCSS + '\n    ' + html.slice(styleClose);
  } else {
    html = html.replace('</head>', '<style>' + stretchedCSS + '\n    </style>\n</head>');
  }

  // 2. Add 'card-link' class to article cards that have hover:shadow-lg
  html = html.replace(
    /(<article\s+class="[^"]*hover:shadow-lg hover:-translate-y-1[^"]*")/g,
    (match) => {
      return match.replace('class="', 'class="card-link ');
    }
  );

  // 3. Add 'card-title-link' class to the <a> inside h2/h3 within cards
  //    Pattern: <a href="guide-..." class="text-xl font-bold ...">
  html = html.replace(
    /(<h[23]\s+class="[^"]*">\s*<a\s+href="[^"]+"\s+class=")(text-xl font-bold[^"]*")/g,
    (match, prefix, classes) => {
      return prefix + 'card-title-link ' + classes;
    }
  );

  if (html !== original) {
    fs.writeFileSync(filePath, html, 'utf8');
    console.log(`PATCHED: ${file}`);
    patched++;
  }
});

console.log(`\nDone: ${patched} patched`);
