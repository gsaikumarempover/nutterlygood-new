"""Replace AI product image backgrounds with plain white."""
from __future__ import annotations

import io
import json
from pathlib import Path

from PIL import Image, ImageEnhance
from rembg import remove

AI_DIR = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06\ai-products")
MANIFESTS = [
    Path(r"C:\xampp\htdocs\nutterlyGood\product-ai-manifest.json"),
    Path(r"C:\xampp\htdocs\nutterlyGood\product-ai-manifest-remaining.json"),
]
SIZE = 1024


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


def collect_targets() -> list[Path]:
    files: list[Path] = []
    seen: set[str] = set()
    for manifest in MANIFESTS:
        if not manifest.exists():
            continue
        for item in json.loads(manifest.read_text(encoding="utf-8")):
            rel = item.get("file")
            if not rel or rel in seen:
                continue
            seen.add(rel)
            files.append(AI_DIR / rel)
    for path in sorted(AI_DIR.glob("*.jpg")):
        if path.name not in seen:
            files.append(path)
    return files


def main() -> None:
    targets = collect_targets()
    if not targets:
        raise SystemExit("No product images found.")
    done = 0
    for path in targets:
        if not path.exists():
            print(f"SKIP missing: {path.name}")
            continue
        to_white_bg_square(path, path)
        print(f"WHITE BG: {path.name}")
        done += 1
    print(f"=== Done: {done} product images on plain white background ===")


if __name__ == "__main__":
    main()