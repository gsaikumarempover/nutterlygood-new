#!/usr/bin/env python3
from playwright.sync_api import sync_playwright

for label, url in {
    "local": "http://localhost/nutterlyGood/",
    "greenpath": "https://greenpath.qodeinteractive.com/",
}.items():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={"width": 1512, "height": 900})
        page.goto(url, wait_until="networkidle", timeout=90000)
        page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
        page.wait_for_timeout(1500)

        data = page.evaluate(
            """() => {
                const footer = document.querySelector('#qodef-page-footer');
                const btt = document.querySelector('#qodef-back-to-top');
                return {
                    hasFooter: !!footer,
                    footerClasses: footer ? footer.className : null,
                    footerHTML: footer ? footer.innerHTML.slice(0, 800) : null,
                    hasBtt: !!btt,
                    bttClasses: btt ? btt.className : null,
                    bttDisplay: btt ? getComputedStyle(btt).display : null,
                    bttVisibility: btt ? getComputedStyle(btt).visibility : null,
                    bttOpacity: btt ? getComputedStyle(btt).opacity : null,
                    bttPointer: btt ? getComputedStyle(btt).pointerEvents : null,
                    bodyBttClass: [...document.body.classList].filter(c => c.includes('back-to-top')),
                    gsap: typeof gsap !== 'undefined',
                    qodefScroll: typeof qodef !== 'undefined' ? qodef.scroll : null,
                };
            }"""
        )
        print(f"\n=== {label} ===")
        for k, v in data.items():
            if k != "footerHTML":
                print(f"{k}: {v}")
        page.locator("#qodef-page-footer").screenshot(path=f"C:/xampp/htdocs/nutterlyGood/footer-{label}.png")
        if data["hasBtt"]:
            page.locator("#qodef-back-to-top").screenshot(path=f"C:/xampp/htdocs/nutterlyGood/btt-{label}.png")
        browser.close()