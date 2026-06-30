#!/usr/bin/env python3
"""Screenshot action holder area and test mobile viewport."""
from playwright.sync_api import sync_playwright

URLS = {
    "local": "http://localhost/nutterlyGood/",
    "greenpath": "https://greenpath.qodeinteractive.com/",
}
SEL = ".elementor-widget-greenpath_core_product_list .qodef-action-holder"

for viewport in [(1512, 900, "desktop"), (390, 844, "mobile")]:
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        for label, url in URLS.items():
            page = browser.new_page(viewport={"width": viewport[0], "height": viewport[1]})
            page.goto(url, wait_until="networkidle", timeout=90000)
            page.wait_for_selector(SEL, timeout=60000)
            holder = page.locator(SEL).first
            holder.scroll_into_view_if_needed()
            path = f"C:/xampp/htdocs/nutterlyGood/action-{label}-{viewport[2]}.png"
            holder.screenshot(path=path)
            data = holder.evaluate(
                """el => {
                    const cs = el => { const s=getComputedStyle(el); const r=el.getBoundingClientRect(); return {w:Math.round(r.width),h:Math.round(r.height),display:s.display,color:s.color}; };
                    return {
                        holder: cs(el),
                        cart: cs(el.querySelector('.add_to_cart_button')),
                        wish: el.querySelector('.qwfw-m-icon svg') ? cs(el.querySelector('.qwfw-m-icon svg')) : null,
                        qv: el.querySelector('.qqvfw-m-icon svg') ? cs(el.querySelector('.qqvfw-m-icon svg')) : null,
                        cmp: el.querySelector('.qcfw-button-icon svg') ? cs(el.querySelector('.qcfw-button-icon svg')) : null,
                        cartBasis: getComputedStyle(el.querySelector('.add_to_cart_button')).flexBasis,
                    };
                }"""
            )
            print(f"\n{label} {viewport[2]} -> {path}")
            print(data)
            page.close()
        browser.close()