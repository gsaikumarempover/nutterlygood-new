#!/usr/bin/env python3
"""Compare product carousel action-holder layout on local vs GreenPath."""
from playwright.sync_api import sync_playwright

URLS = {
    "local": "http://localhost/nutterlyGood/",
    "greenpath": "https://greenpath.qodeinteractive.com/",
}

SELECTOR = ".elementor-widget-greenpath_core_product_list .qodef-action-holder"


def inspect(page, label):
    page.goto(URLS[label], wait_until="networkidle", timeout=90000)
    page.wait_for_selector(SELECTOR, timeout=60000)
    holder = page.locator(SELECTOR).first
    styles = holder.evaluate(
        """el => {
            const cs = getComputedStyle(el);
            const kids = [...el.children].map(c => ({
                tag: c.tagName,
                classes: c.className,
                display: getComputedStyle(c).display,
                order: getComputedStyle(c).order,
                marginTop: getComputedStyle(c).marginTop,
            }));
            return {
                display: cs.display,
                flexWrap: cs.flexWrap,
                gap: cs.gap,
                alignItems: cs.alignItems,
                marginTop: cs.marginTop,
                childCount: el.children.length,
                children: kids,
            };
        }"""
    )
    path = f"C:/xampp/htdocs/nutterlyGood/action-holder-{label}.png"
    holder.screenshot(path=path)
    return styles, path


with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    for label in URLS:
        page = browser.new_page(viewport={"width": 1512, "height": 900})
        try:
            styles, path = inspect(page, label)
            print(f"=== {label} ===")
            print(styles)
            print(f"screenshot: {path}")
        except Exception as exc:
            print(f"=== {label} FAILED ===")
            print(exc)
        page.close()
    browser.close()