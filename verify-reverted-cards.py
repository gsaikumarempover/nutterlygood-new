"""Verify product cards reverted to default GreenPath layout."""
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
        page.wait_for_timeout(5000)
        info = page.evaluate(
            """() => {
              const media = document.querySelector('.elementor-element-a463981 .qodef-e-media-image');
              const img = document.querySelector('.elementor-element-a463981 .qodef-e-media-image img');
              const inner = document.querySelector('.elementor-element-a463981 .qodef-e-inner');
              return {
                bodyFarmley: document.body.className.includes('ng-farmley-product-cards'),
                farmleyCss: !!document.querySelector('link[href*="farmley-product-cards.css"]'),
                hasFarmleyCard: !!document.querySelector('.ng-farmley-card'),
                hasFarmleyMedia: !!document.querySelector('.ng-farmley-card-media'),
                innerClass: inner ? inner.className : null,
                mediaBg: media ? getComputedStyle(media).backgroundColor : null,
                mediaPadding: media ? getComputedStyle(media).padding : null,
                imgSrc: img ? img.src.split('/').pop() : null,
              };
            }"""
        )
        print(info)
        page.locator(".elementor-element-a463981").first.screenshot(
            path=str(OUT / "reverted-cards.png")
        )
        browser.close()


if __name__ == "__main__":
    main()