"""
Category thumbnails only.

Slider hero backgrounds are generated separately via AI (see prepare-slider-heroes.py)
and mapped in fix-slider-header.php — do NOT composite global product packshots
into slider backgrounds.
"""
from __future__ import annotations

from pathlib import Path

from PIL import Image, ImageDraw

BASE = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06")
OUT_CAT = BASE / "category-thumbs"

CREAM = (252, 244, 235)
GOLD = (185, 149, 49)
CAT_SIZE = 360


def load(rel: str) -> Image.Image:
    return Image.open(BASE / rel).convert("RGBA")


def fit_contain(img: Image.Image, max_w: int, max_h: int) -> Image.Image:
    ratio = min(max_w / img.width, max_h / img.height)
    size = (max(1, int(img.width * ratio)), max(1, int(img.height * ratio)))
    return img.resize(size, Image.Resampling.LANCZOS)


def make_category_thumb(slug: str, rel: str) -> Path:
    canvas = Image.new("RGB", (CAT_SIZE, CAT_SIZE), CREAM)
    draw = ImageDraw.Draw(canvas)
    draw.ellipse((18, 18, CAT_SIZE - 18, CAT_SIZE - 18), outline=GOLD, width=4)
    src = load(rel)
    fitted = fit_contain(src, CAT_SIZE - 70, CAT_SIZE - 70)
    x = (CAT_SIZE - fitted.width) // 2
    y = (CAT_SIZE - fitted.height) // 2 - 8
    if fitted.mode == "RGBA":
        canvas.paste(fitted, (x, y), fitted)
    else:
        canvas.paste(fitted, (x, y))
    out = OUT_CAT / f"ng-cat-{slug}.jpg"
    canvas.save(out, "JPEG", quality=90, optimize=True)
    return out


def main() -> None:
    OUT_CAT.mkdir(parents=True, exist_ok=True)
    categories = {
        "dry-fruits": "Premium-Classic-Almonds-1.webp",
        "almonds": "Premium-Classic-Almonds-1.webp",
        "cashews": "Premium-Classic-Cashews-1.webp",
        "khishmish": "Kala-Khatta-Kishmish-1.webp",
        "cranberry": "Premium-Classic-Cranberry-1.webp",
        "walnuts": "Chilean-Walnuts-1.webp",
        "chips": "Beetroot-Masala-Chips-1.webp",
        "mixes": "Protein-Mix-1.webp",
        "brittles": "Chocolate-Brittle-1.webp",
        "mouth-fresheners": "Calcutta-Paan-1.webp",
    }
    for slug, rel in categories.items():
        print(f"Category: {make_category_thumb(slug, rel)}")


if __name__ == "__main__":
    main()