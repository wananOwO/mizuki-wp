import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright';

const baseUrl = process.argv[2] || process.env.BASE_URL || 'http://127.0.0.1:8888';
const outDir = 'tools/visual-out';
const adminUser = process.env.MIZUKI_WP_ADMIN_USER || 'mizuki_admin';
const adminPassword = process.env.MIZUKI_WP_ADMIN_PASSWORD || 'Mizuki-Local-Test-2026!';
const scenarios = [
	{
		name: 'banner-list',
		viewport: { width: 1440, height: 900 },
		wallpaperMode: 'banner',
		layoutMode: 'list',
	},
	{
		name: 'fullscreen-list',
		viewport: { width: 1440, height: 900 },
		wallpaperMode: 'fullscreen',
		layoutMode: 'list',
	},
	{
		name: 'overlay-list',
		viewport: { width: 1440, height: 900 },
		wallpaperMode: 'overlay',
		layoutMode: 'list',
		overlay: { opacity: '0.8', blur: '1.5', cardOpacity: '0.8' },
	},
	{
		name: 'banner-grid',
		viewport: { width: 1440, height: 900 },
		wallpaperMode: 'banner',
		layoutMode: 'grid',
	},
	{
		name: 'mobile-seeded-desktop-state',
		viewport: { width: 390, height: 844 },
		wallpaperMode: 'overlay',
		layoutMode: 'grid',
		overlay: { opacity: '0.65', blur: '4.5', cardOpacity: '0.7' },
	},
];

function firstEnvironmentValue(env, names) {
	for (const name of names) {
		if (env[name]?.trim()) return env[name].trim();
	}
	return null;
}

export function resolveAuditPaths({ env = process.env, tempDir = null } = {}) {
	const resolvedTempDir = path.resolve(
		tempDir ||
			firstEnvironmentValue(env, ['MIZUKI_AUDIT_TEMP_DIR', 'TEMP', 'TMP']) ||
			os.tmpdir(),
	);
	let wordpressRoot = firstEnvironmentValue(env, [
		'MIZUKI_WP_ROOT',
		'MIZUKI_WORDPRESS_ROOT',
		'WORDPRESS_ROOT',
	]);

	if (wordpressRoot) {
		wordpressRoot = path.resolve(wordpressRoot);
		const nestedWordpress = path.join(wordpressRoot, 'wordpress');
		if (fs.existsSync(nestedWordpress) && fs.statSync(nestedWordpress).isDirectory()) {
			wordpressRoot = nestedWordpress;
		}
	} else {
		const candidates = fs.existsSync(resolvedTempDir)
			? fs.readdirSync(resolvedTempDir, { withFileTypes: true })
				.filter((entry) => entry.isDirectory() && entry.name.startsWith('mizuki-wp-native-wordpress-'))
				.map((entry) => {
					const container = path.join(resolvedTempDir, entry.name);
					return {
						container,
						wordpressRoot: path.join(container, 'wordpress'),
						mtimeMs: fs.statSync(container).mtimeMs,
					};
				})
				.filter((candidate) => fs.existsSync(candidate.wordpressRoot) && fs.statSync(candidate.wordpressRoot).isDirectory())
				.sort((left, right) => right.mtimeMs - left.mtimeMs)
			: [];
		wordpressRoot = candidates[0]?.wordpressRoot || null;
	}

	const wpDebugOverride = firstEnvironmentValue(env, [
		'MIZUKI_WP_DEBUG_LOG',
		'WP_DEBUG_LOG',
	]);
	if (!wordpressRoot && !wpDebugOverride) {
		throw new Error(
			`No mizuki-wp-native-wordpress-*\\wordpress root found under ${resolvedTempDir}; set MIZUKI_WP_ROOT or MIZUKI_WP_DEBUG_LOG.`,
		);
	}

	return {
		wordpressRoot,
		wpDebugLog: path.resolve(wpDebugOverride || path.join(wordpressRoot, 'wp-content', 'debug.log')),
		phpErrorLog: path.resolve(
			firstEnvironmentValue(env, ['MIZUKI_PHP_ERROR_LOG', 'PHP_ERROR_LOG']) ||
				path.join(resolvedTempDir, 'mizuki-wp-php-error.log'),
		),
	};
}

export function captureLogOffsets(logPaths) {
	return Object.fromEntries(
		Object.entries(logPaths).map(([name, logPath]) => {
			let offset = 0;
			try {
				offset = fs.statSync(logPath).size;
			} catch (error) {
				if (error.code !== 'ENOENT') throw error;
			}
			return [name, offset];
		}),
	);
}

export function readAppendedLog(logPath, offset) {
	let size;
	try {
		size = fs.statSync(logPath).size;
	} catch (error) {
		if (error.code === 'ENOENT') return '';
		throw error;
	}

	const safeOffset = Number.isSafeInteger(offset) && offset >= 0 ? offset : 0;
	const start = size < safeOffset ? 0 : safeOffset;
	const length = size - start;
	if (length === 0) return '';

	const handle = fs.openSync(logPath, 'r');
	try {
		const buffer = Buffer.alloc(length);
		let totalRead = 0;
		while (totalRead < length) {
			const bytesRead = fs.readSync(
				handle,
				buffer,
				totalRead,
				length - totalRead,
				start + totalRead,
			);
			if (bytesRead === 0) break;
			totalRead += bytesRead;
		}
		return buffer.subarray(0, totalRead).toString('utf8');
	} finally {
		fs.closeSync(handle);
	}
}

const forbiddenDiagnosticPattern = /\bPHP\s+(?:(?:(?:Core|Compile|User)\s+)?(?:Warning|Notice|Deprecated|Error)|(?:(?:Recoverable|Catchable)\s+)?Fatal error|Parse error|Startup)\s*:|\bWordPress database error\b/i;

export function findForbiddenDiagnostics(logText) {
	return logText
		.split(/\r?\n/)
		.map((line) => line.trim())
		.filter((line) => line && forbiddenDiagnosticPattern.test(line));
}

function normalizeSameOriginUrls(urls, base) {
	const normalized = [];
	const seen = new Set();
	for (const value of urls) {
		try {
			const candidate = new URL(value, base);
			if (candidate.origin !== base.origin || !['http:', 'https:'].includes(candidate.protocol)) continue;
			candidate.hash = '';
			if (seen.has(candidate.href)) continue;
			seen.add(candidate.href);
			normalized.push(candidate.href);
		} catch {}
	}
	return normalized;
}

export function buildRouteAuditTargets({
	baseUrl: requestedBaseUrl,
	postUrls,
	categoryUrls,
	searchQuery = 'mizuki-audit',
}) {
	const home = new URL(requestedBaseUrl);
	home.hash = '';
	const posts = normalizeSameOriginUrls(postUrls, home);
	const categories = normalizeSameOriginUrls(categoryUrls, home);
	if (posts.length < 3) {
		throw new Error(`Homepage exposed ${posts.length} unique same-origin post URLs; expected at least 3 post URLs.`);
	}
	if (categories.length < 2) {
		throw new Error(`Homepage exposed ${categories.length} unique same-origin category URLs; expected at least 2 category URLs.`);
	}

	const search = new URL(home);
	search.search = '';
	search.searchParams.set('s', searchQuery);
	return [
		{ kind: 'home', url: home.href },
		{ kind: 'search', url: search.href },
		...posts.slice(0, 3).map((url) => ({ kind: 'post', url })),
		...categories.slice(0, 2).map((url) => ({ kind: 'category', url })),
	];
}

export function validateRouteResponse({ url, status, body }) {
	const errors = [];
	if (status !== 200) errors.push(`${url} returned HTTP ${status}`);
	if (!body.includes('wp-theme-mizuki-wp')) {
		errors.push(`${url} response does not contain wp-theme-mizuki-wp`);
	}
	return errors;
}

async function runAudit() {
	const auditPaths = resolveAuditPaths();
	const logPaths = {
		wpDebugLog: auditPaths.wpDebugLog,
		phpErrorLog: auditPaths.phpErrorLog,
	};
	const logOffsets = captureLogOffsets(logPaths);
	console.log(JSON.stringify({ scenario: 'audit-start', baseUrl, logPaths, logOffsets }, null, 2));

fs.mkdirSync(outDir, { recursive: true });

const browser = await chromium.launch({ headless: true });
const failures = [];

for (const scenario of scenarios) {
	const context = await browser.newContext({
		viewport: scenario.viewport,
		deviceScaleFactor: 1,
	});
	await context.addInitScript(
		({ wallpaperMode, layoutMode, overlay }) => {
			if (sessionStorage.getItem('mizukiAuditSeeded') === '1') return;
			localStorage.clear();
			localStorage.setItem('wallpaperMode', wallpaperMode);
			localStorage.setItem('postListLayout', layoutMode);
			if (overlay) {
				localStorage.setItem('overlayOpacity', overlay.opacity);
				localStorage.setItem('overlayBlur', overlay.blur);
				localStorage.setItem('overlayCardOpacity', overlay.cardOpacity);
			}
			sessionStorage.setItem('mizukiAuditSeeded', '1');
		},
		{
			wallpaperMode: scenario.wallpaperMode,
			layoutMode: scenario.layoutMode,
			overlay: scenario.overlay || null,
		},
	);

	const page = await context.newPage();
	const consoleErrors = [];
	const fontResponses = [];
	page.on('console', (message) => {
		if (message.type() === 'error') consoleErrors.push(message.text());
	});
	page.on('pageerror', (error) => consoleErrors.push(error.message));
	page.on('response', (response) => {
		try {
			if (new URL(response.url()).pathname.toLowerCase().endsWith('.ttf')) {
				fontResponses.push({ url: response.url(), status: response.status() });
			}
		} catch {}
	});

	await page.goto(baseUrl, { waitUntil: 'load', timeout: 45_000 });
	await page.waitForTimeout(1_200);
	if (scenario.name === 'banner-list') {
		await page.evaluate(async () => {
			await Promise.all([
				document.fonts.load('500 16px "Zen Maru Gothic"', '中文字体测试'),
				document.fonts.load('400 16px "Loli"', '中文字体测试'),
			]);
		});
		await page.waitForTimeout(250);
	}

	const metrics = await page.evaluate(() => {
		const query = (selector) => document.querySelector(selector);
		const style = (element) => (element ? getComputedStyle(element) : null);
		const rect = (element) => element?.getBoundingClientRect().toJSON() || null;
		const main = query('.absolute.w-full.z-30.pointer-events-none');
		const banner = query('#banner-wrapper');
		const wallpaper = query('[data-fullscreen-wallpaper]');
		const grid = query('#main-grid');
		const postList = query('#post-list-container');
		const firstCard = query('.post-card-item');
		const navbarSurface = query('#navbar');
		const bannerText = query('.banner-text-overlay');
		const cardText = firstCard?.querySelector(':scope > div');
		const cardCover = firstCard?.querySelector(':scope > a[class*="md:absolute"]');
		const cardTitle = firstCard?.querySelector('.post-card-title');
		const cardMetaLinks = Array.from(firstCard?.querySelectorAll('a.link-lg') || []);
		const rightWrapper = query('.right-sidebar-container');
		const rightSidebar = query('#right-sidebar');
		const rootStyle = getComputedStyle(document.documentElement);
		const desktopSections = Array.from(
			document.querySelectorAll('.desktop-setting-section'),
		).map((element) => ({
			display: style(element).display,
			visibility: style(element).visibility,
			text: element.textContent.trim().replace(/\s+/g, ' ').slice(0, 100),
		}));

		return {
			bodyClass: document.body.className,
			bodyFontFamily: style(document.body).fontFamily,
			fontStatus: {
				zen: document.fonts.check('500 16px "Zen Maru Gothic"', '中文字体测试'),
				loli: document.fonts.check('400 16px "Loli"', '中文字体测试'),
			},
			main: main
				? { position: style(main).position, top: style(main).top, rect: rect(main) }
				: null,
			banner: banner
				? {
						position: style(banner).position,
						top: style(banner).top,
						height: style(banner).height,
						display: style(banner).display,
						rect: rect(banner),
					}
				: null,
			wallpaper: wallpaper
				? {
						position: style(wallpaper).position,
						display: style(wallpaper).display,
						opacity: style(wallpaper).opacity,
						filter: style(wallpaper).filter,
						rect: rect(wallpaper),
						wallpaperOpacity: wallpaper.style.getPropertyValue('--wallpaper-opacity'),
						wallpaperBlur: wallpaper.style.getPropertyValue('--wallpaper-blur'),
					}
				: null,
			cardTransparentOpacity: rootStyle.getPropertyValue('--card-transparent-opacity').trim(),
			storedDisplayState: {
				wallpaperMode: localStorage.getItem('wallpaperMode'),
				layoutMode: localStorage.getItem('postListLayout'),
				overlayOpacity: localStorage.getItem('overlayOpacity'),
				overlayBlur: localStorage.getItem('overlayBlur'),
				overlayCardOpacity: localStorage.getItem('overlayCardOpacity'),
			},
			grid: grid
				? {
						mode: grid.dataset.layoutMode,
						columns: style(grid).gridTemplateColumns,
						rect: rect(grid),
					}
				: null,
			postList: postList
				? {
						className: postList.className,
						display: style(postList).display,
						columns: style(postList).gridTemplateColumns,
					}
				: null,
			firstCard: rect(firstCard),
			navbarSurface: rect(navbarSurface),
			bannerTextDisplay: bannerText ? style(bannerText).display : null,
			gridCard: {
				text: rect(cardText),
				cover: rect(cardCover),
				coverDisplay: cardCover ? style(cardCover).display : null,
				coverFontSize: cardCover ? Number.parseFloat(style(cardCover).fontSize) : null,
				coverLineClamp: cardCover ? style(cardCover).webkitLineClamp : null,
				titleFontSize: cardTitle ? Number.parseFloat(style(cardTitle).fontSize) : null,
				metaFontSizes: cardMetaLinks.map((element) => Number.parseFloat(style(element).fontSize)),
			},
			accentDeltas: Array.from(document.querySelectorAll('.post-card-title')).map((title) => {
				const titleRect = title.getBoundingClientRect();
				const pseudo = getComputedStyle(title, '::before');
				const transform = pseudo.transform === 'none' ? new DOMMatrixReadOnly() : new DOMMatrixReadOnly(pseudo.transform);
				const pseudoTop = titleRect.top + Number.parseFloat(pseudo.top || '0') + transform.m42;
				const pseudoCenter = pseudoTop + Number.parseFloat(pseudo.height || '0') / 2;
				return Math.abs(titleRect.top + titleRect.height / 2 - pseudoCenter);
			}),
			rightWrapper: rightWrapper
				? {
						className: rightWrapper.className,
						display: style(rightWrapper).display,
					}
				: null,
			rightSidebarDisplay: rightSidebar ? style(rightSidebar).display : null,
			desktopSections,
			wordCountTexts: Array.from(
				document.querySelectorAll('.post-card-item .text-50.text-sm.font-medium'),
			)
				.map((element) => element.textContent.trim())
				.filter((text) => /word|字/i.test(text)),
		};
	});

	const settingsButton = page.locator('#display-settings-switch');
	const initialSettingsAria = await settingsButton.getAttribute('aria-expanded');
	if (await settingsButton.count()) {
		await settingsButton.click();
		await page.waitForTimeout(250);
	}
	const panelMetrics = await page.evaluate(() => {
		const panel = document.querySelector('#display-setting');
		const hue = document.querySelector('#panel-hue-slider');
		const rects = (selector) => Array.from(document.querySelectorAll(selector)).map((element) => element.getBoundingClientRect().toJSON());
		return {
			panel: panel?.getBoundingClientRect().toJSON() || null,
			rootFontSize: Number.parseFloat(getComputedStyle(document.documentElement).fontSize),
			ariaExpanded: document.querySelector('#display-settings-switch')?.getAttribute('aria-expanded'),
			hue: hue?.getBoundingClientRect().toJSON() || null,
			wallpaperOptions: rects('.wallpaper-mode-btn'),
			layoutOptions: rects('.layout-mode-btn'),
			overlaySectionDisplay: getComputedStyle(document.querySelector('#overlay-settings-section')).display,
			overlayRanges: rects('#overlay-settings-section input[type="range"]'),
		};
	});
	await page.screenshot({
		path: `${outDir}/local-${scenario.name}.png`,
		fullPage: false,
	});

	const checks = [];
	const check = (condition, message) => checks.push({ condition, message });
	let routeAudit = null;

	check(consoleErrors.length === 0, 'page has no JavaScript console/page errors');
	check(metrics.firstCard !== null, 'homepage renders at least one post card');
	check(metrics.wordCountTexts.length > 0, 'post cards render word-count metadata');
	check(
		Math.abs((panelMetrics.panel?.width || 0) - panelMetrics.rootFontSize * 20) < 1,
		'settings panel computes to the upstream 20rem width',
	);
	check(
		Math.abs((panelMetrics.hue?.height || 0) - panelMetrics.rootFontSize * 1.5) < 1,
		'hue control uses the upstream 1.5rem color bar',
	);
	check(initialSettingsAria === 'false' && panelMetrics.ariaExpanded === 'true', 'settings button synchronizes aria-expanded when opened');

	if (scenario.name === 'banner-list') {
		try {
			const homepageLinks = await page.evaluate(() => ({
				postUrls: Array.from(
					document.querySelectorAll('.post-card-item .post-card-title[href]'),
					(link) => link.href,
				),
				categoryUrls: Array.from(
					document.querySelectorAll('#category-bar .category-scroll a[href]'),
					(link) => link.href,
				),
			}));
			const targets = buildRouteAuditTargets({ baseUrl, ...homepageLinks });
			const responses = [];
			for (const target of targets) {
				try {
					const response = await context.request.get(target.url, {
						failOnStatusCode: false,
						timeout: 45_000,
					});
					const body = await response.text();
					const responseErrors = validateRouteResponse({
						url: target.url,
						status: response.status(),
						body,
					});
					responses.push({
						...target,
						status: response.status(),
						themeMarker: body.includes('wp-theme-mizuki-wp'),
					});
					if (responseErrors.length === 0) {
						check(true, `${target.kind} route returns HTTP 200 with the Mizuki theme marker: ${target.url}`);
					} else {
						for (const error of responseErrors) check(false, `route health: ${error}`);
					}
				} catch (error) {
					responses.push({ ...target, requestError: error.message });
					check(false, `route health: ${target.url} request failed: ${error.message}`);
				}
			}
			routeAudit = { homepageLinks, targets, responses };
		} catch (error) {
			routeAudit = { discoveryError: error.message };
			check(false, `route discovery: ${error.message}`);
		}

		check(
			panelMetrics.wallpaperOptions.length === 4 &&
			panelMetrics.wallpaperOptions.every((option, index, options) => index === 0 || option.top > options[index - 1].top),
			'wallpaper controls render as vertical rows',
		);
		check(
			panelMetrics.layoutOptions.length === 2 &&
			panelMetrics.layoutOptions[0].top === panelMetrics.layoutOptions[1].top &&
			panelMetrics.layoutOptions[1].left > panelMetrics.layoutOptions[0].left,
			'layout controls render as two columns',
		);
		check(
			metrics.bodyFontFamily.startsWith('"Zen Maru Gothic", Loli') && metrics.fontStatus.zen && metrics.fontStatus.loli,
			'body uses the upstream Zen/Loli font stack and both faces are loadable',
		);
		check(
			fontResponses.some(({ url, status }) => new URL(url).pathname.endsWith('/ZenMaruGothic-Medium.ttf') && status === 200) &&
			fontResponses.some(({ url, status }) => new URL(url).pathname.endsWith('/loli.ttf') && status === 200),
			'browser requests both packaged font files successfully',
		);

		await page.keyboard.press('Escape');
		check(await settingsButton.getAttribute('aria-expanded') === 'false', 'Escape resets settings aria-expanded');
		const dropdowns = page.locator('#navbar-links-container [data-dropdown]');
		const requiredDropdowns = new Set(['我的', '关于', '更多']);
		let closeInteractionsChecked = false;
		for (let index = 0; index < await dropdowns.count(); index++) {
			const dropdown = dropdowns.nth(index);
			const trigger = dropdown.locator('[data-dropdown-trigger]');
			const menu = dropdown.locator('[data-dropdown-menu]');
			if (!await trigger.count()) continue;
			const title = (await trigger.locator('span').first().textContent() || '').trim();
			if (!requiredDropdowns.has(title)) continue;
			await dropdown.hover();
			await page.waitForTimeout(300);
			const hoverState = await menu.evaluate((menuElement) => {
				const computed = getComputedStyle(menuElement);
				const child = menuElement.querySelector('a');
				return {
					opacity: Number(computed.opacity),
					visibility: computed.visibility,
					pointerEvents: computed.pointerEvents,
					childVisible: Boolean(child && child.getBoundingClientRect().width > 0),
				};
			});
			check(
				hoverState.opacity > 0.99 && hoverState.visibility === 'visible' && hoverState.pointerEvents === 'auto' && hoverState.childVisible,
				`${title} dropdown opens with visible children on hover`,
			);
			await trigger.focus();
			const focusVisibility = await menu.evaluate((menuElement) => getComputedStyle(menuElement).visibility);
			check(focusVisibility === 'visible', `${title} dropdown remains keyboard focus accessible`);

			if (!closeInteractionsChecked) {
				const readMenuState = () => menu.evaluate((menuElement) => {
					const computed = getComputedStyle(menuElement);
					return {
						opacity: Number(computed.opacity),
						visibility: computed.visibility,
						pointerEvents: computed.pointerEvents,
					};
				});
				const isHidden = (state) => state.opacity < 0.01
					&& state.visibility === 'hidden'
					&& state.pointerEvents === 'none';

				await page.evaluate(() => document.activeElement?.blur());
				await page.mouse.move(0, 899);
				await trigger.click();
				check(await trigger.getAttribute('aria-expanded') === 'true', `${title} first click opens the dropdown`);
				await trigger.click();
				await page.waitForTimeout(300);
				check(
					await trigger.getAttribute('aria-expanded') === 'false' && isHidden(await readMenuState()),
					`${title} second click makes the dropdown truly invisible`,
				);
				await trigger.click();
				await page.keyboard.press('Escape');
				await page.waitForTimeout(300);
				check(
					await trigger.getAttribute('aria-expanded') === 'false' && isHidden(await readMenuState()),
					`${title} Escape makes the dropdown truly invisible`,
				);
				await page.mouse.move(0, 899);
				await dropdown.hover();
				await page.waitForTimeout(300);
				check(!isHidden(await readMenuState()), `${title} reopens on a fresh mouseenter after closing`);
				closeInteractionsChecked = true;
			}
			requiredDropdowns.delete(title);
			await page.evaluate(() => document.activeElement?.blur());
			await page.mouse.move(0, 899);
		}
		check(requiredDropdowns.size === 0, 'all requested desktop dropdown groups were exercised');
		check(closeInteractionsChecked, 'desktop dropdown click and Escape close interactions were exercised');

		await settingsButton.click();

		await page.locator('.wallpaper-mode-btn[data-mode="fullscreen"]').click();
		await page.locator('.layout-mode-btn[data-mode="grid"]').click();
		const switched = await page.evaluate(() => ({
			wallpaperMode: localStorage.getItem('wallpaperMode'),
			layoutMode: localStorage.getItem('postListLayout'),
			fullscreen: document.body.classList.contains('fullscreen-banner'),
			grid: document.querySelector('#main-grid')?.dataset.layoutMode,
			rightDisplay: getComputedStyle(document.querySelector('.right-sidebar-container')).display,
		}));
		check(switched.wallpaperMode === 'fullscreen' && switched.fullscreen, 'real WordPress wallpaper control switches to fullscreen');
		check(switched.layoutMode === 'grid' && switched.grid === 'grid', 'real WordPress layout control switches to grid');
		check(switched.rightDisplay === 'none', 'real WordPress grid interaction hides the right sidebar');

		await page.locator('#reset-wallpaper-mode').click();
		await page.locator('#reset-layout-mode').click();
		const reset = await page.evaluate(() => ({
			wallpaperMode: localStorage.getItem('wallpaperMode'),
			layoutMode: localStorage.getItem('postListLayout'),
			bodyClass: document.body.className,
			grid: document.querySelector('#main-grid')?.dataset.layoutMode,
		}));
		check(reset.wallpaperMode === null && reset.bodyClass.includes('enable-banner') && !reset.bodyClass.includes('fullscreen-banner'), 'real WordPress wallpaper reset restores banner default');
		check(reset.layoutMode === null && reset.grid === 'list', 'real WordPress layout reset restores list default');
	}

	if (scenario.name === 'overlay-list') {
		check(metrics.bodyClass.includes('overlay-wallpaper'), 'overlay body state is active');
		check(metrics.bodyClass.includes('wallpaper-transparent'), 'overlay enables transparent cards');
		check(metrics.bodyClass.includes('no-banner-mode'), 'overlay uses no-banner content geometry');
		check(metrics.banner?.display === 'none', 'overlay hides the ordinary Banner');
		check(metrics.wallpaper?.display === 'block' && metrics.wallpaper?.position === 'fixed', 'overlay shows the fixed wallpaper layer');
		check(
			Math.abs((metrics.wallpaper?.rect?.width || 0) - scenario.viewport.width) < 20 &&
			Math.abs((metrics.wallpaper?.rect?.height || 0) - scenario.viewport.height) < 1,
			'overlay wallpaper covers the viewport',
		);
		check(
			metrics.wallpaper?.wallpaperOpacity === '0.8' &&
			metrics.wallpaper?.wallpaperBlur === '1.5px' &&
			metrics.cardTransparentOpacity === '0.8',
			'overlay applies upstream default effect values',
		);
		check(panelMetrics.overlaySectionDisplay === 'block' && panelMetrics.overlayRanges.length === 3, 'overlay reveals all three effect controls');
		check(metrics.main?.rect?.top >= (metrics.navbarSurface?.bottom || 0) + 8, 'overlay content clears the navbar');

		await page.locator('#overlay-opacity-slider').evaluate((input) => {
			input.value = '65';
			input.dispatchEvent(new Event('input', { bubbles: true }));
		});
		await page.locator('#overlay-blur-slider').evaluate((input) => {
			input.value = '4.5';
			input.dispatchEvent(new Event('input', { bubbles: true }));
		});
		await page.locator('#overlay-card-opacity-slider').evaluate((input) => {
			input.value = '70';
			input.dispatchEvent(new Event('input', { bubbles: true }));
		});
		await page.reload({ waitUntil: 'load' });
		const persistedOverlay = await page.evaluate(() => {
			const wallpaper = document.querySelector('[data-fullscreen-wallpaper]');
			return {
				opacity: localStorage.getItem('overlayOpacity'),
				blur: localStorage.getItem('overlayBlur'),
				cardOpacity: localStorage.getItem('overlayCardOpacity'),
				wallpaperOpacity: wallpaper.style.getPropertyValue('--wallpaper-opacity'),
				wallpaperBlur: wallpaper.style.getPropertyValue('--wallpaper-blur'),
				rootCardOpacity: document.documentElement.style.getPropertyValue('--card-transparent-opacity'),
			};
		});
		check(
			persistedOverlay.opacity === '0.65' && persistedOverlay.blur === '4.5' && persistedOverlay.cardOpacity === '0.7' &&
			persistedOverlay.wallpaperOpacity === '0.65' && persistedOverlay.wallpaperBlur === '4.5px' && persistedOverlay.rootCardOpacity === '0.7',
			'overlay effects persist and reapply after reload',
		);
		await page.locator('#display-settings-switch').click();
		await page.locator('#reset-overlay-settings').click();
		const resetOverlay = await page.evaluate(() => ({
			opacity: localStorage.getItem('overlayOpacity'),
			blur: localStorage.getItem('overlayBlur'),
			cardOpacity: localStorage.getItem('overlayCardOpacity'),
		}));
		check(
			resetOverlay.opacity === '0.8' && resetOverlay.blur === '1.5' && resetOverlay.cardOpacity === '0.8',
			'overlay reset restores and persists upstream defaults',
		);
	}

	if (scenario.name === 'fullscreen-list') {
		check(
			metrics.bodyClass.includes('fullscreen-banner'),
			'fullscreen body state is active',
		);
		check(metrics.banner?.position === 'fixed', 'fullscreen banner is fixed');
		check(metrics.bannerTextDisplay === 'none', 'fullscreen hides the banner title layer');
		check(
			metrics.grid?.rect?.top >= (metrics.navbarSurface?.bottom || 0) + 8,
			'fullscreen content clears the navbar surface',
		);
		check(
			metrics.firstCard?.top < scenario.viewport.height,
			'fullscreen first post card begins inside the first viewport',
		);
	}

	if (scenario.name === 'banner-grid') {
		check(metrics.grid?.mode === 'grid', 'desktop grid state is active');
		check(
			metrics.rightWrapper?.display === 'none',
			'desktop grid hides the right sidebar wrapper',
		);
		check(
			metrics.grid?.columns.split(' ').filter(Boolean).length === 2,
			'desktop grid has two main tracks',
		);
		check(metrics.postList?.display === 'grid', 'post list uses CSS grid');
		check(
			metrics.gridCard.cover?.top < metrics.gridCard.text?.top,
			'grid card cover renders above its text content',
		);
		check(
			metrics.gridCard.coverLineClamp === 'none' &&
				metrics.gridCard.coverFontSize !== metrics.gridCard.titleFontSize,
			'grid cover does not inherit title typography or line clamping',
		);
		check(
			metrics.gridCard.metaFontSizes.every((size) => size < metrics.gridCard.titleFontSize),
			'grid metadata links keep a smaller font than the title',
		);
		check(
			metrics.accentDeltas.length > 0 && metrics.accentDeltas.every((delta) => delta <= 1),
			'grid title accents are centered within one CSS pixel',
		);
	}

	if (scenario.name === 'mobile-seeded-desktop-state') {
		check(metrics.grid?.mode === 'list', 'mobile forces list layout');
		check(
			metrics.bodyClass.includes('enable-banner') &&
				!metrics.bodyClass.includes('fullscreen-banner') &&
				!metrics.bodyClass.includes('overlay-wallpaper') &&
				!metrics.bodyClass.includes('wallpaper-transparent') &&
				!metrics.bodyClass.includes('no-banner-mode'),
			'mobile forces banner wallpaper mode',
		);
		check(metrics.desktopSections.length >= 2, 'desktop-only settings are rendered');
		check(
			metrics.desktopSections.every((section) => section.display === 'none'),
			'mobile hides wallpaper and layout settings',
		);
		check(metrics.wallpaper?.display === 'none', 'mobile hides the seeded overlay wallpaper');
		check(
			metrics.storedDisplayState.wallpaperMode === 'overlay' &&
			metrics.storedDisplayState.layoutMode === 'grid' &&
			metrics.storedDisplayState.overlayOpacity === '0.65' &&
			metrics.storedDisplayState.overlayBlur === '4.5' &&
			metrics.storedDisplayState.overlayCardOpacity === '0.7',
			'mobile preserves desktop overlay and layout preferences',
		);
	}

	for (const result of checks) {
		if (!result.condition) failures.push(`${scenario.name}: ${result.message}`);
	}

	console.log(
		JSON.stringify(
			{
				scenario: scenario.name,
				metrics,
				panelMetrics,
				consoleErrors,
				routeAudit,
				checks: checks.map(({ condition, message }) => ({ pass: condition, message })),
			},
			null,
			2,
		),
	);

	await context.close();
}

async function auditAnnouncementSetting() {
	const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
	const page = await context.newPage();
	const checks = [];
	const check = (condition, message) => checks.push({ condition, message });
	const adminUrl = `${baseUrl}/wp-admin/admin.php?page=mizuki-theme-settings`;
	let originalAnnouncement = null;
	const openAnnouncementField = async () => {
		const field = page.locator('#mizuki_announcement');
		await field.waitFor({ state: 'attached' });
		if (!await field.isVisible()) {
			await field.locator('xpath=ancestor::div[contains(@class,"mizuki-accordion-item")]').locator('.mizuki-accordion-header').click();
		}
		await field.waitFor({ state: 'visible' });
		return field;
	};

	try {
		await page.goto(`${baseUrl}/wp-login.php`, { waitUntil: 'domcontentloaded', timeout: 45_000 });
		await page.locator('#user_login').fill(adminUser);
		await page.locator('#user_pass').fill(adminPassword);
		await Promise.all([
			page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
			page.locator('#wp-submit').click(),
		]);
		await page.goto(adminUrl, { waitUntil: 'domcontentloaded', timeout: 45_000 });
		const field = await openAnnouncementField();
		originalAnnouncement = await field.inputValue();

		const marker = `Mizuki 公告浏览器审计 ${Date.now()}`;
		const submitted = `${marker} <strong>不可执行</strong>\n第二行 & 保留`;
		const expected = `${marker} 不可执行\n第二行 & 保留`;
		await field.fill(submitted);
		await Promise.all([
			page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
			page.locator('#submit').click(),
		]);
		check(await page.locator('#mizuki_announcement').inputValue() === expected, 'dashboard saves and sanitizes the website announcement');

		await page.goto(baseUrl, { waitUntil: 'load', timeout: 45_000 });
		const frontend = await page.evaluate((textMarker) => {
			const element = Array.from(document.querySelectorAll('div')).find(
				(candidate) => candidate.childElementCount === 0 && candidate.textContent.includes(textMarker),
			);
			return element ? { text: element.textContent, html: element.innerHTML } : null;
		}, marker);
		check(frontend?.text === expected, 'front end renders the saved Unicode announcement exactly');
		check(frontend !== null && !frontend.html.includes('<strong>'), 'front end does not execute announcement HTML');
	} catch (error) {
		checks.push({ condition: false, message: `announcement workflow completed without exception: ${error.message}` });
	} finally {
		if (originalAnnouncement !== null) {
			try {
				await page.goto(adminUrl, { waitUntil: 'domcontentloaded', timeout: 45_000 });
				const field = page.locator('#mizuki_announcement');
				await field.waitFor({ state: 'attached' });
				await field.evaluate((element, value) => {
					element.value = value;
					element.dispatchEvent(new Event('input', { bubbles: true }));
				}, originalAnnouncement);
				await Promise.all([
					page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
					page.locator('#submit').evaluate((element) => element.click()),
				]);
				check(await page.locator('#mizuki_announcement').inputValue() === originalAnnouncement, 'announcement audit restores the original value');
			} catch (error) {
				checks.push({ condition: false, message: `announcement restore succeeded: ${error.message}` });
			}
		}
		await context.close();
	}

	for (const result of checks) {
		if (!result.condition) failures.push(`announcement-admin: ${result.message}`);
	}
	console.log(JSON.stringify({ scenario: 'announcement-admin', checks: checks.map(({ condition, message }) => ({ pass: condition, message })) }, null, 2));
}

await auditAnnouncementSetting();

await browser.close();

const logAudit = Object.entries(logPaths).map(([name, logPath]) => {
	const appended = readAppendedLog(logPath, logOffsets[name]);
	const diagnostics = findForbiddenDiagnostics(appended);
	for (const diagnostic of diagnostics) {
		failures.push(`${name}: ${diagnostic}`);
	}
	return {
		name,
		path: logPath,
		startOffset: logOffsets[name],
		appendedBytes: Buffer.byteLength(appended),
		diagnostics,
	};
});
console.log(JSON.stringify({ scenario: 'appended-server-logs', logs: logAudit }, null, 2));

if (failures.length) {
	console.error(`\n${failures.length} browser assertion(s) failed:`);
	for (const failure of failures) console.error(`- ${failure}`);
	process.exit(1);
}

console.log('\nAll local WordPress browser assertions passed.');
}

const isDirectRun = Boolean(
	process.argv[1] && path.resolve(process.argv[1]) === fileURLToPath(import.meta.url),
);
if (isDirectRun) await runAudit();
