const { chromium } = require('playwright');

(async () => {
	const browser = await chromium.launch();
	const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
	await page.goto('http://localhost/nutterlyGood/', { waitUntil: 'networkidle' });
	await page.waitForTimeout(2000);

	const qvBtn = page.locator('.qqvfw-quick-view-button').first();
	if (await qvBtn.count()) {
		await qvBtn.click({ force: true });
		await page.waitForTimeout(1500);
		await page.screenshot({ path: 'C:/xampp/htdocs/nutterlyGood/tools/quick-view-open.png' });

		const drawer = page.locator('#qode-quick-view-for-woocommerce-pop-up.ng-farmley-qv-drawer.qqvfw--opened');
		const mediaWidth = await drawer.locator('.qqvfw-m-media-wrapper').first().evaluate((el) => {
			return window.getComputedStyle(el).width;
		});
		const scrollInfo = await drawer.locator('.ng-farmley-qv').first().evaluate((el) => {
			return {
				scrollHeight: el.scrollHeight,
				clientHeight: el.clientHeight,
				overflowY: window.getComputedStyle(el).overflowY,
				canScroll: el.scrollHeight > el.clientHeight,
			};
		});
		console.log('media width:', mediaWidth);
		console.log('scroll:', JSON.stringify(scrollInfo));

		if (scrollInfo.canScroll) {
			await drawer.locator('.ng-farmley-qv').first().evaluate((el) => {
				el.scrollTop = 200;
			});
			await page.waitForTimeout(300);
			await page.screenshot({ path: 'C:/xampp/htdocs/nutterlyGood/tools/quick-view-scrolled.png' });
		}
	} else {
		console.log('No quick view button found');
	}

	await browser.close();
})();