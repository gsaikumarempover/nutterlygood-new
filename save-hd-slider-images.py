"""Generate HD slider hero pack into a dedicated folder."""
from pathlib import Path
import shutil

from PIL import Image, ImageDraw, ImageEnhance

SESSION = Path(
    r"C:\Users\G Sai Kumar\.grok\sessions\C%3A%5CUsers%5CG%20Sai%20Kumar\019ed9ba-80f9-78a1-9fda-a791f0155a73\images"
)
OUT = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06\slider\hd-heroes")
SOURCE_DIR = OUT / "source"
HERO_DIR = OUT / "ready-1920x600"

HERO_W, HERO_H = 1920, 600
WHITE = (255, 255, 255)
PRODUCT_BAND = 340

SLIDES = {
    "01-flavor-freshness": {
        "source": SESSION / "14.jpg",
        "title": "Flavor & Freshness",
        "crop_top": 0.22,
    },
    "02-dry-fruits": {
        "source": SESSION / "12.jpg",
        "title": "Premium Dry Fruits",
        "crop_top": 0.24,
    },
    "03-chips-mixes": {
        "source": SESSION / "13.jpg",
        "title": "Crunchy Chips & Mixes",
        "crop_top": 0.23,
    },
}


def to_hero(src: Path, crop_top: float, dest: Path) -> None:
    img = Image.open(src).convert("RGB")
    w, h = img.size
    img = img.crop((0, int(h * crop_top), w, h))

    scale = HERO_W / img.width
    target_h = max(1, int(img.height * scale))
    img = img.resize((HERO_W, target_h), Image.Resampling.LANCZOS)
    if target_h > PRODUCT_BAND:
        img = img.resize((HERO_W, PRODUCT_BAND), Image.Resampling.LANCZOS)
        target_h = PRODUCT_BAND

    img = ImageEnhance.Sharpness(img).enhance(1.12)
    img = ImageEnhance.Contrast(img).enhance(1.04)

    canvas = Image.new("RGB", (HERO_W, HERO_H), WHITE)
    y = HERO_H - target_h
    canvas.paste(img, (0, y))

    fade = Image.new("RGBA", (HERO_W, HERO_H), (0, 0, 0, 0))
    draw = ImageDraw.Draw(fade)
    fade_h = 55
    fade_y = y - fade_h // 2
    for i in range(fade_h):
        alpha = int(255 * (1 - i / fade_h) ** 1.8)
        draw.line([(0, fade_y + i), (HERO_W, fade_y + i)], fill=(255, 255, 255, alpha))
    canvas = Image.alpha_composite(canvas.convert("RGBA"), fade).convert("RGB")
    canvas.save(dest, "JPEG", quality=96, optimize=True, subsampling=0)


def main() -> None:
    SOURCE_DIR.mkdir(parents=True, exist_ok=True)
    HERO_DIR.mkdir(parents=True, exist_ok=True)

    readme = OUT / "README.txt"
    lines = [
        "Nutterly Good — HD Slider Hero Images",
        "=====================================",
        "",
        "source/          Original AI-generated images (high-res)",
        "ready-1920x600/  Slider-ready crops at 1920 x 600 px",
        "",
        "Slide mapping:",
    ]

    for slug, meta in SLIDES.items():
        src = meta["source"]
        if not src.exists():
            raise FileNotFoundError(src)

        source_out = SOURCE_DIR / f"ng-hero-{slug}-source.jpg"
        hero_out = HERO_DIR / f"ng-hero-{slug}.jpg"

        shutil.copy2(src, source_out)
        Image.open(src).convert("RGB").save(source_out, "JPEG", quality=98)
        to_hero(src, meta["crop_top"], hero_out)

        lines.append(f"  {slug} — {meta['title']}")
        lines.append(f"    source: {source_out.name}")
        lines.append(f"    ready:  {hero_out.name}")
        print(f"Saved {hero_out}")

    readme.write_text("\n".join(lines) + "\n", encoding="utf-8")
    print(f"\nFolder: {OUT}")


if __name__ == "__main__":
    main()