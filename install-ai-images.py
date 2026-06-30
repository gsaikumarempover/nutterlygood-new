"""Install AI-generated HD square images for products and category cards."""
from __future__ import annotations

import json
import shutil
from pathlib import Path

from PIL import Image, ImageEnhance

SESSION = Path(
    r"C:\Users\G Sai Kumar\.grok\sessions\C%3A%5CUsers%5CG%20Sai%20Kumar\019ed9ba-80f9-78a1-9fda-a791f0155a73\images"
)
UPLOADS = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06")
ROOT = Path(r"C:\xampp\htdocs\nutterlyGood")
SIZE = 1024

JOBS = [
    ("product-ai-manifest.json", UPLOADS / "ai-products"),
    ("product-ai-manifest-remaining.json", UPLOADS / "ai-products"),
    ("category-ai-manifest.json", UPLOADS / "ai-categories"),
]


def center_crop_square(img: Image.Image) -> Image.Image:
    w, h = img.size
    side = min(w, h)
    left = (w - side) // 2
    top = (h - side) // 2
    return img.crop((left, top, left + side, top + side))


def to_hd_square(src: Path, dest: Path) -> None:
    img = Image.open(src).convert("RGB")
    img = center_crop_square(img)
    img = img.resize((SIZE, SIZE), Image.Resampling.LANCZOS)
    img = ImageEnhance.Sharpness(img).enhance(1.1)
    img = ImageEnhance.Color(img).enhance(1.03)
    dest.parent.mkdir(parents=True, exist_ok=True)
    img.save(dest, "JPEG", quality=96, optimize=True, subsampling=0)


def install_manifest(manifest_name: str, out_dir: Path) -> list[str]:
    manifest = ROOT / manifest_name
    if not manifest.exists():
        return []
    items = json.loads(manifest.read_text(encoding="utf-8"))
    source_archive = out_dir / "source"
    source_archive.mkdir(parents=True, exist_ok=True)
    lines: list[str] = []
    for item in items:
        src = SESSION / item["source"]
        if not src.exists():
            raise FileNotFoundError(src)
        shutil.copy2(src, source_archive / item["source"])
        dest = out_dir / item["file"]
        to_hd_square(src, dest)
        lines.append(f"{item.get('title', item['slug'])} -> {out_dir.name}/{item['file']}")
        print(dest)
    return lines


def main() -> None:
    all_lines = ["Nutterly Good — AI image install log", f"Uniform size: {SIZE}x{SIZE}px", ""]
    for manifest_name, out_dir in JOBS:
        all_lines.append(f"[{manifest_name}]")
        all_lines.extend(install_manifest(manifest_name, out_dir))
        all_lines.append("")
    (UPLOADS / "ai-products" / "README.txt").write_text("\n".join(all_lines), encoding="utf-8")


if __name__ == "__main__":
    main()