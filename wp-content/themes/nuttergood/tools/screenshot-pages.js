const { chromium } = require('playwright');

(async () => {
	const browser = await chromium.launch();
	const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });

	await page.goto('http://localhost/nutterlyGood/contact/', { waitUntil: 'networkidle' });
	await page.waitForTimeout(3000);
	await page.screenshot({ path: 'C:/xampp/htdocs/nutterlyGood/tools/screenshot-contact-viewport.png' });

	await page.goto('http://localhost/nutterlyGood/about-us/', { waitUntil: 'networkidle' });
	await page.waitForTimeout(1500);
	await page.screenshot({ path: 'C:/xampp/htdocs/nutterlyGood/tools/screenshot-about-viewport.png', fullPage: true });

	await browser.close();
})();