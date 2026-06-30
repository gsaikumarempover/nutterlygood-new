"""Install AI-generated HD square product images (1024x1024) — no legacy packshots."""
from __future__ import annotations

import json
import shutil
from pathlib import Path

import io

from PIL import Image, ImageEnhance
from rembg import remove

SESSION = Path(
    r"C:\Users\G Sai Kumar\.grok\sessions\C%3A%5CUsers%5CG%20Sai%20Kumar\019ed9ba-80f9-78a1-9fda-a791f0155a73\images"
)
OUT = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06\ai-products")
SOURCE_ARCHIVE = OUT / "source"
MANIFEST = Path(r"C:\xampp\htdocs\nutterlyGood\product-ai-manifest.json")
SIZE = 1024


def center_crop_square(img: Image.Image) -> Image.Image:
    w, h = img.size
    side = min(w, h)
    left = (w - side) // 2
    top = (h - side) // 2
    return img.crop((left, top, left + side, top + side))


def to_transparent_square(src: Path, dest: Path) -> None:
    cutout = Image.open(io.BytesIO(remove(src.read_bytes()))).convert("RGBA")
    canvas = Image.new("RGBA", (SIZE, SIZE), (0, 0, 0, 0))
    bbox = cutout.getbbox()
    if not bbox:
        raise RuntimeError(f"No subject detected in {src}")
    subject = cutout.crop(bbox)
    scale = min((SIZE * 0.82) / subject.width, (SIZE * 0.82) / subject.height)
    new_w = max(1, int(subject.width * scale))
    new_h = max(1, int(subject.height * scale))
    subject = subject.resize((new_w, new_h), Image.Resampling.LANCZOS)
    x = (SIZE - new_w) // 2
    y = (SIZE - new_h) // 2
    canvas.paste(subject, (x, y), subject)
    canvas = ImageEnhance.Sharpness(canvas).enhance(1.05)
    dest = dest.with_suffix(".png")
    dest.parent.mkdir(parents=True, exist_ok=True)
    canvas.save(dest, "PNG", optimize=True)


def main() -> None:
    items = json.loads(MANIFEST.read_text(encoding="utf-8"))
    OUT.mkdir(parents=True, exist_ok=True)
    SOURCE_ARCHIVE.mkdir(parents=True, exist_ok=True)
    lines = [
        "Nutterly Good — AI-Generated Product Carousel Images",
        f"Uniform size: {SIZE}x{SIZE}px PNG (transparent)",
        "DO NOT use legacy webp packshots for carousel display.",
        "",
    ]
    for item in items:
        src = SESSION / item["source"]
        if not src.exists():
            raise FileNotFoundError(src)
        archive = SOURCE_ARCHIVE / item["source"]
        shutil.copy2(src, archive)
        dest = OUT / item["file"]
        to_transparent_square(src, dest)
        lines.append(f"{item['title']} -> ai-products/{item['file']} (from {item['source']})")
        print(dest)
    (OUT / "README.txt").write_text("\n".join(lines), encoding="utf-8")


if __name__ == "__main__":
    main()