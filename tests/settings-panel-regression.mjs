import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { JSDOM } from 'jsdom';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const navbarSource = fs.readFileSync(
	path.join(root, 'theme/mizuki-wp/inc/parts/navbar.php'),
	'utf8',
);
const searchInputs = navbarSource.match(/<input type="search"[^>]*value="<\?php echo get_search_query\(\); \?>"/g) || [];
if (searchInputs.length !== 2) throw new Error('Both navbar search fields must preserve the escaped query.');

const scriptSource = fs.readFileSync(
	path.join(root, 'theme/mizuki-wp/assets/js/mizuki-theme.js'),
	'utf8',
);
const bannerSource = fs.readFileSync(
	path.join(root, 'theme/mizuki-wp/inc/parts/banner.php'),
	'utf8',
);
const headerSource = fs.readFileSync(
	path.join(root, 'theme/mizuki-wp/header.php'),
	'utf8',
);
const carouselSource = fs.readFileSync(
	path.join(root, 'theme/mizuki-wp/assets/js/banner-carousel.js'),
	'utf8',
);
const enqueueSource = fs.readFileSync(
	path.join(root, 'theme/mizuki-wp/inc/enqueue.php'),
	'utf8',
);
const cssPath = path.join(
	root,
	'theme/mizuki-wp/assets/css/mizuki-settings-panel.css',
);
const cssSource = fs.existsSync(cssPath) ? fs.readFileSync(cssPath, 'utf8') : '';
const layoutCssSource = fs.readFileSync(
	path.join(root, 'theme/mizuki-wp/assets/css/mizuki-layout-modes.css'),
	'utf8',
);

const failures = [];

function check(condition, message) {
	if (!condition) failures.push(message);
}

function count(source, token) {
	return source.split(token).length - 1;
}

check(count(navbarSource, 'id="display-setting"') === 1, 'Render one unified settings panel.');
check(navbarSource.includes('w-80'), 'Use the upstream 320px panel width.');
check(count(navbarSource, 'settings-section') >= 3, 'Render grouped theme, wallpaper, and layout sections.');
check(count(navbarSource, 'desktop-setting-section') >= 2, 'Gate wallpaper and layout as desktop-only sections.');
check(navbarSource.includes('id="reset-hue"'), 'Theme color needs a reset control.');
check(navbarSource.includes('id="reset-wallpaper-mode"'), 'Wallpaper needs a reset control.');
check(navbarSource.includes('id="reset-layout-mode"'), 'Layout needs a reset control.');
check(navbarSource.includes('settings-section-accent'), 'Section headings need an accent treatment.');
check(navbarSource.includes('settings-option-icon'), 'Settings choices need icons.');
check(navbarSource.includes('settings-option-check'), 'Settings choices need a selected-state icon.');
check(navbarSource.includes('wallpaper-mode-btn'), 'Use wallpaper-mode controls.');
check(navbarSource.includes('data-mode="overlay"'), 'Wallpaper controls need the upstream overlay state.');
check(navbarSource.includes('data-mode="none"'), 'Wallpaper controls need an explicit none state.');
check(navbarSource.includes('settings-wallpaper-options'), 'Wallpaper choices need their upstream vertical-row wrapper.');
check(navbarSource.includes('settings-layout-options'), 'Layout choices need their upstream two-column wrapper.');
check(/id="panel-hue-slider"[^>]*step="5"/s.test(navbarSource), 'Hue selection must use the upstream five-degree step.');
check(/id="overlay-opacity-slider"[^>]*min="20"[^>]*max="100"[^>]*step="1"/s.test(navbarSource), 'Overlay opacity must use the upstream 20-100 range.');
check(/id="overlay-blur-slider"[^>]*min="0"[^>]*max="12"[^>]*step="0\.5"/s.test(navbarSource), 'Overlay blur must use the upstream 0-12px range.');
check(/id="overlay-card-opacity-slider"[^>]*min="20"[^>]*max="100"[^>]*step="1"/s.test(navbarSource), 'Overlay card opacity must use the upstream 20-100 range.');
check(navbarSource.includes('id="reset-overlay-settings"'), 'Overlay effects need one reset control.');
check(navbarSource.includes('id="overlay-settings-section"'), 'Overlay effects need a conditional settings section.');
check(bannerSource.includes('function mizuki_render_fullscreen_wallpaper'), 'Render overlay wallpaper independently from Banner.');
check(bannerSource.includes('data-fullscreen-wallpaper'), 'Dedicated wallpaper needs the upstream data hook.');
check(count(bannerSource, 'data-mizuki-carousel') >= 2, 'Banner and overlay wallpaper must share the carousel contract.');
check(carouselSource.includes("querySelectorAll('[data-mizuki-carousel]')"), 'Carousel runtime must initialize both wallpaper renderers.');
check(headerSource.includes('mizuki_render_fullscreen_wallpaper();'), 'Place the dedicated wallpaper beside Banner in the grid layout.');

check(fs.existsSync(cssPath), 'Enqueue a dedicated settings-panel stylesheet.');
check(/#display-setting\s*\{[^}]*width:\s*20rem/s.test(cssSource), 'CSS must keep the panel at 20rem.');
check(/@media\s*\(max-width:\s*1279px\)/.test(cssSource), 'CSS needs the <1280 mobile gate.');
check(/\.desktop-setting-section\s*\{[^}]*display:\s*none/s.test(cssSource), 'Mobile must hide desktop settings sections.');
check(/\.settings-desktop-only\s*\{[^}]*display:\s*none/s.test(cssSource), 'Mobile must hide an empty desktop-only settings entry.');
check(/\.settings-option(?:\.is-selected|\[data-selected=['"]true['"]\])/.test(cssSource), 'Selected options need explicit styling.');
check(/\.settings-wallpaper-options\s*\{[^}]*flex-direction:\s*column/s.test(cssSource), 'Wallpaper choices must be full-width vertical rows.');
check(/\.settings-layout-options\s*\{[^}]*grid-template-columns:\s*repeat\(2,/s.test(cssSource), 'Layout choices must use two columns.');
check(cssSource.includes('var(--color-selection-bar)'), 'Hue selection must use the upstream color bar.');
check(/\.overlay-range-control\s+input\[type=['"]range['"]\]/.test(cssSource), 'Overlay controls need the upstream progress-track styling.');
check(!cssSource.includes('var(--text-50)'), 'Settings styles must only use defined theme variables.');
check(enqueueSource.includes('mizuki-settings-panel.css'), 'The settings stylesheet must be enqueued.');
check(/\[data-fullscreen-wallpaper\]\s*\{[^}]*position:\s*fixed[^}]*inset:\s*0/s.test(layoutCssSource), 'Overlay wallpaper must fill the fixed viewport.');
check(/body\.overlay-wallpaper\s+\[data-fullscreen-wallpaper\]\s*\{[^}]*display:\s*block/s.test(layoutCssSource), 'Overlay state must reveal only the dedicated wallpaper.');

check(scriptSource.includes("'wallpaperMode'"), 'Persist wallpaperMode.');
check(scriptSource.includes("'postListLayout'"), 'Persist postListLayout.');
check(scriptSource.includes("'overlayOpacity'"), 'Persist overlayOpacity.');
check(scriptSource.includes("'overlayBlur'"), 'Persist overlayBlur.');
check(scriptSource.includes("'overlayCardOpacity'"), 'Persist overlayCardOpacity.');
check(scriptSource.includes("'bannerMode'"), 'Migrate bannerMode.');
check(scriptSource.includes("'layoutMode'"), 'Migrate layoutMode.');
check(scriptSource.includes("matchMedia('(min-width: 1280px)')"), 'Use the 1280px desktop media query.');
check(scriptSource.includes('applyResponsiveDisplayState'), 'Responsive state needs one recomputation entry point.');
check(scriptSource.includes("document.querySelector('.right-sidebar-container')"), 'Layout state must target the right-sidebar wrapper.');
check(scriptSource.includes("'hidden-in-grid-mode'"), 'Grid mode must toggle hidden-in-grid-mode.');

function harness(isDesktop, stored = {}) {
	const dom = new JSDOM(
		`<!doctype html><html><body>
			<div id="navbar"></div>
			<div id="banner-wrapper"></div>
			<div id="fullscreen-wallpaper" data-fullscreen-wallpaper style="display:none"></div>
			<div class="absolute w-full z-30 pointer-events-none" style="top:35vh"></div>
			<div id="main-grid" data-layout-mode="list"></div>
			<div id="post-list-container" class="list-mode"></div>
			<div class="right-sidebar-container"></div>
			<button id="display-settings-switch" aria-expanded="false"></button>
			<button id="nav-menu-switch"></button>
			<div id="nav-menu-panel" class="float-panel-closed"><a href="#main">Home</a></div>
			<button id="search-switch"></button>
			<div id="search-panel" class="float-panel-closed"><input /></div>
			<div id="display-setting" class="float-panel-closed">
				<section class="settings-section">
					<input id="panel-hue-slider" data-default="240" value="240" />
					<output id="panel-hue-value"></output>
					<button id="reset-hue"></button>
				</section>
				<section class="desktop-setting-section" data-default="banner">
					<button id="reset-wallpaper-mode"></button>
					<button class="wallpaper-mode-btn" data-mode="banner"></button>
					<button class="wallpaper-mode-btn" data-mode="fullscreen"></button>
					<button class="wallpaper-mode-btn" data-mode="overlay"></button>
					<button class="wallpaper-mode-btn" data-mode="none"></button>
				</section>
				<section id="overlay-settings-section" class="desktop-setting-section" hidden>
					<button id="reset-overlay-settings"></button>
					<input id="overlay-opacity-slider" type="range" min="20" max="100" step="1" value="80" data-default="0.8" />
					<output id="overlay-opacity-value"></output>
					<input id="overlay-blur-slider" type="range" min="0" max="12" step="0.5" value="1.5" data-default="1.5" />
					<output id="overlay-blur-value"></output>
					<input id="overlay-card-opacity-slider" type="range" min="20" max="100" step="1" value="80" data-default="0.8" />
					<output id="overlay-card-opacity-value"></output>
				</section>
				<section class="desktop-setting-section" data-default="list">
					<button id="reset-layout-mode"></button>
					<button class="layout-mode-btn" data-mode="list"></button>
					<button class="layout-mode-btn" data-mode="grid"></button>
				</section>
			</div>
		</body></html>`,
		{ url: 'https://example.test/', runScripts: 'outside-only', pretendToBeVisual: true },
	);
	const { window } = dom;
	let matches = isDesktop;
	const listeners = new Set();
	const media = {
		media: '(min-width: 1280px)',
		get matches() { return matches; },
		addEventListener(type, listener) { if (type === 'change') listeners.add(listener); },
		removeEventListener(type, listener) { if (type === 'change') listeners.delete(listener); },
		addListener(listener) { listeners.add(listener); },
		removeListener(listener) { listeners.delete(listener); },
		set(next) {
			matches = next;
			for (const listener of listeners) listener({ matches, media: this.media });
		},
	};
	window.matchMedia = () => media;
	window.requestAnimationFrame = (callback) => callback();
	for (const [key, value] of Object.entries(stored)) {
		window.localStorage.setItem(key, value);
	}
	window.eval(scriptSource);
	window.document.dispatchEvent(new window.Event('DOMContentLoaded', { bubbles: true }));
	return { dom, media, window };
}

try {
	const desktop = harness(true, { bannerMode: 'fullscreen', layoutMode: 'grid', hue: '120' });
	const { window } = desktop;
	check(window.localStorage.getItem('wallpaperMode') === 'fullscreen', 'Migrate the desktop wallpaper preference.');
	check(window.localStorage.getItem('postListLayout') === 'grid', 'Migrate the desktop layout preference.');
	check(window.localStorage.getItem('bannerMode') === null, 'Remove bannerMode after migration.');
	check(window.localStorage.getItem('layoutMode') === null, 'Remove layoutMode after migration.');
	check(window.document.body.classList.contains('fullscreen-banner'), 'Apply the migrated desktop wallpaper mode.');
	check(window.document.getElementById('post-list-container').classList.contains('grid-mode'), 'Apply the migrated desktop grid mode.');
	check(window.document.querySelector('.right-sidebar-container').classList.contains('hidden-in-grid-mode'), 'Hide the right sidebar in desktop grid mode.');
	check(window.document.querySelector('.wallpaper-mode-btn[data-mode="fullscreen"]').getAttribute('aria-pressed') === 'true', 'Expose wallpaper selected state.');
	check(window.document.querySelector('.layout-mode-btn[data-mode="grid"]').getAttribute('aria-pressed') === 'true', 'Expose layout selected state.');

	const settingsButton = window.document.getElementById('display-settings-switch');
	const settingsPanel = window.document.getElementById('display-setting');
	settingsButton.click();
	check(settingsButton.getAttribute('aria-expanded') === 'true', 'Opening settings synchronizes aria-expanded.');
	check(!settingsPanel.classList.contains('float-panel-closed'), 'Opening settings reveals the panel.');
	window.document.getElementById('nav-menu-switch').click();
	check(settingsButton.getAttribute('aria-expanded') === 'false', 'Opening a peer panel resets settings aria-expanded.');
	settingsButton.click();
	window.document.body.dispatchEvent(new window.MouseEvent('click', { bubbles: true }));
	check(settingsButton.getAttribute('aria-expanded') === 'false', 'Outside click resets settings aria-expanded.');
	settingsButton.click();
	window.document.dispatchEvent(new window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
	check(settingsButton.getAttribute('aria-expanded') === 'false', 'Escape resets settings aria-expanded.');

	const menuButton = window.document.getElementById('nav-menu-switch');
	const menuPanel = window.document.getElementById('nav-menu-panel');
	const searchButton = window.document.getElementById('search-switch');
	const searchPanel = window.document.getElementById('search-panel');
	check(menuButton.getAttribute('aria-expanded') === 'false', 'Menu starts collapsed.');
	check(searchButton.getAttribute('aria-expanded') === 'false', 'Search starts collapsed.');
	menuButton.click();
	check(menuButton.getAttribute('aria-expanded') === 'true', 'Menu announces its open state.');
	menuPanel.querySelector('a').focus();
	window.document.activeElement.dispatchEvent(new window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
	check(menuButton.getAttribute('aria-expanded') === 'false', 'Escape resets menu expanded state.');
	check(window.document.activeElement === menuButton, 'Escape returns menu focus to its trigger.');
	searchButton.click();
	check(searchButton.getAttribute('aria-expanded') === 'true', 'Search announces its open state.');
	check(window.document.activeElement === searchPanel.querySelector('input'), 'Opening search focuses its input.');
	window.document.activeElement.dispatchEvent(new window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
	check(searchButton.getAttribute('aria-expanded') === 'false', 'Escape resets search expanded state.');
	check(window.document.activeElement === searchButton, 'Escape returns search focus to its trigger.');
	searchButton.click();
	menuButton.click();
	check(searchButton.getAttribute('aria-expanded') === 'false', 'Opening menu resets search expanded state.');
	menuButton.click();
	check(menuButton.getAttribute('aria-expanded') === 'false', 'Toggling menu closed resets expanded state.');
	searchButton.click();
	searchButton.click();
	check(searchButton.getAttribute('aria-expanded') === 'false', 'Toggling search closed resets expanded state.');
	menuButton.click();
	window.document.body.click();
	check(menuButton.getAttribute('aria-expanded') === 'false', 'Outside click resets menu expanded state.');
	searchButton.click();
	window.document.body.click();
	check(searchButton.getAttribute('aria-expanded') === 'false', 'Outside click resets search expanded state.');
	settingsButton.focus();
	window.document.dispatchEvent(new window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
	check(window.document.activeElement === settingsButton, 'Escape without an open panel does not move focus.');

	window.document.querySelector('.wallpaper-mode-btn[data-mode="overlay"]').click();
	const wallpaper = window.document.querySelector('[data-fullscreen-wallpaper]');
	const banner = window.document.getElementById('banner-wrapper');
	const overlaySection = window.document.getElementById('overlay-settings-section');
	check(window.localStorage.getItem('wallpaperMode') === 'overlay', 'Desktop overlay control persists its selection.');
	check(window.document.body.classList.contains('overlay-wallpaper'), 'Overlay mode exposes its dedicated body state.');
	check(window.document.body.classList.contains('wallpaper-transparent'), 'Overlay mode enables transparent cards.');
	check(window.document.body.classList.contains('no-banner-mode'), 'Overlay mode uses no-banner content geometry.');
	check(wallpaper.style.display === 'block', 'Overlay mode reveals the dedicated wallpaper.');
	check(banner.style.display === 'none', 'Overlay mode hides the ordinary Banner.');
	check(!overlaySection.hidden, 'Overlay mode reveals its effect controls.');
	check(wallpaper.style.getPropertyValue('--wallpaper-opacity') === '0.8', 'Overlay mode applies default wallpaper opacity.');
	check(wallpaper.style.getPropertyValue('--wallpaper-blur') === '1.5px', 'Overlay mode applies default wallpaper blur.');
	check(window.document.documentElement.style.getPropertyValue('--card-transparent-opacity') === '0.8', 'Overlay mode applies default card opacity.');

	const opacitySlider = window.document.getElementById('overlay-opacity-slider');
	opacitySlider.value = '65';
	opacitySlider.dispatchEvent(new window.Event('input', { bubbles: true }));
	check(window.localStorage.getItem('overlayOpacity') === '0.65', 'Overlay opacity persists normalized values.');
	check(wallpaper.style.getPropertyValue('--wallpaper-opacity') === '0.65', 'Overlay opacity updates the wallpaper variable.');
	check(window.document.getElementById('overlay-opacity-value').textContent === '65%', 'Overlay opacity updates its output.');

	const blurSlider = window.document.getElementById('overlay-blur-slider');
	blurSlider.value = '4.5';
	blurSlider.dispatchEvent(new window.Event('input', { bubbles: true }));
	check(window.localStorage.getItem('overlayBlur') === '4.5', 'Overlay blur persists pixel values.');
	check(wallpaper.style.getPropertyValue('--wallpaper-blur') === '4.5px', 'Overlay blur updates the wallpaper variable.');
	check(window.document.getElementById('overlay-blur-value').textContent === '4.5px', 'Overlay blur updates its output.');

	const cardOpacitySlider = window.document.getElementById('overlay-card-opacity-slider');
	cardOpacitySlider.value = '70';
	cardOpacitySlider.dispatchEvent(new window.Event('input', { bubbles: true }));
	check(window.localStorage.getItem('overlayCardOpacity') === '0.7', 'Overlay card opacity persists normalized values.');
	check(window.document.documentElement.style.getPropertyValue('--card-transparent-opacity') === '0.7', 'Overlay card opacity updates the root variable.');
	check(window.document.getElementById('overlay-card-opacity-value').textContent === '70%', 'Overlay card opacity updates its output.');

	window.document.getElementById('reset-overlay-settings').click();
	check(window.localStorage.getItem('overlayOpacity') === '0.8', 'Overlay reset persists default wallpaper opacity.');
	check(window.localStorage.getItem('overlayBlur') === '1.5', 'Overlay reset persists default blur.');
	check(window.localStorage.getItem('overlayCardOpacity') === '0.8', 'Overlay reset persists default card opacity.');
	check(wallpaper.style.getPropertyValue('--wallpaper-opacity') === '0.8', 'Overlay reset reapplies wallpaper opacity.');
	check(wallpaper.style.getPropertyValue('--wallpaper-blur') === '1.5px', 'Overlay reset reapplies blur.');
	check(window.document.documentElement.style.getPropertyValue('--card-transparent-opacity') === '0.8', 'Overlay reset reapplies card opacity.');

	window.document.querySelector('.wallpaper-mode-btn[data-mode="none"]').click();
	check(window.localStorage.getItem('wallpaperMode') === 'none', 'Desktop wallpaper controls persist their selection.');
	check(window.document.body.classList.contains('no-banner-mode'), 'Desktop wallpaper controls apply their selection.');
	check(!window.document.body.classList.contains('wallpaper-transparent'), 'Leaving overlay removes transparent-card state.');
	check(!window.document.body.classList.contains('overlay-wallpaper'), 'Leaving overlay removes the dedicated body state.');
	check(wallpaper.style.display === 'none' && banner.style.display === 'none', 'None mode hides both wallpaper renderers.');
	check(overlaySection.hidden, 'Leaving overlay hides its effect controls.');
	check(wallpaper.style.getPropertyValue('--wallpaper-opacity') === '', 'Leaving overlay clears wallpaper opacity.');
	check(wallpaper.style.getPropertyValue('--wallpaper-blur') === '', 'Leaving overlay clears wallpaper blur.');
	check(window.document.documentElement.style.getPropertyValue('--card-transparent-opacity') === '', 'Leaving overlay clears card opacity.');
	window.document.querySelector('.layout-mode-btn[data-mode="list"]').click();
	check(window.localStorage.getItem('postListLayout') === 'list', 'Desktop layout controls persist their selection.');
	check(window.document.getElementById('post-list-container').classList.contains('list-mode'), 'Desktop layout controls apply their selection.');
	window.document.querySelector('.wallpaper-mode-btn[data-mode="fullscreen"]').click();
	window.document.querySelector('.layout-mode-btn[data-mode="grid"]').click();

	desktop.media.set(false);
	check(window.document.body.classList.contains('enable-banner'), 'Mobile computes banner wallpaper mode.');
	check(!window.document.body.classList.contains('fullscreen-banner'), 'Mobile does not apply fullscreen wallpaper.');
	check(window.document.getElementById('post-list-container').classList.contains('list-mode'), 'Mobile computes list layout.');
	check(!window.document.querySelector('.right-sidebar-container').classList.contains('hidden-in-grid-mode'), 'Mobile list state releases the right sidebar.');
	check(window.localStorage.getItem('wallpaperMode') === 'fullscreen', 'Mobile preserves the desktop wallpaper preference.');
	check(window.localStorage.getItem('postListLayout') === 'grid', 'Mobile preserves the desktop layout preference.');

	window.document.querySelector('.wallpaper-mode-btn[data-mode="none"]').click();
	window.document.querySelector('.layout-mode-btn[data-mode="list"]').click();
	check(window.localStorage.getItem('wallpaperMode') === 'fullscreen', 'Hidden mobile wallpaper controls cannot overwrite preferences.');
	check(window.localStorage.getItem('postListLayout') === 'grid', 'Hidden mobile layout controls cannot overwrite preferences.');

	desktop.media.set(true);
	check(window.document.body.classList.contains('fullscreen-banner'), 'Desktop wallpaper preference restores after resize.');
	check(window.document.getElementById('post-list-container').classList.contains('grid-mode'), 'Desktop layout preference restores after resize.');

	const hueSlider = window.document.getElementById('panel-hue-slider');
	hueSlider.value = '180';
	hueSlider.dispatchEvent(new window.Event('input', { bubbles: true }));
	check(window.localStorage.getItem('hue') === '180', 'Hue changes persist.');
	check(window.document.documentElement.style.getPropertyValue('--hue') === '180', 'Hue changes update the CSS variable.');
	check(window.document.getElementById('panel-hue-value').value === '180\u00b0', 'Hue changes update the displayed value.');

	window.document.getElementById('reset-wallpaper-mode').click();
	window.document.getElementById('reset-layout-mode').click();
	window.document.getElementById('reset-hue').click();
	check(window.localStorage.getItem('wallpaperMode') === null, 'Wallpaper reset clears the saved override.');
	check(window.localStorage.getItem('postListLayout') === null, 'Layout reset clears the saved override.');
	check(window.document.body.classList.contains('enable-banner') && !window.document.body.classList.contains('fullscreen-banner') && !window.document.body.classList.contains('no-banner-mode'), 'Wallpaper reset applies only its default mode.');
	check(window.document.getElementById('post-list-container').classList.contains('list-mode'), 'Layout reset applies its default.');
	check(window.document.getElementById('panel-hue-slider').value === '240', 'Hue reset restores its configured default.');
	check(window.localStorage.getItem('hue') === null, 'Hue reset clears the saved override.');
	check(window.document.documentElement.style.getPropertyValue('--hue') === '240', 'Hue reset reapplies the configured default.');
	check(window.document.getElementById('panel-hue-value').value === '240\u00b0', 'Hue reset updates the displayed value.');
	desktop.dom.window.close();

	const migratedNone = harness(true, { bannerMode: 'off', layoutMode: 'list' });
	check(migratedNone.window.localStorage.getItem('wallpaperMode') === 'none', 'Migrate legacy off wallpaper mode to none.');
	check(migratedNone.window.document.body.classList.contains('no-banner-mode'), 'Apply the migrated none wallpaper mode.');
	migratedNone.dom.window.close();

	const mobile = harness(false, {
		wallpaperMode: 'overlay',
		postListLayout: 'grid',
		overlayOpacity: '0.65',
		overlayBlur: '4.5',
		overlayCardOpacity: '0.7',
	});
	check(!mobile.window.document.body.classList.contains('fullscreen-banner'), 'Initial mobile state ignores desktop fullscreen preference.');
	check(!mobile.window.document.body.classList.contains('overlay-wallpaper'), 'Initial mobile state ignores desktop overlay preference.');
	check(!mobile.window.document.body.classList.contains('wallpaper-transparent'), 'Initial mobile state does not enable transparent cards.');
	check(mobile.window.document.querySelector('[data-fullscreen-wallpaper]').style.display === 'none', 'Initial mobile state hides the overlay wallpaper.');
	check(mobile.window.document.getElementById('overlay-settings-section').hidden, 'Initial mobile state hides overlay effects.');
	check(mobile.window.document.getElementById('post-list-container').classList.contains('list-mode'), 'Initial mobile state ignores desktop grid preference.');
	check(mobile.window.localStorage.getItem('wallpaperMode') === 'overlay', 'Initial mobile state leaves wallpaper storage untouched.');
	check(mobile.window.localStorage.getItem('postListLayout') === 'grid', 'Initial mobile state leaves layout storage untouched.');
	check(mobile.window.localStorage.getItem('overlayOpacity') === '0.65', 'Initial mobile state leaves overlay opacity untouched.');
	mobile.dom.window.close();
} catch (error) {
	failures.push(`Runtime harness error: ${error.stack || error}`);
}

if (failures.length) {
	for (const failure of failures) console.error(`FAIL: ${failure}`);
	process.exit(1);
}

console.log('PASS: unified settings panel and responsive preference behavior.');
