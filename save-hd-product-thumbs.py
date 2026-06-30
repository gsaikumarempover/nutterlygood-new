"""DEPRECATED — use install-ai-product-images.py + product-ai-manifest.json instead.

Legacy script cropped existing webp packshots. Carousel products now use
AI-generated images in uploads/2026/06/ai-products/.
"""
from __future__ import annotations

from pathlib import Path

from PIL import Image, ImageEnhance

BASE = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06")
OUT = BASE / "hd-products"
SIZE = 1024
PAD = 72
CREAM = (252, 244, 235)


def fit_contain(img: Image.Image, max_w: int, max_h: int) -> Image.Image:
    ratio = min(max_w / img.width, max_h / img.height)
    new_size = (max(1, int(img.width * ratio)), max(1, int(img.height * ratio)))
    return img.resize(new_size, Image.Resampling.LANCZOS)


def make_hd_thumb(src: Path, dest: Path) -> None:
    img = Image.open(src).convert("RGBA")
    canvas = Image.new("RGB", (SIZE, SIZE), CREAM)
    inner = SIZE - PAD * 2
    fitted = fit_contain(img, inner, inner)
    x = (SIZE - fitted.width) // 2
    y = (SIZE - fitted.height) // 2
    if fitted.mode == "RGBA":
        canvas.paste(fitted, (x, y), fitted)
    else:
        canvas.paste(fitted, (x, y))
    canvas = ImageEnhance.Sharpness(canvas).enhance(1.15)
    canvas = ImageEnhance.Contrast(canvas).enhance(1.05)
    dest.parent.mkdir(parents=True, exist_ok=True)
    canvas.save(dest, "JPEG", quality=94, optimize=True, subsampling=0)


def main() -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    sources = sorted(BASE.glob("*-1.webp"))
    if not sources:
        sources = sorted(BASE.glob("*.webp"))
    lines = ["Nutterly Good — HD Product Carousel Thumbs", f"Size: {SIZE}x{SIZE}px", ""]
    for src in sources:
        stem = src.stem.replace("-1", "")
        dest = OUT / f"{stem}-hd.jpg"
        make_hd_thumb(src, dest)
        lines.append(f"{src.name} -> hd-products/{dest.name}")
        print(dest)
    (OUT / "README.txt").write_text("\n".join(lines), encoding="utf-8")


if __name__ == "__main__":
    main()