import fs from 'node:fs';
import { chromium } from 'playwright';

const target = process.argv[2] || 'https://blog.mysqil.com/';
const outDir = 'tools/visual-out';
const viewports = [
	['desktop', 1440, 900],
	['desktop1280', 1280, 900],
	['mobile', 390, 844],
];

fs.mkdirSync(outDir, { recursive: true });

const browser = await chromium.launch({ headless: true });

for (const [name, width, height] of viewports) {
	const context = await browser.newContext({
		viewport: { width, height },
		deviceScaleFactor: 1,
	});
	const page = await context.newPage();
	const errors = [];
	page.on('console', (message) => {
		if (message.type() === 'error') errors.push(message.text());
	});

	await page.goto(target, { waitUntil: 'domcontentloaded', timeout: 60_000 });
	await page.waitForTimeout(3_500);

	const before = await page.evaluate(() => {
		const query = (selector) => document.querySelector(selector);
		const visible = (element) => {
			if (!element) return false;
			const style = getComputedStyle(element);
			const rect = element.getBoundingClientRect();
			return (
				style.display !== 'none' &&
				style.visibility !== 'hidden' &&
				rect.width > 0 &&
				rect.height > 0
			);
		};
		const elementInfo = (element) =>
			element
				? {
						visible: visible(element),
						id: element.id,
						aria: element.getAttribute('aria-label'),
						title: element.getAttribute('title'),
						className:
							typeof element.className === 'string' ? element.className : '',
						text: element.textContent.trim().replace(/\s+/g, ' ').slice(0, 180),
					}
				: null;

		const controlPattern =
			/layout|wallpaper|display settings|布局|壁纸|主题色/i;
		const buttons = Array.from(document.querySelectorAll('button'))
			.filter((button) =>
				controlPattern.test(
					[
						button.id,
						button.getAttribute('aria-label'),
						button.getAttribute('title'),
						button.textContent,
					].join(' '),
				),
			)
			.map(elementInfo);
		const visibleButtons = Array.from(document.querySelectorAll('button'))
			.filter(visible)
			.map(elementInfo);

		const main = query('.absolute.w-full.z-30.pointer-events-none');
		const grid = query('#main-grid');
		const card = query('.post-card-item, #post-list-container > div');
		const right = query('.right-sidebar-container');
		const panel = query('#display-setting');

		return {
			buttons,
			visibleButtons,
			displaySwitch: elementInfo(query('#display-settings-switch')),
			wallpaperSwitch: elementInfo(query('#wallpaper-mode-switch')),
			panel: elementInfo(panel),
			panelText:
				panel?.textContent.trim().replace(/\s+/g, ' ').slice(0, 500) || null,
			panelButtons: panel
				? Array.from(panel.querySelectorAll('button')).map(elementInfo)
				: [],
			bodyClass: document.body.className,
			main: main
				? {
						top: getComputedStyle(main).top,
						position: getComputedStyle(main).position,
						rectTop: main.getBoundingClientRect().top,
					}
				: null,
			grid: grid
				? {
						mode: grid.dataset.layoutMode,
						columns: getComputedStyle(grid).gridTemplateColumns,
					}
				: null,
			firstCardTop: card?.getBoundingClientRect().top ?? null,
			right: right
				? {
						visible: visible(right),
						className: right.className,
						display: getComputedStyle(right).display,
						rect: right.getBoundingClientRect().toJSON(),
					}
				: null,
		};
	});

	await page.screenshot({
		path: `${outDir}/upstream-baseline-${name}.png`,
		fullPage: false,
	});

	const settingsSwitch = page.locator('#display-settings-switch');
	if (await settingsSwitch.count()) {
		await settingsSwitch.click();
		await page.waitForTimeout(500);
	}

	const openPanel = await page.evaluate(() => {
		const panel = document.querySelector('#display-setting');
		if (!panel) return null;
		const style = getComputedStyle(panel);
		return {
			className: panel.className,
			display: style.display,
			opacity: style.opacity,
			visibility: style.visibility,
			rect: panel.getBoundingClientRect().toJSON(),
			text: panel.textContent.trim().replace(/\s+/g, ' ').slice(0, 800),
		};
	});

	await page.screenshot({
		path: `${outDir}/upstream-settings-${name}.png`,
		fullPage: false,
	});

	console.log(
		JSON.stringify(
			{ name, width, target, before, openPanel, errors: errors.slice(0, 8) },
			null,
			2,
		),
	);

	await context.close();
}

await browser.close();
