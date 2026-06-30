"""Replace light grey JPEG backgrounds and soft drop shadows with pure white."""
from __future__ import annotations

from collections import deque
from pathlib import Path

from PIL import Image

ROOT = Path(r"C:\xampp\htdocs\nutterlyGood\wp-content\uploads\2026\06")
BG_TOLERANCE = 42
SHADOW_GREY_TOLERANCE = 28


def sample_bg_color(img: Image.Image) -> tuple[int, int, int]:
	w, h = img.size
	points = [(4, 4), (w - 5, 4), (4, h - 5), (w - 5, h - 5)]
	pixels = [img.getpixel(p)[:3] for p in points]
	return (
		sum(p[0] for p in pixels) // len(pixels),
		sum(p[1] for p in pixels) // len(pixels),
		sum(p[2] for p in pixels) // len(pixels),
	)


def is_seed_pixel(r: int, g: int, b: int, bg: tuple[int, int, int]) -> bool:
	if max(r, g, b) < 175:
		return False

	if (
		abs(r - bg[0]) <= BG_TOLERANCE
		and abs(g - bg[1]) <= BG_TOLERANCE
		and abs(b - bg[2]) <= BG_TOLERANCE
	):
		return True

	# Soft grey drop shadows baked into pack shots.
	if max(r, g, b) >= 185 and max(r, g, b) < 252 and (max(r, g, b) - min(r, g, b)) <= SHADOW_GREY_TOLERANCE:
		return True

	return False


def flood_fill_background(img: Image.Image, bg: tuple[int, int, int]) -> int:
	w, h = img.size
	px = img.load()
	visited = bytearray(w * h)
	changed = 0
	queue: deque[tuple[int, int]] = deque()

	for x in range(w):
		queue.append((x, 0))
		queue.append((x, h - 1))
	for y in range(h):
		queue.append((0, y))
		queue.append((w - 1, y))

	while queue:
		x, y = queue.popleft()
		idx = y * w + x
		if visited[idx]:
			continue
		visited[idx] = 1

		r, g, b = px[x, y]
		if not is_seed_pixel(r, g, b, bg):
			continue

		px[x, y] = (255, 255, 255)
		changed += 1

		if x > 0:
			queue.append((x - 1, y))
		if x < w - 1:
			queue.append((x + 1, y))
		if y > 0:
			queue.append((x, y - 1))
		if y < h - 1:
			queue.append((x, y + 1))

	return changed


def whiten_background(path: Path) -> bool:
	with Image.open(path) as src:
		img = src.convert("RGB")
		bg = sample_bg_color(img)
		changed = flood_fill_background(img, bg)

		if changed == 0:
			return False

		img.save(path, quality=92, optimize=True)
		return True


def main() -> None:
	patterns = ["df-*-1.jpg", "df-*-1.jpeg", "ng-branded-packets/df-*.jpg", "ng-branded-packets/mx-*.jpg"]
	seen: set[Path] = set()
	updated = 0
	for pattern in patterns:
		for path in ROOT.glob(pattern):
			if path in seen:
				continue
			seen.add(path)
			if whiten_background(path):
				updated += 1
				print(f"whitened: {path.name}")

	print(f"done: {updated} images updated")


if __name__ == "__main__":
	main()