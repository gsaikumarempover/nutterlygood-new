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
                const top = document.querySelector('#qodef-page-footer-top-area');
                const bottom = document.querySelector('#qodef-page-footer-bottom-area');
                const col1 = document.querySelector('#qodef-page-footer-top-area .qodef-grid-item:nth-child(1)');
                const btt = document.querySelector('#qodef-back-to-top');
                const getVis = (el) => el ? {
                    display: getComputedStyle(el).display,
                    visibility: getComputedStyle(el).visibility,
                    opacity: getComputedStyle(el).opacity,
                    height: el.offsetHeight,
                    width: el.offsetWidth,
                } : null;
                return {
                    footerHeight: footer ? footer.offsetHeight : 0,
                    topHeight: top ? top.offsetHeight : 0,
                    bottomHeight: bottom ? bottom.offsetHeight : 0,
                    col1HTML: col1 ? col1.innerHTML.slice(0, 1200) : null,
                    col1Vis: getVis(col1),
                    bottomHTML: bottom ? bottom.innerHTML.slice(0, 800) : null,
                    footerSkin: footer ? footer.className : null,
                    topClasses: top ? top.className : null,
                    topInnerClasses: document.querySelector('#qodef-page-footer-top-area-inner')?.className,
                    bttZ: btt ? getComputedStyle(btt).zIndex : null,
                    elementAtBtt: (() => {
                        if (!btt) return null;
                        const r = btt.getBoundingClientRect();
                        const el = document.elementFromPoint(r.left + r.width/2, r.top + r.height/2);
                        return el ? { tag: el.tagName, id: el.id, class: el.className } : null;
                    })(),
                };
            }"""
        )
        print(f"\n=== {label} ===")
        for k, v in data.items():
            print(f"{k}:")
            print(v)
            print()
        browser.close()