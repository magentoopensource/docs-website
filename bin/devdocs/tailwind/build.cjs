#!/usr/bin/env node
/*
 * Compile the Tailwind CSS the developer docs and The Core used to get from cdn.tailwindcss.com at runtime.
 *
 *   node bin/devdocs/tailwind/build.cjs            # compile every group, write the files and manifest.json
 *   node bin/devdocs/tailwind/build.cjs --check    # exit 1 if a compiled file would change (stale CSS)
 *
 * groups.json lists, per group, the pages that share one Tailwind theme (configs/<group>.json is the theme
 * each page used to set inline). Each group becomes public/<section>/assets/tw-<group>.<hash>.css, minified,
 * with the content hash in the name so it can be cached for a year. manifest.json maps group -> URL; the
 * generator and apply_static_seo.py read it to write the <link>.
 *
 * Classes are found by scanning the group's pages and the scripts that inject markup (header.js, contributors.js,
 * patch-*.js). A class that only exists in a script outside that list, or is assembled from string pieces,
 * will be missing: add the script to SCRIPTS or write the class out in full.
 *
 * Re-run this after adding a page or changing classes in any page. The deploy does not run it.
 * Pinned to Tailwind 3.4.17, the same major the CDN served (the pages set the v3 `tailwind.config` global).
 */
'use strict';
const fs = require('fs');
const path = require('path');
const os = require('os');
const crypto = require('crypto');
const { execSync } = require('child_process');

const ROOT = path.resolve(__dirname, '..', '..', '..');
const CHECK = process.argv.includes('--check');
const CLI = process.env.TAILWIND_BIN || 'npx --yes tailwindcss@3.4.17';
const SCRIPTS = {
    developer: ['public/developer/includes/header.js', 'public/developer/includes/contributors.js',
        'public/developer/patch-clickable-cards.js', 'public/developer/patch-code-blocks.js',
        'public/developer/patch-toc-sidebar.js', 'public/developer/patch-viewport.js'],
    'the-core': [],
};

const groups = JSON.parse(fs.readFileSync(path.join(__dirname, 'groups.json'), 'utf8'));
const manifestPath = path.join(__dirname, 'manifest.json');
const manifest = {};
let stale = 0;
const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'twbuild-'));

for (const [name, g] of Object.entries(groups)) {
    const cfg = JSON.parse(fs.readFileSync(path.join(__dirname, 'configs', name + '.json'), 'utf8'));
    cfg.content = g.pages.concat(SCRIPTS[g.section]).map((f) => path.join(ROOT, f));
    const cfgFile = path.join(tmp, name + '.config.js');
    fs.writeFileSync(cfgFile, 'module.exports = ' + JSON.stringify(cfg) + ';');
    const input = path.join(tmp, 'in.css');
    fs.writeFileSync(input, '@tailwind base;\n@tailwind components;\n@tailwind utilities;\n');
    const out = path.join(tmp, name + '.css');
    execSync(`${CLI} -c "${cfgFile}" -i "${input}" -o "${out}" --minify`, { stdio: ['ignore', 'ignore', 'inherit'] });
    const css = fs.readFileSync(out);
    const hash = crypto.createHash('sha256').update(css).digest('hex').slice(0, 10);
    const rel = `public/${g.section}/assets/tw-${name}.${hash}.css`;
    manifest[name] = '/' + rel.replace(/^public\//, '');
    const dir = path.join(ROOT, path.dirname(rel));
    const existing = fs.existsSync(dir) ? fs.readdirSync(dir).filter((f) => f.startsWith(`tw-${name}.`)) : [];
    if (CHECK) {
        if (!existing.includes(path.basename(rel))) { stale++; console.log('STALE ' + name); }
        continue;
    }
    fs.mkdirSync(dir, { recursive: true });
    existing.forEach((f) => fs.unlinkSync(path.join(dir, f)));
    fs.writeFileSync(path.join(ROOT, rel), css);
    console.log(`${name}: ${rel} (${css.length} bytes)`);
}
fs.rmSync(tmp, { recursive: true, force: true });
if (CHECK) { process.exit(stale ? 1 : 0); }
fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2) + '\n');
