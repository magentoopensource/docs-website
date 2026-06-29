#!/usr/bin/env bash
# DevDocs generator — Markdown source → styled HTML pages
#
# Usage: generate.sh <input-md-dir> <output-html-dir>
#
# <input-md-dir>   Directory containing guides/ and modules/ sub-directories.
# <output-html-dir> Where to write the generated HTML (created if absent).
#
# The script builds into a temp directory, then overlays the generated pages
# onto <output-html-dir> WITHOUT deleting files it did not generate — so bespoke
# landing/cert pages, patch-*.js and other assets are preserved across rebuilds.
# Idempotent: re-running produces the same result.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

INPUT_MD_DIR="${1:?Usage: $0 <input-md-dir> <output-html-dir>}"
OUTPUT_HTML_DIR="${2:?Usage: $0 <input-md-dir> <output-html-dir>}"

# Resolve input dir to absolute path — must already exist.
INPUT_MD_DIR="$(cd "$INPUT_MD_DIR" && pwd)"

# Resolve output dir to absolute path — need not exist yet.
if command -v realpath &>/dev/null; then
    OUTPUT_HTML_DIR="$(realpath -m "$OUTPUT_HTML_DIR")"
else
    # Fallback for systems without GNU realpath.
    _parent="$(cd "$(dirname "$OUTPUT_HTML_DIR")" 2>/dev/null && pwd || echo "$(pwd)/$(dirname "$OUTPUT_HTML_DIR")")"
    OUTPUT_HTML_DIR="$_parent/$(basename "$OUTPUT_HTML_DIR")"
fi

# Build into a temp dir.
WORK_DIR="$(mktemp -d --tmpdir devdocs-build.XXXXXXXX)"
trap 'rm -rf "$WORK_DIR"' EXIT

# Export paths for the Python scripts — overrides all hardcoded defaults.
export DEVDOCS_MD_DIR="$INPUT_MD_DIR"
export DEVDOCS_OUT_DIR="$WORK_DIR"

echo "=== DevDocs Generator ==="
echo "Input : $INPUT_MD_DIR"
echo "Output: $OUTPUT_HTML_DIR"
echo ""

# Step 1 — Render Markdown → HTML + internal link pass
echo "[1/3] Rendering pages (build_html_pages.py)..."
python3 "$SCRIPT_DIR/build_html_pages.py"

# Step 2 — Fix card-style href="#" links in the generated HTML
echo "[2/3] Fixing card links (fix_card_links.py)..."
python3 "$SCRIPT_DIR/fix_card_links.py"

# Step 3 — Fix remaining href="#" placeholder links
echo "[3/3] Fixing placeholder links (fix_placeholder_links.py)..."
python3 "$SCRIPT_DIR/fix_placeholder_links.py"

HTML_COUNT="$(find "$WORK_DIR" -maxdepth 1 -name '*.html' | wc -l)"
echo ""
echo "Generated $HTML_COUNT HTML files."

# Publish by OVERLAYING generated pages onto the output directory.
# We never delete the whole directory, so files the generator did not produce
# (bespoke landing/cert pages, patch-*.js, other assets) are preserved.
mkdir -p "$OUTPUT_HTML_DIR"
if command -v rsync &>/dev/null; then
    # No --delete: generated files are added/updated; everything else is kept.
    rsync -a "$WORK_DIR"/ "$OUTPUT_HTML_DIR"/
else
    cp -a "$WORK_DIR"/. "$OUTPUT_HTML_DIR"/
fi
# WORK_DIR is removed by the EXIT trap.

echo "Done → $OUTPUT_HTML_DIR ($HTML_COUNT files)"
