"""Inspect grey backgrounds on Fresh Tasty Wholesome product cards."""
from pathlib import Path

from playwright.sync_api import sync_playwright

URL = "http://localhost/nutterlyGood/"
OUT = Path(r"C:\xampp\htdocs\nutterlyGood\slider-browser-check")
OUT.mkdir(parents=True, exist_ok=True)


def main() -> None:
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={"width": 1440, "height": 900})
        page.goto(URL, wait_until="networkidle", timeout=90000)
        page.wait_for_timeout(5000)

        title = page.locator("text=Fresh, Tasty, and Wholesome").first
        if title.count():
            title.scroll_into_view_if_needed()
            page.wait_for_timeout(1500)

        # Full section with title + cards
        section = page.locator(".elementor-element-47bd4b6").first
        if section.count():
            section.screenshot(path=str(OUT / "wholesome-full-section.png"))

        carousel = page.locator(".elementor-element-a463981").first
        if carousel.count():
            carousel.screenshot(path=str(OUT / "wholesome-carousel-live.png"))

        # Single card close-up
        card = page.locator(".elementor-element-a463981 .product").first
        if card.count():
            card.screenshot(path=str(OUT / "wholesome-single-card.png"))

        report = page.evaluate(
            """() => {
              const card = document.querySelector('.elementor-element-a463981 .product');
              if (!card) return { error: 'no card found' };

              const pick = (el, label) => {
                if (!el) return { label, missing: true };
                const cs = getComputedStyle(el);
                const r = el.getBoundingClientRect();
                return {
                  label,
                  tag: el.tagName,
                  className: el.className,
                  bg: cs.backgroundColor,
                  bgImage: cs.backgroundImage,
                  padding: cs.padding,
                  margin: cs.margin,
                  border: cs.border,
                  borderRadius: cs.borderRadius,
                  boxShadow: cs.boxShadow,
                  w: Math.round(r.width),
                  h: Math.round(r.height),
                };
              };

              const img = card.querySelector('.qodef-e-media-image img');
              const layers = [
                pick(card, 'li.product'),
                pick(card.querySelector('.qodef-e-inner'), 'qodef-e-inner'),
                pick(card.querySelector('.qodef-e-media'), 'qodef-e-media'),
                pick(card.querySelector('.qodef-e-media-image'), 'qodef-e-media-image'),
                pick(card.querySelector('.qodef-e-media-image a'), 'media-image a'),
                pick(img, 'img'),
                pick(card.querySelector('.qodef-e-content'), 'qodef-e-content'),
                pick(document.querySelector('.elementor-element-a463981'), 'widget a463981'),
                pick(document.querySelector('.elementor-element-47bd4b6'), 'section 47bd4b6'),
                pick(document.querySelector('#qodef-page-content'), 'page-content'),
                pick(document.body, 'body'),
              ];

              const inline = document.querySelector('#greenpath-style-inline-css');
              const hasWhiteRules = inline ? inline.textContent.includes('elementor-element-a463981') : false;

              return {
                title: document.querySelector('.elementor-element-abca240')?.textContent?.trim(),
                layers,
                hasInlineRules: hasWhiteRules,
                imgSrc: img?.currentSrc || img?.src,
              };
            }"""
        )

        print("=== STYLE REPORT ===")
        for layer in report.get("layers", []):
            if layer.get("missing"):
                print(f"{layer['label']}: MISSING")
            else:
                print(
                    f"{layer['label']}: bg={layer['bg']} border={layer['border']} "
                    f"pad={layer['padding']} {layer['w']}x{layer['h']}"
                )
        print("title:", report.get("title"))
        print("inline rules:", report.get("hasInlineRules"))
        print("img:", report.get("imgSrc", "")[-80:])

        browser.close()


if __name__ == "__main__":
    main()