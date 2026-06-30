#!/usr/bin/env python3
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1512, "height": 900})
    page.goto("http://localhost/nutterlyGood/", wait_until="networkidle", timeout=90000)
    page.locator(".elementor-widget-greenpath_core_product_list").first.scroll_into_view_if_needed()

    holder = page.locator(".qodef-woo-product-list.qodef-swiper-container .qodef-action-holder").first
    btn = holder.locator(".add_to_cart_button")
    carousel = page.locator(".qodef-woo-product-list.qodef-swiper-container").first

    holder.hover()
    page.wait_for_timeout(300)
    print("autoplay after hover:", carousel.evaluate("el => el.swiper.autoplay.running"))

    btn.click(force=True)
    page.wait_for_timeout(250)
    print("classes after click:", btn.evaluate("el => el.className"))
    print("label after click:", btn.evaluate("el => el.querySelector('.qodef-m-text').textContent").strip())

    page.wait_for_timeout(1200)
    print("label after wait:", btn.evaluate("el => el.querySelector('.qodef-m-text').textContent").strip())
    print("autoplay resumed:", carousel.evaluate("el => el.swiper.autoplay.running"))
    browser.close()