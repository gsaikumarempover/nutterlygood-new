#!/usr/bin/env python3
"""Generate per-page banner JPEGs from hero sources."""

from pathlib import Path
from PIL import Image, ImageDraw, ImageFont, ImageFilter

ROOT = Path(__file__).resolve().parents[4]
UPLOADS = ROOT / "wp-content" / "uploads"
HERO_DIR = UPLOADS / "2026" / "06" / "slider" / "hd-heroes" / "ready-1920x600"
OUT_DIR = UPLOADS / "2026" / "06" / "banners"

BANNERS = {
    "banner-default.jpg": ("ng-hero-02-dry-fruits.jpg", "Nutterly Good"),
    "banner-shop.jpg": ("ng-hero-02-dry-fruits.jpg", "Shop"),
    "banner-about.jpg": ("ng-hero-01-flavor-freshness.jpg", "About Us"),
    "banner-contact.jpg": ("ng-hero-03-chips-mixes.jpg", "Contact"),
    "banner-policy.jpg": ("ng-hero-01-flavor-freshness.jpg", "Policies"),
}

SIZE = (1920, 420)


def load_font(size: int):
    for name in ("arialbd.ttf", "Arial Bold.ttf", "segoeuib.ttf"):
        try:
            return ImageFont.truetype(name, size)
        except OSError:
            continue
    return ImageFont.load_default()


def make_banner(src_name: str, title: str, out_name: str):
    src = HERO_DIR / src_name
    if not src.exists():
        raise FileNotFoundError(src)
    img = Image.open(src).convert("RGB")
    img = img.resize(SIZE, Image.Resampling.LANCZOS)
    img = img.filter(ImageFilter.GaussianBlur(radius=0.6))

    overlay = Image.new("RGBA", SIZE, (12, 83, 61, 0))
    draw = ImageDraw.Draw(overlay)
    for x in range(SIZE[0]):
        alpha = int(150 + (90 * x / SIZE[0]))
        draw.line([(x, 0), (x, SIZE[1])], fill=(12, 83, 61, min(alpha, 220)))

    img = Image.alpha_composite(img.convert("RGBA"), overlay).convert("RGB")
    draw = ImageDraw.Draw(img)
    font = load_font(62)
    sub_font = load_font(22)
    draw.text((80, SIZE[1] // 2 - 52), title, fill=(255, 255, 255), font=font)
    draw.text((82, SIZE[1] // 2 + 24), "Premium nuts, dry fruits & wholesome snacks", fill=(252, 244, 235), font=sub_font)

    OUT_DIR.mkdir(parents=True, exist_ok=True)
    out = OUT_DIR / out_name
    img.save(out, "JPEG", quality=88, optimize=True)
    print(f"Wrote {out}")


def main():
    for out_name, (src, title) in BANNERS.items():
        make_banner(src, title, out_name)


if __name__ == "__main__":
    main()