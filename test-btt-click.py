#!/usr/bin/env python3
from playwright.sync_api import sync_playwright

url = "http://localhost/nutterlyGood/"
with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1512, "height": 900})
    page.goto(url, wait_until="networkidle", timeout=90000)

    # Force deep scroll
    page.evaluate(
        """async () => {
            for (let i = 0; i < 20; i++) {
                window.scrollBy(0, 500);
                await new Promise(r => setTimeout(r, 50));
            }
        }"""
    )
    page.wait_for_timeout(1000)

    state = page.evaluate(
        """() => {
            const btt = document.querySelector('#qodef-back-to-top');
            const events = (typeof jQuery !== 'undefined' && jQuery._data)
                ? jQuery._data(btt, 'events') : null;
            return {
                scrollY: window.scrollY,
                qodefScroll: typeof qodef !== 'undefined' ? qodef.scroll : 'missing',
                qodefCoreScroll: typeof qodefCore !== 'undefined' ? qodefCore.scroll : 'missing',
                bttClasses: btt ? btt.className : null,
                bttOn: btt ? btt.classList.contains('qodef--on') : false,
                hasQodefBackToTop: typeof qodefBackToTop !== 'undefined',
                clickEvents: events ? Object.keys(events) : null,
                scripts: [...document.querySelectorAll('script[src]')]
                    .map(s => s.src)
                    .filter(s => s.includes('greenpath') || s.includes('main')),
            };
        }"""
    )
    print("State before click:")
    for k, v in state.items():
        print(f"  {k}: {v}")

    # Trigger click via jQuery like the plugin does
    result = page.evaluate(
        """() => {
            const before = window.scrollY;
            try {
                if (typeof qodefBackToTop !== 'undefined') {
                    qodefBackToTop.animateScrollToTop();
                } else {
                    document.querySelector('#qodef-back-to-top').click();
                }
            } catch (e) {
                return { error: e.toString(), before };
            }
            return { before, afterImmediate: window.scrollY, qodefScroll: qodef.scroll };
        }"""
    )
    print("Direct animateScrollToTop:", result)

    for ms in [200, 500, 1000, 2000, 3000]:
        page.wait_for_timeout(ms)
        y = page.evaluate("() => window.scrollY")
        print(f"  scrollY +{ms}ms: {y}")

    browser.close()