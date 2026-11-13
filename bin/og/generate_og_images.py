#!/usr/bin/env python3
"""
Regenerate the per-guide Open Graph images (SEO plan decision D6).

One 1200x630 PNG per merchant guide, written to public/img/og/<category>/<slug>.png. The guide title is read from
the <h1> of resources/docs/html/<category>/<slug>.html, so a renamed or retitled guide only needs this script run
again. The images are committed; nothing is generated at request time (DocsController::guideImage only checks
that the file exists).

    python3 bin/og/generate_og_images.py                # all guides
    python3 bin/og/generate_og_images.py --check        # exit 1 if a guide has no image, or an image has no guide
    python3 bin/og/generate_og_images.py --font /path/to/Inter.ttf

Needs Pillow (with FreeType variable-font support) and the Inter variable font. The brand font is looked up in
--font, then $OG_FONT, then the usual system and QB locations. Output is deterministic for a given font file.
"""
import argparse
import html
import os
import re
import sys

from PIL import Image, ImageDraw, ImageFont

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
HTML_DIR = os.path.join(ROOT, 'resources', 'docs', 'html')
OUT_DIR = os.path.join(ROOT, 'public', 'img', 'og')

W, H = 1200, 630
CHARCOAL = (44, 44, 44)
YELLOW = (241, 188, 27)
ORANGE = (242, 100, 35)
ORANGE_TEXT = (194, 65, 12)
GREY = (209, 213, 219)

FONT_CANDIDATES = [
    '/home/carl/.local/share/fonts/qb-brand/Inter.ttf',
    '/usr/share/fonts/truetype/inter/Inter.ttf',
    '/usr/share/fonts/truetype/inter/InterVariable.ttf',
    '/usr/share/fonts/opentype/inter/Inter.ttf',
]


def find_font(explicit):
    for path in [explicit, os.environ.get('OG_FONT')] + FONT_CANDIDATES:
        if path and os.path.isfile(path):
            return path
    sys.exit('Inter font not found: pass --font or set OG_FONT')


def font(path, size, weight):
    f = ImageFont.truetype(path, size)
    f.set_variation_by_axes([min(32, max(14, size)), weight])
    return f


def guides():
    """(category, slug, title) for every guide, sorted."""
    out = []
    for cat in sorted(os.listdir(HTML_DIR)):
        d = os.path.join(HTML_DIR, cat)
        if not os.path.isdir(d):
            continue
        for name in sorted(os.listdir(d)):
            if not name.endswith('.html'):
                continue
            text = open(os.path.join(d, name), encoding='utf-8').read()
            m = re.search(r'<h1\b[^>]*>(.*?)</h1>', text, re.S)
            if not m:
                sys.exit('no <h1> in %s/%s' % (cat, name))
            title = html.unescape(re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', '', m.group(1)))).strip()
            out.append((cat, name[:-5], title))
    return out


def category_label(cat):
    return {'reports-and-analytics': 'Reports and Analytics', 'improve-ux': 'Improve UX'}.get(
        cat, cat.replace('-', ' ').title())


def hexagon(cx, cy, r):
    """Pointy-top hexagon like the site's hero rings."""
    import math
    return [(cx + r * math.sin(math.radians(a)), cy - r * math.cos(math.radians(a))) for a in range(0, 360, 60)]


def ring(img, cx, cy, outer, inner, color, alpha):
    mask = Image.new('L', (W, H), 0)
    d = ImageDraw.Draw(mask)
    d.polygon(hexagon(cx, cy, outer), fill=255)
    d.polygon(hexagon(cx, cy, inner), fill=0)
    mask = mask.point(lambda v: int(v * alpha))
    img.paste(Image.new('RGB', (W, H), color), (0, 0), mask)


def wrap(draw, text, fnt, width):
    lines, cur = [], ''
    for word in text.split():
        trial = (cur + ' ' + word).strip()
        if draw.textlength(trial, font=fnt) <= width or not cur:
            cur = trial
        else:
            lines.append(cur)
            cur = word
    if cur:
        lines.append(cur)
    return lines


def render(font_path, cat, title):
    img = Image.new('RGB', (W, H), (255, 255, 255))
    # Hero rings on the right, as on the landing page, kept clear of the title column.
    ring(img, 1090, 330, 300, 172, YELLOW, 0.40)
    ring(img, 1090, 400, 300, 172, ORANGE, 0.40)
    ring(img, 1090, 470, 300, 172, CHARCOAL, 0.40)
    d = ImageDraw.Draw(img)

    # Wordmark, top left: browser-window mark with the yellow hexagon, then "Documentation".
    d.rectangle([64, 48, 94, 80], fill=(52, 50, 58))
    d.rectangle([64, 44, 94, 52], fill=(201, 201, 201))
    d.polygon(hexagon(79, 68, 11), fill=YELLOW)
    d.text((108, 46), 'Documentation', font=font(font_path, 26, 700), fill=CHARCOAL)

    # Section label.
    d.text((64, 168), category_label(cat).upper(), font=font(font_path, 24, 700), fill=ORANGE_TEXT)

    # Title: largest size that fits in four lines of the left column.
    width = 720
    for size in (76, 68, 62, 56, 50, 46):
        fnt = font(font_path, size, 800)
        lines = wrap(d, title, fnt, width)
        if len(lines) <= 3 or (size <= 50 and len(lines) <= 4):
            break
    y = 214
    for line in lines:
        d.text((64, y), line, font=fnt, fill=CHARCOAL)
        y += int(size * 1.12)

    # Badge, bottom left.
    d.rectangle([64, 520, 64 + 330, 520 + 46], fill=CHARCOAL)
    d.polygon(hexagon(91, 543, 11), fill=ORANGE)
    d.text((114, 530), 'Magento Open Source', font=font(font_path, 22, 500), fill=GREY)

    # Brand bar along the bottom.
    for x in range(W):
        t = x / (W - 1)
        if t < 0.5:
            a, b, k = YELLOW, ORANGE, t / 0.5
        else:
            a, b, k = ORANGE, CHARCOAL, (t - 0.5) / 0.5
        d.line([(x, 620), (x, 629)], fill=tuple(int(a[i] + (b[i] - a[i]) * k) for i in range(3)))
    return img


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--font')
    ap.add_argument('--check', action='store_true')
    args = ap.parse_args()
    items = guides()

    if args.check:
        want = {'%s/%s.png' % (c, s) for c, s, _ in items}
        have = set()
        for cat in (os.listdir(OUT_DIR) if os.path.isdir(OUT_DIR) else []):
            for name in os.listdir(os.path.join(OUT_DIR, cat)):
                have.add('%s/%s' % (cat, name))
        missing, extra = sorted(want - have), sorted(have - want)
        for m in missing:
            print('missing image:', m)
        for e in extra:
            print('image without guide:', e)
        print('%d guides, %d images' % (len(want), len(have)))
        sys.exit(1 if missing or extra else 0)

    path = find_font(args.font)
    for cat, slug, title in items:
        out = os.path.join(OUT_DIR, cat, slug + '.png')
        os.makedirs(os.path.dirname(out), exist_ok=True)
        # Palette PNG keeps each file small (flat colours, one gradient bar).
        render(path, cat, title).quantize(colors=48, method=Image.Quantize.MEDIANCUT, dither=Image.Dither.NONE).save(out, optimize=True)
        print(out[len(ROOT) + 1:], '-', title)


if __name__ == '__main__':
    main()
