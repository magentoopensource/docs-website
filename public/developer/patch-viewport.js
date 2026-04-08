#!/usr/bin/env node
/**
 * Patch viewport width from max-w-7xl (1280px) to max-w-screen-2xl (1536px)
 * across all deployed HTML files.
 */
const fs = require('fs');
const path = require('path');

const deployDir = __dirname;
const files = fs.readdirSync(deployDir).filter(f => f.endsWith('.html'));

let patched = 0;
let skipped = 0;

files.forEach(file => {
  const filePath = path.join(deployDir, file);
  let html = fs.readFileSync(filePath, 'utf8');

  if (!html.includes('max-w-7xl')) {
    skipped++;
    return;
  }

  const original = html;
  html = html.replace(/max-w-7xl/g, 'max-w-screen-2xl');

  if (html !== original) {
    fs.writeFileSync(filePath, html, 'utf8');
    const count = (original.match(/max-w-7xl/g) || []).length;
    console.log(`PATCHED: ${file} (${count} replacements)`);
    patched++;
  } else {
    skipped++;
  }
});

console.log(`\nDone: ${patched} patched, ${skipped} skipped`);
