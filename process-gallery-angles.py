"""Process generated angle shots into white-bg 1024px gallery JPEGs."""
from __future__ import annotations

import io
import shutil
from pathlib import Path

from PIL import Image, ImageEnhance
from rembg import remove

SESSION = Path(
    r"C:\Users\G Sai Kumar\.grok\sessions\C%3A%5CUsers%5CG%20Sai%20Kumar\019ed9ba-80f9-78a1-9fda-a791f0155a73\images"
)
OUT = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06\ai-products")
SIZE = 1024

ANGLES = [
    ("61.jpg", "df-ppa-250-peri-peri-almonds-5-angle-top.jpg"),
    ("63.jpg", "df-ppa-250-peri-peri-almonds-5-angle-side.jpg"),
    ("62.jpg", "df-ppa-250-peri-peri-almonds-5-angle-close.jpg"),
]


def to_white_bg_square(src: Path, dest: Path) -> None:
    cutout = Image.open(io.BytesIO(remove(src.read_bytes()))).convert("RGBA")
    side = max(cutout.size)
    canvas = Image.new("RGBA", (side, side), (255, 255, 255, 255))
    bbox = cutout.getbbox()
    if not bbox:
        raise RuntimeError(f"No subject detected in {src}")
    subject = cutout.crop(bbox)
    scale = min((side * 0.82) / subject.width, (side * 0.82) / subject.height)
    new_w = max(1, int(subject.width * scale))
    new_h = max(1, int(subject.height * scale))
    subject = subject.resize((new_w, new_h), Image.Resampling.LANCZOS)
    x = (side - new_w) // 2
    y = (side - new_h) // 2
    canvas.paste(subject, (x, y), subject)
    rgb = canvas.convert("RGB").resize((SIZE, SIZE), Image.Resampling.LANCZOS)
    rgb = ImageEnhance.Sharpness(rgb).enhance(1.05)
    dest.parent.mkdir(parents=True, exist_ok=True)
    rgb.save(dest, "JPEG", quality=96, optimize=True, subsampling=0)


def main() -> None:
    for src_name, dest_name in ANGLES:
        src = SESSION / src_name
        if not src.exists():
            raise FileNotFoundError(src)
        dest = OUT / dest_name
        to_white_bg_square(src, dest)
        shutil.copy2(src, OUT / "source" / src_name)
        print(dest)


if __name__ == "__main__":
    main()