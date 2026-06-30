"""Generate three unique gallery angles per product from its own hero image."""
from __future__ import annotations

import io
import json
from pathlib import Path

from PIL import Image, ImageEnhance, ImageOps

ROOT = Path(r"C:\xampp\htdocs\nutterlyGood")
MANIFESTS = [
    ROOT / "product-ai-manifest.json",
    ROOT / "product-ai-manifest-remaining.json",
]
OUT_DIR = ROOT / "wp-content/uploads/2026/06/ai-products"
SIZE = 1024

ANGLE_SPECS = (
    ("angle-top", {"scale": 0.58, "rotate": -18, "offset": (0.0, -0.12)}),
    ("angle-side", {"scale": 0.68, "rotate": 28, "offset": (0.14, 0.08)}),
    ("angle-close", {"scale": 1.22, "rotate": 6, "offset": (0.0, 0.02), "crop_tight": True}),
)


def load_manifest() -> list[dict]:
    items: list[dict] = []
    for path in MANIFESTS:
        if path.exists():
            items.extend(json.loads(path.read_text(encoding="utf-8")))
    return items


def remove_background(src: Path) -> Image.Image:
    try:
        from rembg import remove

        return Image.open(io.BytesIO(remove(src.read_bytes()))).convert("RGBA")
    except Exception:
        img = Image.open(src).convert("RGBA")
        return img


def tight_bbox(img: Image.Image, crop_tight: bool) -> Image.Image:
    bbox = img.getbbox()
    if not bbox:
        return img
    subject = img.crop(bbox)
    if crop_tight:
        w, h = subject.size
        pad_x = int(w * 0.04)
        pad_y = int(h * 0.04)
        subject = subject.crop((pad_x, pad_y, w - pad_x, h - pad_y))
    return subject


def compose_angle(src: Path, dest: Path, spec: dict) -> None:
    cutout = remove_background(src)
    subject = tight_bbox(cutout, spec.get("crop_tight", False))

    if spec.get("rotate"):
        subject = subject.rotate(spec["rotate"], resample=Image.Resampling.BICUBIC, expand=True)

    canvas = Image.new("RGBA", (SIZE, SIZE), (0, 0, 0, 0))
    scale = spec.get("scale", 0.8)
    target = int(SIZE * scale)
    subject.thumbnail((target, target), Image.Resampling.LANCZOS)

    ox = int((SIZE - subject.width) / 2 + spec.get("offset", (0, 0))[0] * SIZE)
    oy = int((SIZE - subject.height) / 2 + spec.get("offset", (0, 0))[1] * SIZE)
    canvas.paste(subject, (ox, oy), subject)

    canvas = ImageEnhance.Sharpness(canvas).enhance(1.06)
    canvas = ImageEnhance.Contrast(canvas).enhance(1.03)
    dest = dest.with_suffix(".png")
    dest.parent.mkdir(parents=True, exist_ok=True)
    canvas.save(dest, "PNG", optimize=True)


def main() -> None:
    created = 0
    skipped = 0
    for item in load_manifest():
        slug = item.get("slug", "")
        file_name = item.get("file", "")
        if not slug or not file_name:
            continue

        hero = OUT_DIR / file_name
        if not hero.exists():
            print(f"SKIP missing hero: {hero}")
            skipped += 1
            continue

        stem = Path(file_name).stem
        for suffix, spec in ANGLE_SPECS:
            dest = OUT_DIR / f"{stem}-{suffix}.png"
            if dest.exists() and dest.stat().st_size > 10_000:
                print(f"EXISTS {dest.name}")
                continue
            compose_angle(hero, dest, spec)
            print(f"CREATED {dest.name}")
            created += 1

    print(f"\nDone. created={created} skipped_missing_hero={skipped}")


if __name__ == "__main__":
    main()