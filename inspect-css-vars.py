#!/usr/bin/env python3
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1512, "height": 900})
    page.goto("http://localhost/nutterlyGood/", wait_until="networkidle", timeout=90000)
    page.wait_for_selector(".qodef-action-holder", timeout=60000)

    data = page.evaluate(
        """() => {
            const root = getComputedStyle(document.documentElement);
            const holder = document.querySelector('.qodef-action-holder');
            const hs = holder ? getComputedStyle(holder) : null;
            const pickRules = (el, label) => {
                if (!el) return null;
                const cs = getComputedStyle(el);
                return {
                    label,
                    display: cs.display,
                    visibility: cs.visibility,
                    opacity: cs.opacity,
                    overflow: cs.overflow,
                    width: cs.width,
                    height: cs.height,
                    color: cs.color,
                    border: cs.border,
                    background: cs.backgroundColor,
                    zIndex: cs.zIndex,
                    position: cs.position,
                };
            };
            const qvIcon = document.querySelector('.qqvfw-m-icon');
            const cmpIcon = document.querySelector('.qcfw-button-icon');
            const wishIcon = document.querySelector('.qwfw-m-icon');
            const qvSvg = qvIcon?.querySelector('svg');
            const cmpSvg = cmpIcon?.querySelector('svg');
            const wishSvg = wishIcon?.querySelector('svg');
            return {
                qodeMainColor: root.getPropertyValue('--qode-main-color').trim(),
                holderWidth: hs?.width,
                stylesheets: [...document.styleSheets].map(s => s.href).filter(Boolean).slice(-15),
                qodeGreenpathLoaded: !!document.querySelector('link[href*="qode-greenpath-product-list"]'),
                wishIcon: pickRules(wishIcon, 'wish'),
                qvIcon: pickRules(qvIcon, 'qv'),
                cmpIcon: pickRules(cmpIcon, 'cmp'),
                wishSvg: qvSvg ? null : pickRules(wishSvg, 'wishSvg'),
                qvSvg: pickRules(qvSvg, 'qvSvg'),
                cmpSvg: pickRules(cmpSvg, 'cmpSvg'),
                qvSvgPaths: qvSvg ? qvSvg.querySelectorAll('path').length : 0,
                cmpSvgPaths: cmpSvg ? cmpSvg.querySelectorAll('path').length : 0,
                wishSvgPaths: wishSvg ? wishSvg.querySelectorAll('path').length : 0,
            };
        }"""
    )
    print(data)

    # wider screenshot with parent card
    item = page.locator(".elementor-widget-greenpath_core_product_list .qodef-e").first
    item.evaluate("el => el.style.outline = '2px solid red'")
    page.locator(".qodef-action-holder").first.evaluate("el => el.style.outline = '2px solid blue'")
    page.screenshot(path="C:/xampp/htdocs/nutterlyGood/debug-product-actions.png", full_page=False)

    browser.close()