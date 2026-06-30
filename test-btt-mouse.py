#!/usr/bin/env python3
from playwright.sync_api import sync_playwright

url = "http://localhost/nutterlyGood/"
with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1512, "height": 900})
    page.goto(url, wait_until="networkidle", timeout=90000)
    page.evaluate("window.scrollTo(0, 5000)")
    page.wait_for_timeout(1500)

    before = page.evaluate("() => window.scrollY")
    box = page.locator("#qodef-back-to-top").bounding_box()
    print("Before:", before, "box:", box)

    if box:
        page.mouse.click(box["x"] + box["width"] / 2, box["y"] + box["height"] / 2)

    for ms in [500, 1500, 3000]:
        page.wait_for_timeout(ms)
        y = page.evaluate("() => window.scrollY")
        print(f"After {ms}ms: scrollY={y}")

    page.locator("#qodef-page-footer").screenshot(path="C:/xampp/htdocs/nutterlyGood/footer-local-fixed.png")
    browser.close()