"""Extract category SVG clipart from greenpath-header.html."""
import re
import json
from pathlib import Path

html = Path(__file__).parent.joinpath("greenpath-header.html").read_text(encoding="utf-8", errors="ignore")
# Each category block: href ... product-category/SLUG/ ... svg ... title
pattern = re.compile(
    r'product-category/([^/]+)/.*?<span class="qodef-e-custom-svg">\s*(<svg.*?</svg>)\s*</span>.*?'
    r'<h6 class="woocommerce-loop-category__title"[^>]*>\s*([^<]+)</h6>',
    re.DOTALL,
)
items = []
for m in pattern.finditer(html):
    slug, svg, title = m.group(1), m.group(2).strip(), m.group(3).strip()
    bg_m = re.search(rf'product-category/{re.escape(slug)}/.*?style="background-color:\s*([^"]+)"', html[m.start():m.start() + 800])
    bg = bg_m.group(1) if bg_m else ""
    items.append({"slug": slug, "title": title, "bg": bg, "svg_len": len(svg)})

out = Path(__file__).parent / "greenpath-category-svgs.json"
data = {}
for m in pattern.finditer(html):
    slug = m.group(1)
    if slug not in data:
        data[slug] = {"title": m.group(3).strip(), "svg": m.group(2).strip()}

# Also grab bg from preceding div
for slug in list(data.keys()):
    block_pat = rf'<div class="qodef-e[^"]*product-category[^"]*" style="background-color:\s*([^"]+)"[^>]*>\s*<a href="[^"]*product-category/{re.escape(slug)}/'
    bg_m = re.search(block_pat, html)
    if bg_m:
        data[slug]["bg"] = bg_m.group(1)

out.write_text(json.dumps(data, indent=2), encoding="utf-8")
print(f"Extracted {len(data)} SVGs -> {out}")
for slug, info in data.items():
    print(f"  {slug}: bg={info.get('bg','?')} svg={len(info['svg'])} chars")