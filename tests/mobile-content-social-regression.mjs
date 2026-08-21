import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium, firefox } from 'playwright';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const theme = path.join(root, 'theme', 'mizuki-wp');
const singleSource = fs.readFileSync(path.join(theme, 'single.php'), 'utf8');
const customizerSource = fs.readFileSync(path.join(theme, 'inc', 'customizer.php'), 'utf8');
const themeCss = [
	'mizuki-main.css',
	'mizuki-markdown-base.css',
	'mizuki-markdown-extend.css',
	'mizuki-twikoo.css',
	'mizuki-overrides.css',
].map((file) => fs.readFileSync(path.join(theme, 'assets', 'css', file), 'utf8')).join('\n');
const failures = [];

function check(condition, message) {
	if (!condition) failures.push(message);
}

const postContainerMatch = singleSource.match(/<div\s+id="post-container"\s+class="([^"]+)"/);
check(postContainerMatch !== null, 'single.php must render a literal #post-container class list.');

const postContainerClasses = postContainerMatch ? postContainerMatch[1].trim().split(/\s+/) : [];
check(
	postContainerClasses.includes('w-full'),
	'#post-container must retain upstream single-post width class w-full.'
);

const qqEntryMatch = customizerSource.match(/'qq'\s*=>\s*'(<svg[^']+<\/svg>)'/);
check(qqEntryMatch !== null, 'The social icon map must contain an inline QQ SVG.');

const qqSvg = qqEntryMatch?.[1] || '';
const qqPath = qqSvg.match(/<path\s+fill="currentColor"\s+d="([^"]+)"\s*\/>/)?.[1] || '';
const expectedQqPath = 'M530.1 484.4c-11.5 1.4-44.9-52.7-44.9-52.7c0 31.3-16.1 72.2-51 101.8c16.8 5.2 54.8 19.2 45.8 34.4c-7.3 12.3-125.5 7.9-159.6 4c-34.1 3.8-152.3 8.3-159.6-4c-9-15.2 28.9-29.2 45.8-34.4c-34.9-29.5-51.1-70.4-51.1-101.8c0 0-33.3 54.1-44.9 52.7c-5.4-.6-12.4-29.6 9.3-99.7c10.3-33 22-60.5 40.1-105.8c-3.1-116.9 45.2-215 160.3-215c113.7 0 163.2 96.1 160.3 215c18.1 45.2 29.9 72.9 40.1 105.8c21.8 70.1 14.7 99.1 9.3 99.7z';

check(qqSvg.includes('viewBox="0 0 640 640"'), 'QQ must use the fa7-brands:qq 640x640 viewBox.');
check(qqSvg.includes('width="1em"') && qqSvg.includes('height="1em"'), 'QQ must preserve the social icon 1em sizing contract.');
check(qqSvg.includes('fill="currentColor"'), 'QQ must inherit the existing social button color.');
check(qqPath === expectedQqPath, 'QQ must use the exact fa7-brands:qq path used by upstream Mizuki.');

async function verifyMobileContainment(browserType, browserName) {
	const browser = await browserType.launch({ headless: true });
	try {
		const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
		const page = await context.newPage();
		const longFilename = `${'mizuki-mobile-overflow-'.repeat(12)}package.apk`;
		const postClasses = postContainerClasses.join(' ');

		await page.setContent(`<!doctype html>
			<html lang="zh-CN">
			<head>
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<style>${themeCss}</style>
				<style>html, body { margin: 0; width: 100%; } body { padding: 7px; }</style>
			</head>
			<body>
				<article id="outer" class="flex w-full rounded-[var(--radius-large)] overflow-hidden relative mb-4">
					<div id="post-container" class="${postClasses}">
						<div id="content" class="prose dark:prose-invert prose-base !max-w-none custom-md mb-6 markdown-content onload-animation">
							<p id="ordinary">这是一段用于验证移动端正文边界的普通中文文本，卡片不能因为浏览器断行差异而超出屏幕。</p>
							<p><a href="#download">https://download.example.test/releases/${longFilename}</a></p>
						</div>
					</div>
				</article>
			</body>
			</html>`);

		const geometry = await page.evaluate(() => {
			const rect = (selector) => {
				const value = document.querySelector(selector).getBoundingClientRect();
				return { left: value.left, right: value.right, width: value.width };
			};
			return {
				outer: rect('#outer'),
				post: rect('#post-container'),
				content: rect('#content'),
				ordinary: rect('#ordinary'),
			};
		});

		const tolerance = 1;
		check(
			geometry.post.width <= geometry.outer.width + tolerance
				&& geometry.post.right <= geometry.outer.right + tolerance,
			`${browserName}: #post-container must stay within its mobile outer card (${JSON.stringify(geometry)}).`
		);
		check(
			geometry.content.right <= geometry.post.right + tolerance
				&& geometry.ordinary.right <= geometry.content.right + tolerance,
			`${browserName}: ordinary article text must remain inside the mobile post card (${JSON.stringify(geometry)}).`
		);

		await context.close();
	} finally {
		await browser.close();
	}
}

for (const [browserName, browserType] of [['Chromium', chromium], ['Firefox', firefox]]) {
	try {
		await verifyMobileContainment(browserType, browserName);
	} catch (error) {
		failures.push(`${browserName} mobile containment harness error: ${error.stack || error}`);
	}
}

if (failures.length > 0) {
	console.error('Mobile content/social regression: FAIL');
	for (const failure of failures) console.error(`- ${failure}`);
	process.exit(1);
}

console.log('Mobile content/social regression: PASS');
