"""Re-process AI product images: transparent PNG pack shots (no white JPEG background)."""
from __future__ import annotations

import io
from pathlib import Path

from PIL import Image, ImageEnhance

ROOT = Path(r"C:\xampp\htdocs\nutterlyGood")
OUT_DIR = ROOT / "wp-content/uploads/2026/06/ai-products"
SIZE = 1024
SCALE = 0.82


def remove_background(src: Path) -> Image.Image:
    from rembg import remove

    return Image.open(io.BytesIO(remove(src.read_bytes()))).convert("RGBA")


def to_transparent_square(src: Path, dest: Path) -> bool:
    cutout = remove_background(src)
    bbox = cutout.getbbox()
    if not bbox:
        print(f"SKIP no subject: {src.name}")
        return False

    subject = cutout.crop(bbox)
    canvas = Image.new("RGBA", (SIZE, SIZE), (0, 0, 0, 0))

    scale = min((SIZE * SCALE) / subject.width, (SIZE * SCALE) / subject.height)
    new_w = max(1, int(subject.width * scale))
    new_h = max(1, int(subject.height * scale))
    subject = subject.resize((new_w, new_h), Image.Resampling.LANCZOS)

    x = (SIZE - new_w) // 2
    y = (SIZE - new_h) // 2
    canvas.paste(subject, (x, y), subject)

    canvas = ImageEnhance.Sharpness(canvas).enhance(1.04)
    dest.parent.mkdir(parents=True, exist_ok=True)
    canvas.save(dest, "PNG", optimize=True)
    return True


def main() -> None:
    created = 0
    skipped = 0
    failed = 0

    for src in sorted(OUT_DIR.glob("*.jpg")):
        dest = src.with_suffix(".png")
        if dest.exists() and dest.stat().st_size > 5000:
            print(f"EXISTS {dest.name}")
            skipped += 1
            continue
        try:
            if to_transparent_square(src, dest):
                print(f"CREATED {dest.name}")
                created += 1
            else:
                failed += 1
        except Exception as exc:
            print(f"ERROR {src.name}: {exc}")
            failed += 1

    print(f"\nDone. created={created} skipped={skipped} failed={failed}")


if __name__ == "__main__":
    main()