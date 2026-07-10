import fs from 'node:fs';
import { chromium } from 'playwright';

const target = process.argv[2] || 'https://blog.mysqil.com/';
const outDir = 'tools/visual-out';
fs.mkdirSync(outDir, { recursive: true });

const browser = await chromium.launch({ headless: true });
const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
const page = await context.newPage();
await page.goto(target, { waitUntil: 'domcontentloaded', timeout: 60_000 });
await page.waitForTimeout(3_500);

async function capture(index) {
	const metrics = await page.evaluate(() => {
		const get = (selector) => document.querySelector(selector);
		const rect = (element) => element?.getBoundingClientRect().toJSON() || null;
		const main = get('.absolute.w-full.z-30.pointer-events-none');
		const banner = get('#banner-wrapper');
		const card = get('.post-card-item, #post-list-container > div');
		const switchButton = get('#wallpaper-mode-switch');
		return {
			bodyClass: document.body.className,
			main: main ? { position: getComputedStyle(main).position, top: getComputedStyle(main).top, rect: rect(main) } : null,
			banner: banner ? { position: getComputedStyle(banner).position, display: getComputedStyle(banner).display, rect: rect(banner) } : null,
			firstCard: rect(card),
			switchLabel: switchButton?.getAttribute('aria-label') || null,
			storedMode: localStorage.getItem('wallpaperMode'),
		};
	});
	await page.screenshot({ path: `${outDir}/upstream-wallpaper-${index}.png`, fullPage: false });
	console.log(JSON.stringify({ index, metrics }, null, 2));
}

await capture(0);
await page.locator('#wallpaper-mode-switch').click();
await page.waitForTimeout(300);
const menu = await page.evaluate(() => ({
	wallpaperElements: Array.from(document.querySelectorAll('[id*="wallpaper"], [class*="wallpaper"]')).map((element) => ({
		id: element.id,
		className: typeof element.className === 'string' ? element.className : '',
		text: element.textContent.trim().replace(/\s+/g, ' ').slice(0, 120),
		display: getComputedStyle(element).display,
		visibility: getComputedStyle(element).visibility,
	})),
	visibleMenuButtons: Array.from(document.querySelectorAll('button')).filter((button) => {
		const style = getComputedStyle(button);
		return style.display !== 'none' && style.visibility !== 'hidden' && button.getBoundingClientRect().width > 0;
	}).map((button) => button.textContent.trim().replace(/\s+/g, ' ')).filter(Boolean),
}));
console.log(JSON.stringify({ menu }, null, 2));

const modeLabels = ['全屏模式', '隐藏壁纸', '横幅模式'];
for (let index = 1; index <= modeLabels.length; index += 1) {
	await page.evaluate(() => localStorage.clear());
	await page.reload({ waitUntil: 'domcontentloaded', timeout: 60_000 });
	await page.waitForTimeout(1_000);
	await page.locator('#wallpaper-mode-switch').click();
	await page.getByRole('menuitem', { name: modeLabels[index - 1], exact: true }).click();
	await page.waitForTimeout(700);
	await capture(index);
}

await browser.close();
