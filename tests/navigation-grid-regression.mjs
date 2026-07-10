import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const cssPath = path.join(root, 'theme', 'mizuki-wp', 'assets', 'css', 'mizuki-overrides.css');
const mainCssPath = path.join(root, 'theme', 'mizuki-wp', 'assets', 'css', 'mizuki-main.css');
const scriptPath = path.join(root, 'theme', 'mizuki-wp', 'assets', 'js', 'mizuki-theme.js');
const css = fs.readFileSync(cssPath, 'utf8');
const mainCss = fs.readFileSync(mainCssPath, 'utf8');
const script = fs.readFileSync(scriptPath, 'utf8');
const failures = [];

function check(condition, message) {
	if (!condition) failures.push(message);
}

const rules = [...css.matchAll(/([^{}]+)\{([^{}]*)\}/gs)].map((match) => ({
	selector: match[1].replace(/\/\*[\s\S]*?\*\//g, ' ').replace(/\s+/g, ' ').trim(),
	body: match[2],
}));

const hoverSelector = '.dropdown-container:hover .dropdown-menu';
const focusSelector = '.dropdown-container:focus-within .dropdown-menu';
const ariaSelector = '.dropdown-trigger[aria-expanded="true"] + .dropdown-menu';

const hiddenHoverOverride = rules.some(({ selector, body }) =>
	(selector.includes(hoverSelector) || selector.includes(focusSelector))
	&& /opacity\s*:\s*0(?:\D|$)/.test(body)
);

const openHoverRule = rules.some(({ selector, body }) =>
	selector.includes(hoverSelector)
	&& selector.includes(focusSelector)
	&& selector.includes(ariaSelector)
	&& /opacity\s*:\s*1(?:\D|$)/.test(body)
	&& /visibility\s*:\s*visible\s*;?/.test(body)
	&& /pointer-events\s*:\s*auto\s*;?/.test(body)
);

check(!hiddenHoverOverride, 'Desktop hover/focus menus must not be forced closed.');
check(openHoverRule, 'Hover, focus-within, and aria-expanded must share one visible menu rule.');
check(
	/\.post-list-container\.grid-mode\s+\.post-card-title\s*\{[^}]*position\s*:\s*relative\s*;?/s.test(css),
	'Each grid title must own the accent positioning context.'
);
check(
	/\.post-list-container\.grid-mode\s+\.post-card-title::before\s*\{[^}]*top\s*:\s*50%\s*;?[^}]*transform\s*:\s*translateY\(-50%\)\s*;?/s.test(css),
	'The grid title accent must be vertically centered against its title.'
);

function isVisible(state) {
	return state.opacity === '1'
		&& state.visibility === 'visible'
		&& state.pointerEvents === 'auto';
}

function isHidden(state) {
	return state.opacity === '0'
		&& state.visibility === 'hidden'
		&& state.pointerEvents === 'none';
}

async function runDropdownInteractionRegression() {
	const browser = await chromium.launch({ headless: true });
	try {
		const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
		const page = await context.newPage();
		const pageErrors = [];
		page.on('pageerror', (error) => pageErrors.push(error.message));
		await page.route('http://navigation.test/', (route) => route.fulfill({
			contentType: 'text/html',
			body: `<!doctype html>
				<html>
				<head><style>${mainCss}\n${css}</style></head>
				<body>
					<div id="navbar-links-container">
						<div class="dropdown-container group" data-dropdown>
							<button class="dropdown-trigger" aria-expanded="false" data-dropdown-trigger>My</button>
							<div class="dropdown-menu" data-dropdown-menu>
								<a class="dropdown-item" href="#item">Item</a>
							</div>
						</div>
					</div>
					<details id="mobile-navigation"><summary>Menu</summary><a href="#mobile">Mobile item</a></details>
					<script>${script.replace(/<\/script/gi, '<\\/script')}</script>
				</body>
				</html>`,
		}));
		await page.goto('http://navigation.test/', { waitUntil: 'load' });

		const trigger = page.locator('[data-dropdown-trigger]');
		const readState = async () => {
			await page.waitForTimeout(230);
			return trigger.evaluate((button) => {
				const menu = button.nextElementSibling;
				const style = getComputedStyle(menu);
				return {
					ariaExpanded: button.getAttribute('aria-expanded'),
					active: document.activeElement === button,
					opacity: style.opacity,
					visibility: style.visibility,
					pointerEvents: style.pointerEvents,
				};
			});
		};

		await trigger.click();
		const firstClick = await readState();
		check(firstClick.ariaExpanded === 'true' && isVisible(firstClick), 'The first desktop click must open the menu.');

		await trigger.click();
		const secondClick = await readState();
		check(secondClick.ariaExpanded === 'false' && isHidden(secondClick), 'The second desktop click must make the menu truly invisible.');

		await page.mouse.move(1200, 800);
		await trigger.evaluate((button) => button.blur());
		await trigger.hover();
		const freshHover = await readState();
		check(isVisible(freshHover), 'A fresh desktop mouseenter must reopen the menu after an explicit close.');

		await trigger.click();
		const clickBeforeEscape = await readState();
		check(clickBeforeEscape.ariaExpanded === 'true' && isVisible(clickBeforeEscape), 'A click must explicitly open the menu before Escape.');
		await trigger.press('Escape');
		const escape = await readState();
		check(escape.ariaExpanded === 'false' && isHidden(escape), 'Escape must make the focused desktop menu truly invisible.');

		await page.mouse.move(1200, 800);
		await trigger.evaluate((button) => button.blur());
		await trigger.focus();
		const freshFocus = await readState();
		check(isVisible(freshFocus), 'New keyboard focus must reopen the menu after Escape.');

		await page.setViewportSize({ width: 390, height: 844 });
		const mobileNavigation = page.locator('#mobile-navigation');
		await mobileNavigation.locator('summary').click();
		check(await mobileNavigation.evaluate((details) => details.open), 'Mobile navigation must retain its native open behavior.');
		await mobileNavigation.locator('summary').click();
		check(!(await mobileNavigation.evaluate((details) => details.open)), 'Mobile navigation must retain its native close behavior.');
		check(pageErrors.length === 0, `Dropdown runtime must not emit page errors: ${pageErrors.join('; ')}`);
		await context.close();
	} finally {
		await browser.close();
	}
}

try {
	await runDropdownInteractionRegression();
} catch (error) {
	failures.push(`Dropdown runtime harness error: ${error.stack || error}`);
}

if (failures.length > 0) {
	console.error('Navigation/grid regression: FAIL');
	for (const failure of failures) console.error(`- ${failure}`);
	process.exit(1);
}

console.log('Navigation/grid regression: PASS');
