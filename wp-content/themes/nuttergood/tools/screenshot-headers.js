const { chromium } = require('playwright');

(async () => {
	const browser = await chromium.launch();
	const page = await browser.newPage({ viewport: { width: 1440, height: 220 } });
	const urls = [
		['home', 'http://localhost/nutterlyGood/'],
		['about', 'http://localhost/nutterlyGood/about-us/'],
		['contact', 'http://localhost/nutterlyGood/contact/'],
	];
	for (const [name, url] of urls) {
		await page.goto(url, { waitUntil: 'networkidle' });
		await page.waitForTimeout(1200);
		await page.screenshot({ path: `C:/xampp/htdocs/nutterlyGood/tools/header-${name}.png` });
	}
	await browser.close();
})();