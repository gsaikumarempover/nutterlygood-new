"""Screenshot Fresh Tasty product carousel section."""
from pathlib import Path

from playwright.sync_api import sync_playwright

URL = "http://localhost/nutterlyGood/"
OUT = Path(r"C:\xampp\htdocs\nutterlyGood\slider-browser-check")
OUT.mkdir(parents=True, exist_ok=True)


def main() -> None:
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={"width": 1440, "height": 900})
        page.goto(URL, wait_until="networkidle", timeout=60000)
        page.wait_for_timeout(4000)
        title = page.locator("text=Fresh, Tasty, and Wholesome").first
        if title.count():
            title.scroll_into_view_if_needed()
            page.wait_for_timeout(1000)
            box = title.bounding_box()
            if box:
                page.screenshot(
                    path=str(OUT / "fresh-tasty-carousel.png"),
                    clip={
                        "x": 0,
                        "y": max(0, box["y"] - 20),
                        "width": 1440,
                        "height": min(620, 900 - max(0, box["y"] - 20)),
                    },
                )
        sizes = page.evaluate(
            """() => {
              const imgs = [...document.querySelectorAll('.qodef-woo-product-list .qodef-e-media-image img')].slice(0,8);
              return imgs.map(img => {
                const box = img.closest('.qodef-e-media-image')?.getBoundingClientRect();
                const src = img.src || '';
                return {
                  box: box ? {w: Math.round(box.width), h: Math.round(box.height)} : null,
                  src: src.split('/').pop(),
                  ai: src.includes('/ai-products/'),
                  legacy: src.includes('.webp') || src.includes('/hd-products/')
                };
              });
            }"""
        )
        print(sizes)
        browser.close()


if __name__ == "__main__":
    main()