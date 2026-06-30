"""
Prepare AI-generated HD slider heroes (1920x600) — GreenPath layout:
  top ~60% clean white for centered text, products in bottom band only.
"""
from pathlib import Path

from PIL import Image, ImageDraw, ImageEnhance, ImageFilter

HERO_W, HERO_H = 1920, 600
WHITE = (255, 255, 255)
PRODUCT_BAND = 340
OUT = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06\slider")
GEN = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06\slider\generated")

# Slide text concept -> dedicated generated source (NOT global product media).
SOURCES = {
    "ng-hero-flavor-freshness.jpg": {
        "src": GEN / "slide1-flavor-freshness-hd.jpg",
        "crop_top": 0.22,
        "concept": "Flavor & Freshness",
    },
    "ng-hero-dry-fruits.jpg": {
        "src": GEN / "slide2-dry-fruits-hd.jpg",
        "crop_top": 0.24,
        "concept": "Premium Dry Fruits",
    },
    "ng-hero-chips-mixes.jpg": {
        "src": GEN / "slide3-chips-mixes-hd.jpg",
        "crop_top": 0.23,
        "concept": "Crunchy Chips & Mixes",
    },
}


def import_generated() -> None:
    """Copy latest AI outputs into stable generated/ folder."""
    GEN.mkdir(parents=True, exist_ok=True)
    session = Path(
        r"C:\Users\G Sai Kumar\.grok\sessions\C%3A%5CUsers%5CG%20Sai%20Kumar\019ed9ba-80f9-78a1-9fda-a791f0155a73\images"
    )
    mapping = {
        "slide1-flavor-freshness-hd.jpg": session / "11.jpg",
        "slide2-dry-fruits-hd.jpg": session / "10.jpg",
        "slide3-chips-mixes-hd.jpg": session / "9.jpg",
    }
    for dest, src in mapping.items():
        if not src.exists():
            raise FileNotFoundError(f"Missing generated source: {src}")
        Image.open(src).convert("RGB").save(GEN / dest, "JPEG", quality=98)


def to_hero(meta: dict, dest_name: str) -> Path:
    img = Image.open(meta["src"]).convert("RGB")
    w, h = img.size

    # Keep product-heavy lower region; discard empty sky above.
    top = int(h * meta["crop_top"])
    img = img.crop((0, top, w, h))

    # Upscale for HD slider width.
    scale = HERO_W / img.width
    target_h = max(1, int(img.height * scale))
    img = img.resize((HERO_W, target_h), Image.Resampling.LANCZOS)

    if target_h > PRODUCT_BAND:
        img = img.resize((HERO_W, PRODUCT_BAND), Image.Resampling.LANCZOS)
        target_h = PRODUCT_BAND

    img = ImageEnhance.Sharpness(img).enhance(1.15)
    img = ImageEnhance.Contrast(img).enhance(1.05)

    canvas = Image.new("RGB", (HERO_W, HERO_H), WHITE)
    y = HERO_H - target_h
    canvas.paste(img, (0, y))

    # Soft blend at product band top edge only (preserves HD detail).
    fade = Image.new("RGBA", (HERO_W, HERO_H), (0, 0, 0, 0))
    draw = ImageDraw.Draw(fade)
    fade_h = 55
    fade_y = y - fade_h // 2
    for i in range(fade_h):
        alpha = int(255 * (1 - i / fade_h) ** 1.8)
        draw.line([(0, fade_y + i), (HERO_W, fade_y + i)], fill=(255, 255, 255, alpha))
    canvas = Image.alpha_composite(canvas.convert("RGBA"), fade).convert("RGB")

    out = OUT / dest_name
    canvas.save(out, "JPEG", quality=96, optimize=True, subsampling=0)
    return out


def main() -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    import_generated()
    for name, meta in SOURCES.items():
        path = to_hero(meta, name)
        print(f"{meta['concept']} -> {path}")


if __name__ == "__main__":
    main()