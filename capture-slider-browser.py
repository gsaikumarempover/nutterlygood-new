"""Capture homepage header + slider after Farmley update."""
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
        page.wait_for_timeout(6000)
        page.screenshot(path=str(OUT / "farmley-homepage.png"), full_page=False)
        promo = page.locator("#ng-farmley-promo")
        if promo.count():
            promo.screenshot(path=str(OUT / "farmley-promo-bar.png"))
        wrap = page.locator("rs-module-wrap").first
        if wrap.count():
            wrap.screenshot(path=str(OUT / "farmley-slider-wrap.png"), timeout=10000)
        info = page.evaluate(
            """() => ({
              fatal: document.body.textContent.includes('Fatal error'),
              promo: !!document.querySelector('#ng-farmley-promo'),
              topArea: !!document.querySelector('#qodef-top-area'),
              wrapH: document.querySelector('rs-module-wrap')?.getBoundingClientRect().height,
              moduleVis: document.querySelector('rs-module-wrap')?.style.visibility,
              sliderH: document.querySelector('rs-module')?.getBoundingClientRect().height,
              texts: [...document.querySelectorAll('rs-layer')].map(e => e.textContent.trim()).filter(Boolean).slice(0,8)
            })"""
        )
        print(info)
        browser.close()


if __name__ == "__main__":
    main()