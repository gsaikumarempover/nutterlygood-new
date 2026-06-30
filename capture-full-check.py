"""Verify AI images on product + category carousels."""
from pathlib import Path

from playwright.sync_api import sync_playwright

URL = "http://localhost/nutterlyGood/"
OUT = Path(r"C:\xampp\htdocs\nutterlyGood\slider-browser-check")
OUT.mkdir(parents=True, exist_ok=True)


def main() -> None:
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={"width": 1440, "height": 1400})
        page.goto(URL, wait_until="networkidle", timeout=60000)
        page.wait_for_timeout(4000)

        cat = page.locator(".qodef-woo-product-category-list").first
        if cat.count():
            cat.scroll_into_view_if_needed()
            page.wait_for_timeout(800)
            cat.screenshot(path=str(OUT / "category-cards-ai.png"))

        info = page.evaluate(
            """() => {
              const prod = [...document.querySelectorAll('.qodef-woo-product-list img')].map(i => ({
                src: i.src.split('/').pop(),
                ai: i.src.includes('/ai-products/'),
                legacy: i.src.includes('.webp') || i.src.includes('/hd-products/') || i.src.includes('/category-thumbs/')
              }));
              const cats = [...document.querySelectorAll('.qodef-woo-product-category-list img')].map(i => ({
                src: i.src.split('/').pop(),
                ai: i.src.includes('/ai-categories/'),
                legacy: i.src.includes('.webp') || i.src.includes('ng-cat-')
              }));
              const catBoxes = [...document.querySelectorAll('.qodef-woo-product-category-list .qodef-e-image-holder')].slice(0,10).map(el => {
                const r = el.getBoundingClientRect();
                return {w: Math.round(r.width), h: Math.round(r.height)};
              });
              return {
                productCount: prod.length,
                productAi: prod.filter(x => x.ai).length,
                productLegacy: prod.filter(x => x.legacy).length,
                categoryCount: cats.length,
                categoryAi: cats.filter(x => x.ai).length,
                categoryLegacy: cats.filter(x => x.legacy).length,
                catBoxes
              };
            }"""
        )
        print(info)
        browser.close()


if __name__ == "__main__":
    main()