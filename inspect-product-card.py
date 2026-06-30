#!/usr/bin/env python3
"""Deep inspect product carousel card vs GreenPath."""
from playwright.sync_api import sync_playwright

URLS = {
    "local": "http://localhost/nutterlyGood/",
    "greenpath": "https://greenpath.qodeinteractive.com/",
}

WIDGET_SEL = ".elementor-widget-greenpath_core_product_list"
ACTION_SEL = f"{WIDGET_SEL} .qodef-action-holder"
ITEM_SEL = f"{WIDGET_SEL} .qodef-woo-product-list .qodef-e"


def inspect(page, label):
    page.goto(URLS[label], wait_until="networkidle", timeout=90000)
    page.wait_for_selector(ACTION_SEL, timeout=60000)

    item = page.locator(ITEM_SEL).first
    item.scroll_into_view_if_needed()
    card_path = f"C:/xampp/htdocs/nutterlyGood/product-card-{label}.png"
    item.screenshot(path=card_path)

    data = page.locator(ACTION_SEL).first.evaluate(
        """el => {
            const pick = (node) => {
                if (!node) return null;
                const cs = getComputedStyle(node);
                const rect = node.getBoundingClientRect();
                const svg = node.tagName === 'svg' ? node : node.querySelector('svg');
                return {
                    classes: node.className,
                    text: (node.textContent || '').trim().slice(0, 50),
                    display: cs.display,
                    visibility: cs.visibility,
                    opacity: cs.opacity,
                    width: cs.width,
                    height: cs.height,
                    rectW: Math.round(rect.width),
                    rectH: Math.round(rect.height),
                    padding: cs.padding,
                    marginTop: cs.marginTop,
                    backgroundColor: cs.backgroundColor,
                    color: cs.color,
                    border: cs.border,
                    order: cs.order,
                    flexBasis: cs.flexBasis,
                    fontSize: cs.fontSize,
                    hasSvg: !!svg,
                    svgFill: svg ? getComputedStyle(svg).fill : null,
                    svgVisible: svg ? (svg.getBoundingClientRect().width > 0) : false,
                };
            };
            return {
                holder: pick(el),
                addToCart: pick(el.querySelector('.add_to_cart_button, .button.product_type_simple')),
                wishlistWrap: pick(el.querySelector('.qwfw-add-to-wishlist-wrapper')),
                wishlistBtn: pick(el.querySelector('.qwfw-add-to-wishlist')),
                wishlistIcon: pick(el.querySelector('.qwfw-m-icon')),
                quickViewWrap: pick(el.querySelector('.qqvfw-quick-view-button-wrapper')),
                quickViewBtn: pick(el.querySelector('.qqvfw-quick-view-button')),
                quickViewIcon: pick(el.querySelector('.qqvfw-m-icon')),
                compareBtn: pick(el.querySelector('.qcfw-button')),
                compareIcon: pick(el.querySelector('.qcfw-button-icon')),
            };
        }"""
    )

    css_loaded = page.evaluate(
        """() => [...document.querySelectorAll('link[rel="stylesheet"]')]
            .map(l => l.href)
            .filter(h => /qode-greenpath|greenpath-main|greenpath-core|qwfw|qqvfw|qcfw/.test(h))
        """
    )

    return data, css_loaded, card_path


with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    for label in URLS:
        page = browser.new_page(viewport={"width": 1512, "height": 900})
        try:
            data, css, card_path = inspect(page, label)
            print(f"\n=== {label} ===")
            print(f"card screenshot: {card_path}")
            print(f"stylesheets: {css}")
            for key, val in data.items():
                print(f"\n{key}:")
                if val:
                    for k, v in val.items():
                        print(f"  {k}: {v}")
                else:
                    print("  (missing)")
        except Exception as exc:
            print(f"\n=== {label} FAILED ===\n{exc}")
        page.close()
    browser.close()