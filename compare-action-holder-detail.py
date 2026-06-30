#!/usr/bin/env python3
from playwright.sync_api import sync_playwright

SELECTOR = ".elementor-widget-greenpath_core_product_list .qodef-action-holder"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    for label, url in {
        "local": "http://localhost/nutterlyGood/",
        "greenpath": "https://greenpath.qodeinteractive.com/",
    }.items():
        page = browser.new_page(viewport={"width": 1512, "height": 900})
        page.goto(url, wait_until="networkidle", timeout=90000)
        page.wait_for_selector(SELECTOR, timeout=60000)
        data = page.locator(SELECTOR).first.evaluate(
            """el => {
                const pick = (node, extra=[]) => {
                    const cs = getComputedStyle(node);
                    return {
                        classes: node.className,
                        text: (node.textContent || '').trim().slice(0, 40),
                        display: cs.display,
                        padding: cs.padding,
                        marginTop: cs.marginTop,
                        backgroundColor: cs.backgroundColor,
                        color: cs.color,
                        border: cs.border,
                        width: cs.width,
                        height: cs.height,
                        order: cs.order,
                        fontSize: cs.fontSize,
                    };
                };
                return {
                    holder: pick(el),
                    addToCart: pick(el.querySelector('.add_to_cart_button, .button.product_type_simple, .button.product_type_variable')),
                    wishlist: pick(el.querySelector('.qwfw-add-to-wishlist-wrapper')),
                    quickView: pick(el.querySelector('.qqvfw-quick-view-button')),
                    compare: pick(el.querySelector('.qcfw-button')),
                };
            }"""
        )
        print(f"=== {label} ===")
        for key, val in data.items():
            print(key, val)
        page.close()
    browser.close()