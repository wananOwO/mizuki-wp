# Mizuki WordPress Upstream Parity Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use `dispatching-parallel-agents` for independent work, then apply TDD and verification before integration. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the WordPress port match pinned upstream Mizuki for homepage controls, wallpaper/layout composition, navigation, typography, grid cards, and release behavior; add a secure dashboard-editable announcement; then prove every state in native WordPress and a real browser.

**Architecture:** Keep WordPress/PHP rendering and the existing vanilla JavaScript runtime, but align state names and DOM/CSS responsibilities with upstream. The unified desktop settings panel owns theme color, four wallpaper modes, overlay effects, and layout; mobile retains theme color only. A dedicated fixed wallpaper layer owns overlay rendering, while the ordinary Banner remains responsible for banner/fullscreen modes. WordPress `theme_mod` remains the shared persistence boundary for dashboard/Customizer/front-end announcement data, and narrowly scoped CSS restores upstream navigation/card/font behavior.

**Tech Stack:** WordPress classic theme, native Windows PHP 8.4, WordPress SQLite database integration, vanilla JavaScript, CSS, Playwright.

## Global Constraints

- Follow upstream `master` tree `9abe8ac790b1217abae0a5f5fd2d78d5b604c969` for the unified settings-panel design, per the user's correction.
- Preserve the explicit requirement that mobile does not expose or apply wallpaper/layout switching.
- Grid mode hides the right sidebar and changes the desktop main grid to `17.5rem 1fr`.
- Fullscreen mode must show homepage cards in the first viewport over the image background, not below a separate 100vh page.
- Preserve unrelated existing user files and changes.
- Add failing behavior tests before production edits.
- Do not use Docker; provision and run WordPress directly with Windows PHP.
- Keep current upstream's single display panel; do not copy the older demo's separated controls.
- Add overlay with upstream option order, storage keys, ranges, and defaults: `overlayOpacity=0.8`, `overlayBlur=1.5`, `overlayCardOpacity=0.8`.
- Overlay/wallpaper/layout controls remain desktop-only under the user's explicit mobile requirement.
- Store the editable announcement in `mizuki_announcement`, protected by `edit_theme_options`, a WordPress nonce, unslashing, and `sanitize_textarea_field()`.
- Desktop dropdowns must open on hover, keyboard `focus-within`, and explicit click/ARIA state; mobile navigation remains separate.
- Load the packaged Zen Maru Gothic and Loli files through real `@font-face` rules and the same upstream font-stack order.
- Increment `style.css`, `MIZUKI_VERSION`, release tag/body, and package evidence together after the behavior is verified.

---

## Root-Cause Report

### 1. Settings Control Architecture

The original report described separate controls, but the user corrected the target after investigation. Current upstream `SettingsPanel.svelte` combines theme color, wallpaper mode, and layout in one 320px grouped panel. The WordPress port already combines the three concepts, so separation is not a bug. Its remaining mismatch is presentational and behavioral: the port uses a 256px panel with plain labels and segmented text buttons, lacks upstream section headers/reset affordances/icons/selected-state treatment, and does not gate wallpaper/layout sections by viewport.

### 2. Fullscreen Homepage Composition

Historical commit `958229c` changed the main wrapper to `top: 0` and fixed the banner, but the existing tests only search for those strings. They do not assert that a post card intersects the first viewport, and the port leaves fullscreen positioning split across server inline styles, `mizuki-banner.css`, `mizuki-layout-modes.css`, and runtime body classes. This duplicated ownership makes a regression easy and does not prove the reported symptom is gone. The required fix is to make one fullscreen state contract and test actual computed geometry: banner covers the viewport in the background, main content is positioned at the top layer, and the first post card begins before `window.innerHeight`.

### 3. Grid Mode Right Sidebar

The cause is explicit and confirmed. `mizuki-layout-modes.css` says the port intentionally preserves the right sidebar, then forces `.hidden-in-grid-mode` back to `display: block`. `mizuki-theme.js` never toggles a right-sidebar wrapper, and `mizuki_render_right_sidebar()` does not render upstream's `.right-sidebar-container`. As a result, the third grid item remains in a three-column template and can occupy the wrong column. Upstream instead adds `hidden-in-grid-mode`, sets it to `display: none`, and changes desktop grid columns to `17.5rem 1fr` with the main content in column 2.

### 4. Unsupported Mobile Wallpaper/Layout Switching

The port renders wallpaper and layout controls in the same panel at every viewport and applies `localStorage` state before `DOMContentLoaded` without a media-query guard. Its mobile CSS only collapses grid cards to one column; the page is still logically in grid/fullscreen mode. The fix must hide those two sections below 1280px and force the computed runtime state to server-default banner + list without overwriting the saved desktop preference.

### 5. Per-Post Word Count

`index.php` calls `str_word_count(wp_strip_all_tags(...))`, which counts Latin words and returns zero or severe undercounts for Chinese/Japanese text. The theme already provides `mizuki_word_count()` using Unicode `mb_strlen()` after stripping whitespace. The homepage bypasses that helper. The card must call the shared helper so CJK content and mixed-language content are counted consistently.

### Blocking Test Defect

`test-full-render.php` currently fatals under PHP 8.4 because its WordPress stub leaves `$wpdb` null, and it can emit a non-numeric timestamp warning. It therefore cannot serve as integration evidence. The test harness needs a deterministic `$wpdb` stub and date/content functions before it can validate the theme render.

### 6. Grid Card Selector Leakage Found During Visual Audit

The bundled base stylesheet contains `.post-list-container.grid-mode .post-card-item a[href]`, which applies title font size, two-line clamping, box display, and overflow to every link in a grid card. That selector enlarges category/tag metadata and gives the cover link title-only properties. The repair adds an explicit `.post-card-title` class for title typography and uses later, narrowly scoped cover and `text-sm`/`text-xs` metadata rules to neutralize the legacy bundle without applying a new all-links override. Playwright verifies cover-before-text ordering, no cover line clamp, and metadata fonts smaller than the title.

### 7. Settings Button Accessibility State

`#display-settings-switch` starts with `aria-expanded="false"`, but the click handler only changes `float-panel-closed` and never writes the new state back. The panel visibly opens while assistive technology continues to receive “collapsed.” The same open/close function must be the single owner of both panel visibility and `aria-expanded`; outside-click and Escape closure must also return it to `false`.

### 8. Stale Release Automation

`tools/create-release.py` is pinned to `v1.1.0`, describes an obsolete Python packer, and claims grid mode “restores” the right sidebar. That statement directly contradicts pinned upstream and the repaired tests. Release metadata must be generated from the new synchronized theme version and describe hidden grid sidebar, unified settings, overlay, announcement, dropdown, accent, font, and native-PHP verification accurately.

### 9. Missing Overlay Wallpaper Mode

The port already defines `--wallpaper-opacity`, `--wallpaper-blur`, `--card-transparent-opacity`, and transparent-card rules, but it exposes only banner/fullscreen/none and has no dedicated wallpaper layer. Pinned upstream renders `FullscreenWallpaper` independently from `Banner`, displays it only for `overlay`, hides the normal Banner, starts content at `5.5rem`, and applies `wallpaper-transparent no-banner-mode`. The WordPress port must add the fourth option and an independent fixed layer, then persist upstream's exact controls: `overlayOpacity` 20–100% (stored 0–1), `overlayBlur` 0–12px step 0.5, and `overlayCardOpacity` 20–100% (stored 0–1), with defaults `0.8`, `1.5`, and `0.8`.

### 10. Announcement Is Not Administratively Editable

`inc/parts/sidebar.php` already reads `mizuki_announcement`, but `inc/customizer.php` neither registers it nor saves/renders it in the existing “Mizuki 主题” page. Administrators therefore only see the hard-coded default. Add the control to that existing top-level page and the Customizer compatibility surface, use the same theme-mod key, and strengthen the current save branch with the page's `edit_theme_options` capability before any write.

### 11. Desktop Submenus Animate but Do Not Open

The markup and click handler are present. `assets/css/mizuki-overrides.css:105-124` intentionally forces hover/focus menus to hidden while leaving chevron hover animation active. Remove that click-only policy and restore the upstream visibility state for `.dropdown-container:hover`, `.dropdown-container:focus-within`, and `[aria-expanded="true"]`; test all “我的 / 关于 / 更多” containers against real computed visibility.

### 12. Grid Title Accent Uses the Wrong Containing Block

The screenshot and browser geometry show the accent center about 11px below the title center on every grid card. `.post-card-title` is `position: static`, so its pseudo-element is positioned relative to a larger text region and drifts toward the metadata icon. Make each title its own positioned containing block and center the pseudo-element with `top: 50%` plus `translateY(-50%)`; browser geometry must keep the two centers within 1px for every visible grid card.

### 13. Bundled Fonts Are Never Loaded

`assets/fonts/ZenMaruGothic-Medium.ttf` and `assets/fonts/loli.ttf` exist, but no `@font-face` references either file. Computed `body` font is Roboto and the browser makes no font request. Add a late-loaded font stylesheet declaring Zen Maru Gothic at weight 500 and Loli at weight 400, then set the upstream stack order `var(--font-body), var(--font-cjk), ...`. Verification must observe same-origin font requests, successful `document.fonts` loads, and the expected computed family; file existence alone is not evidence.

---

### Task 1: Behavior Regression Suite

**Files:**
- Create: `tests/theme-behavior-test.php`
- Modify: `test-full-render.php`

**Interfaces:**
- Consumes: theme PHP templates and source assets.
- Produces: deterministic assertions for Unicode word counts, unified panel sections, mobile gating classes, right-sidebar wrapper, grid hide/expand rules, and fullscreen first-viewport contract.

- [x] **Step 1: Write failing PHP/source assertions**

Assert all of the following before implementation:

```php
assert_same( 8, mizuki_count_text( '中文测试 English test' ) );
assert_contains( 'class="right-sidebar-container', $rendered_html );
assert_contains( 'desktop-setting-section', $rendered_html );
assert_not_contains( 'display: block !important', $grid_sidebar_rule );
assert_contains( 'grid-template-columns: 17.5rem 1fr', $layout_css );
```

- [x] **Step 2: Run tests and verify failure**

Run: `php tests/theme-behavior-test.php`

Expected: FAIL on missing right-sidebar wrapper/mobile gating and current incorrect grid visibility; the Unicode fixture must demonstrate the current homepage path undercounts CJK.

- [x] **Step 3: Repair the full-render stub**

Provide deterministic `get_post_field()`, `get_the_ID()`, timestamp/date values, and a `$wpdb` object whose `get_var()` returns a fixed total. Do not alter production behavior to satisfy the stub.

- [x] **Step 4: Re-run the baseline tests**

Run: `php test-full-render.php`

Expected: template renders to completion; behavior assertions may still fail until later tasks.

---

### Task 2: Unicode Word Count

**Files:**
- Modify: `theme/mizuki-wp/inc/template-tags.php`
- Modify: `theme/mizuki-wp/index.php`
- Test: `tests/theme-behavior-test.php`

**Interfaces:**
- Produces: `mizuki_count_text( string $content ): int` and consistent `mizuki_word_count( ?int $post_id ): int`.

- [x] **Step 1: Add a failing mixed-language test**

The test must cover CJK, Latin text, HTML tags, whitespace, and an empty string.

- [x] **Step 2: Verify RED**

Run: `php tests/theme-behavior-test.php --filter=word-count`

Expected: FAIL because the homepage still calls `str_word_count()` and the pure helper does not exist.

- [x] **Step 3: Implement the shared counter**

Strip HTML, remove Unicode whitespace, and count Unicode code points with `mb_strlen`; make `mizuki_word_count()` delegate to it. Replace the homepage's `str_word_count()` call with `mizuki_word_count( get_the_ID() )`.

- [x] **Step 4: Verify GREEN**

Run: `php tests/theme-behavior-test.php --filter=word-count`

Expected: all word-count fixtures pass.

---

### Task 3: Unified Settings Panel and Mobile Gate

**Files:**
- Modify: `theme/mizuki-wp/inc/parts/navbar.php`
- Modify: `theme/mizuki-wp/assets/js/mizuki-theme.js`
- Modify: `theme/mizuki-wp/assets/css/mizuki-layout-modes.css`
- Test: `tests/theme-behavior-test.php`

**Interfaces:**
- Persists desktop preferences under `wallpaperMode` and `postListLayout`, with one-time migration from `bannerMode` and `layoutMode`.
- Produces: `applyResponsiveDisplayState()` that computes mobile as banner/list while preserving desktop storage.

- [x] **Step 1: Add failing markup/state tests**

Require a 320px unified panel, upstream-like grouped section headings and icons, separate reset affordances, and `desktop-setting-section` wrappers around wallpaper/layout only.

- [x] **Step 2: Verify RED**

Run: `php tests/theme-behavior-test.php --filter=settings`

Expected: FAIL on current 256px/basic markup and missing mobile gate.

- [x] **Step 3: Implement upstream-aligned markup**

Keep one palette button and one `#display-setting`. Render theme color first. Render wallpaper and layout as grouped desktop-only sections with icon + selected-state controls and reset buttons. Do not add mobile wallpaper/layout buttons elsewhere.

- [x] **Step 4: Implement responsive state**

Use `matchMedia('(min-width: 1280px)')`. Desktop reads/persists migrated preferences. Mobile applies banner/list to DOM only. On breakpoint changes, reapply the appropriate computed state without erasing desktop preferences.

- [x] **Step 5: Verify GREEN**

Run: `php tests/theme-behavior-test.php --filter=settings`

Expected: settings and mobile-state source assertions pass.

---

### Task 4: Grid Sidebar and Fullscreen Geometry

**Files:**
- Modify: `theme/mizuki-wp/inc/parts/sidebar.php`
- Modify: `theme/mizuki-wp/assets/js/mizuki-theme.js`
- Modify: `theme/mizuki-wp/assets/css/mizuki-layout-modes.css`
- Modify only if required by measured output: `theme/mizuki-wp/header.php`, `theme/mizuki-wp/inc/parts/banner.php`
- Test: `tests/theme-behavior-test.php`

**Interfaces:**
- Produces: `.right-sidebar-container`, `.hidden-in-grid-mode`, desktop two-column grid mode, and a single fullscreen geometry contract.

- [x] **Step 1: Add failing structural/CSS tests**

Require the wrapper, `display: none !important`, desktop `17.5rem 1fr`, `#swup-container` column 2, and no rule that forces a hidden grid sidebar to display.

- [x] **Step 2: Verify RED**

Run: `php tests/theme-behavior-test.php --filter=layout`

Expected: FAIL against the current force-visible rule and missing wrapper.

- [x] **Step 3: Implement grid behavior**

Wrap the right sidebar, toggle `hidden-in-grid-mode` with layout state, collapse the desktop main grid to two columns, and place the main content in column 2. Below 1280px, computed layout remains list.

- [x] **Step 4: Consolidate fullscreen rules**

Ensure the image layer covers the viewport behind content, the main wrapper is top-layer content at `top: 0`, and no 100vh spacer precedes the cards. Remove contradictory fullscreen declarations rather than adding a third override.

- [x] **Step 5: Verify GREEN**

Run: `php tests/theme-behavior-test.php --filter=layout`

Expected: structural/CSS assertions pass.

---

### Task 5: Real WordPress and Visual Verification

**Files:**
- Create: `tools/audit-local.mjs`
- Modify: `tools/audit-upstream.mjs` only if a shared metric is needed.
- Runtime only: local WordPress test directory outside tracked theme files.

**Interfaces:**
- Produces screenshots and JSON metrics for banner/list, fullscreen/list, banner/grid, and mobile states.

- [x] **Step 1: Provision actual WordPress**

Install WordPress under a disposable local directory, enable the official SQLite integration drop-in, run it with the installed Windows PHP 8.4 built-in server, and complete setup through WP-CLI or Playwright. Docker is explicitly out of scope.

- [x] **Step 2: Install and activate the theme**

Symlink or copy `theme/mizuki-wp`, activate it, import deterministic mixed Chinese/English posts with featured images, and enable `WP_DEBUG_LOG`.

- [x] **Step 3: Run PHP/theme checks**

Run PHP syntax checks over every theme PHP file, activate the theme, request homepage/post/archive pages, and assert no fatal/error/warning from theme code in the WordPress debug log.

- [x] **Step 4: Run Playwright state checks**

At 1440x900 assert fullscreen first-card top is less than 900, grid right sidebar is hidden, and main grid has two columns. At 390x844 assert wallpaper/layout controls are absent/hidden and DOM state remains banner/list even when desktop preferences were seeded.

- [x] **Step 5: Capture and compare screenshots**

Capture matching desktop/mobile viewports for local and upstream demo. For the corrected unified settings panel, compare local DOM/style against current upstream source because the demo is older. Record unavoidable content differences separately from structural parity.

- [x] **Step 6: Final verification**

Run all regression tests, full render, PHP lint, Playwright checks, and package validation from a fresh theme archive.

---

### Task 6: Dashboard-Editable Announcement

**Files:**
- Modify: `theme/mizuki-wp/inc/customizer.php`
- Verify unchanged renderer contract: `theme/mizuki-wp/inc/parts/sidebar.php`
- Create: `tests/announcement-settings-regression.php`

**Interfaces:**
- Consumes and produces: `theme_mod('mizuki_announcement')` as sanitized plain multiline text.
- Produces: one Customizer setting/control and one field in the existing `mizuki-theme-settings` admin form.

- [x] **Step 1: Write the failing registration/save/markup test**

The test reads the actual PHP source and renders the existing sidebar stub. It must require all of these contracts:

```php
assert_contains( "add_setting( 'mizuki_announcement'", $customizer_source );
assert_contains( "sanitize_callback' => 'sanitize_textarea_field'", $customizer_source );
assert_contains( "current_user_can( 'edit_theme_options' )", $admin_save_branch );
assert_contains( "set_theme_mod( 'mizuki_announcement'", $admin_save_branch );
assert_contains( 'name="mizuki_announcement"', $admin_markup );
assert_contains( 'get_theme_mod( \'mizuki_announcement\'', $sidebar_source );
assert_contains( 'esc_html( $announcement )', $sidebar_source );
```

- [x] **Step 2: Verify RED**

Run: `php tests/announcement-settings-regression.php`

Expected: FAIL on missing setting, dashboard field, save call, and capability gate while confirming the existing front-end read/escape path.

- [x] **Step 3: Register the shared setting and secure the save branch**

Add the setting with `sanitize_textarea_field`, render a textarea in a dedicated “网站公告” accordion, and gate the nonce-valid save block before any `set_theme_mod()` call:

```php
if (
	isset( $_POST['mizuki_settings_nonce'] ) &&
	wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mizuki_settings_nonce'] ) ), 'mizuki_save_settings' ) &&
	current_user_can( 'edit_theme_options' )
) {
	$announcement = isset( $_POST['mizuki_announcement'] )
		? sanitize_textarea_field( wp_unslash( $_POST['mizuki_announcement'] ) )
		: '';
	set_theme_mod( 'mizuki_announcement', $announcement );
}
```

- [x] **Step 4: Verify GREEN**

Run: `php tests/announcement-settings-regression.php`

Expected: PASS with the dashboard, Customizer, and front-end bound to the same sanitized key.

---

### Task 7: Desktop Hover Menus and Grid Title Accent

**Files:**
- Modify: `theme/mizuki-wp/assets/css/mizuki-overrides.css`
- Create: `tests/navigation-grid-regression.mjs`

**Interfaces:**
- Consumes: existing `.dropdown-container`, `.dropdown-trigger`, `.dropdown-menu`, and `.post-card-title` markup.
- Produces: desktop hover/focus/click visibility and a title-local pseudo-element containing block.

- [x] **Step 1: Write failing source and geometry contracts**

Require the final override layer to open menus for hover/focus/ARIA, forbid any hover/focus rule whose body sets `opacity: 0`, and require title-relative centering:

```js
check(!hiddenHoverOverride.test(css), 'desktop hover must not be forced closed');
check(openHoverRule.test(css), 'hover, focus-within, and aria-expanded open the menu');
check(/\.post-card-title\s*\{[^}]*position:\s*relative/s.test(css), 'title owns accent positioning');
check(/\.post-card-title::before\s*\{[^}]*top:\s*50%[^}]*translateY\(-50%\)/s.test(css), 'accent centers on title');
```

- [x] **Step 2: Verify RED**

Run: `node tests/navigation-grid-regression.mjs`

Expected: FAIL because the existing high-specificity override explicitly closes hover/focus menus and the title is not positioned.

- [x] **Step 3: Restore the upstream state union and local positioning**

Use one desktop rule whose visible state is the union of hover, `focus-within`, and explicit click state:

```css
@media (min-width: 768px) {
	#navbar-links-container .dropdown-container:hover .dropdown-menu,
	#navbar-links-container .dropdown-container:focus-within .dropdown-menu,
	#navbar-links-container .dropdown-trigger[aria-expanded="true"] + .dropdown-menu {
		opacity: 1;
		visibility: visible;
		pointer-events: auto;
		translate: var(--tw-translate-x, 0) 0;
	}
}

.post-list-container.grid-mode .post-card-title {
	position: relative;
}

.post-list-container.grid-mode .post-card-title::before {
	top: 50%;
	transform: translateY(-50%);
}
```

- [x] **Step 4: Verify GREEN**

Run: `node tests/navigation-grid-regression.mjs`

Expected: PASS without changing mobile navigation markup or click handling.

---

### Task 8: Packaged Font Loading

**Files:**
- Create: `theme/mizuki-wp/assets/css/mizuki-fonts.css`
- Modify: `theme/mizuki-wp/inc/enqueue.php`
- Create: `tests/font-loading-regression.php`

**Interfaces:**
- Consumes: `assets/fonts/ZenMaruGothic-Medium.ttf` and `assets/fonts/loli.ttf`.
- Produces: `--font-body`, `--font-cjk`, and the upstream-ordered `body` stack after bundled CSS.

- [x] **Step 1: Write the failing declaration/enqueue test**

Require two real font URLs, weights 500/400, `font-display: swap`, late enqueue, and stack order:

```php
assert_contains( '../fonts/ZenMaruGothic-Medium.ttf', $font_css );
assert_contains( '../fonts/loli.ttf', $font_css );
assert_matches( '/font-weight:\s*500/', $font_css );
assert_matches( '/font-weight:\s*400/', $font_css );
assert_contains( "'mizuki-fonts'", $enqueue_source );
assert_matches( '/font-family:\s*var\(--font-body\),\s*var\(--font-cjk\)/', $font_css );
```

- [x] **Step 2: Verify RED**

Run: `php tests/font-loading-regression.php`

Expected: FAIL because no font stylesheet or `@font-face` declaration exists.

- [x] **Step 3: Add the actual upstream font stack**

Create a stylesheet loaded after `mizuki-main`:

```css
@font-face {
	font-family: "Zen Maru Gothic";
	src: url("../fonts/ZenMaruGothic-Medium.ttf") format("truetype");
	font-style: normal;
	font-weight: 500;
	font-display: swap;
}

@font-face {
	font-family: "Loli";
	src: url("../fonts/loli.ttf") format("truetype");
	font-style: normal;
	font-weight: 400;
	font-display: swap;
}

:root {
	--font-body: "Zen Maru Gothic";
	--font-cjk: "Loli";
}

body {
	font-family: var(--font-body), var(--font-cjk), sans-serif;
}
```

- [x] **Step 4: Verify GREEN**

Run: `php tests/font-loading-regression.php`

Expected: PASS and both font files remain inside the packaged theme.

---

### Task 9: Overlay Mode and Settings ARIA

**Files:**
- Modify: `theme/mizuki-wp/inc/parts/navbar.php`
- Modify: `theme/mizuki-wp/inc/parts/banner.php`
- Modify: `theme/mizuki-wp/header.php`
- Modify: `theme/mizuki-wp/assets/js/mizuki-theme.js`
- Modify: `theme/mizuki-wp/assets/css/mizuki-settings-panel.css`
- Modify: `theme/mizuki-wp/assets/css/mizuki-layout-modes.css`
- Modify: `tests/settings-panel-regression.mjs`

**Interfaces:**
- Extends wallpaper mode to `banner | fullscreen | overlay | none`.
- Persists `overlayOpacity`, `overlayBlur`, and `overlayCardOpacity`; applies `--wallpaper-opacity`, `--wallpaper-blur`, and `--card-transparent-opacity`.
- Keeps `#display-settings-switch[aria-expanded]` synchronized with panel visibility.

- [x] **Step 1: Extend the harness with failing overlay and ARIA assertions**

Require four options, three conditional ranges, exact defaults/ranges/storage keys, dedicated wallpaper markup, overlay classes/variables, mobile rejection, reset, and open/close ARIA:

```js
check(wallpaperButtons.length === 4, 'render banner/fullscreen/overlay/none');
clickSettings();
check(settingsButton.getAttribute('aria-expanded') === 'true', 'opening updates ARIA');
clickOverlay();
check(localStorage.getItem('wallpaperMode') === 'overlay', 'persist overlay mode');
check(document.body.classList.contains('wallpaper-transparent'), 'enable transparent cards');
check(getComputedStyle(wallpaper).getPropertyValue('--wallpaper-blur') === '1.5px', 'apply default blur');
```

- [x] **Step 2: Verify RED**

Run: `node tests/settings-panel-regression.mjs`

Expected: FAIL on missing fourth option/effects/layer and stale `aria-expanded`.

- [x] **Step 3: Render upstream-equivalent controls and layer**

Add the overlay row after fullscreen. Add an overlay-only effects section with ranges `20..100/1`, `0..12/0.5`, `20..100/1`, outputs, and one reset control. Render a fixed `#fullscreen-wallpaper` sibling of Banner using the configured Banner image, not a second stretched Banner hero.

- [x] **Step 4: Implement one wallpaper state transaction**

On desktop, overlay must hide normal Banner, show `#fullscreen-wallpaper`, add `overlay-wallpaper wallpaper-transparent no-banner-mode`, set main top to `5.5rem`, and apply the three variables. Leaving overlay must hide the dedicated layer and remove overlay-only variables/classes. Mobile must still compute banner/list and must not write stored desktop overlay values.

- [x] **Step 5: Synchronize panel accessibility state**

Every panel transition uses one helper:

```js
function setSettingsPanelOpen(open) {
	displaySetting.classList.toggle('float-panel-closed', !open);
	settingsButton.setAttribute('aria-expanded', String(open));
}
```

Button toggle, outside click, Escape, and mutually exclusive panel opening all call that helper.

- [x] **Step 6: Verify GREEN**

Run: `node tests/settings-panel-regression.mjs`

Expected: PASS for desktop overlay/reset/ARIA and existing mobile/banner/fullscreen/none/layout behavior.

---

### Task 10: Version and Release Tooling

**Files:**
- Modify: `theme/mizuki-wp/style.css`
- Modify: `theme/mizuki-wp/functions.php`
- Modify: `theme/mizuki-wp/readme.txt`
- Modify: `package.json`
- Modify: `tools/create-release.py`
- Modify: `tests/theme-version-regression.php`

**Interfaces:**
- Produces one synchronized follow-up version `1.0.9` and release tag `v1.0.9`.
- Does not publish a GitHub release during verification; only validates script metadata/syntax.

- [x] **Step 1: Extend the failing version regression**

Parse all five metadata sources and require `style.css == MIZUKI_VERSION == readme stable tag == package.json == create-release TAG`, plus reject the obsolete “恢复右侧边栏” wording.

- [x] **Step 2: Verify RED**

Run: `php tests/theme-version-regression.php`

Expected: FAIL because current theme is `1.0.8`, readme/package metadata are stale, and release tag/body contradict the implementation.

- [x] **Step 3: Synchronize metadata and rewrite release notes**

Set theme/cache/readme/package to `1.0.9`, release tag to `v1.0.9`, and describe the complete verified parity work. Keep the existing owner/repository and upload behavior; do not call the GitHub API without an explicit publishing request.

- [x] **Step 4: Verify GREEN and Python syntax**

Run: `php tests/theme-version-regression.php`

Run: `python -m py_compile tools/create-release.py`

Expected: both commands exit 0.

---

### Task 11: Native WordPress and Browser Acceptance Gate

**Files:**
- Modify: `tools/audit-local.mjs`
- Add: `tests/audit-local-regression.mjs`
- Modify: `tools/pack-theme.mjs`
- Add: `tools/package-validation.mjs`
- Add: `tests/package-tool-regression.mjs`
- Modify: `package.json`
- Delete after evidence capture: `tools/debug-followup.mjs`
- Rebuild: `mizuki-wp-theme.zip`
- Update: `findings.md`, `progress.md`, `task_plan.md`

**Interfaces:**
- Consumes: native Windows PHP WordPress at `http://127.0.0.1:8888` and the existing administrator fixture.
- Produces: repeatable browser assertions, screenshots, clean logs/routes, and a validated installable archive.

- [x] **Step 1: Prove dashboard announcement persistence in real WordPress**

Log in as the fixture administrator, open `wp-admin/admin.php?page=mizuki-theme-settings`, save a unique Unicode announcement, reload the admin page, then visit `/` and assert exact visible text. Submit an HTML-bearing value and assert it is rendered as text, not executable markup.

- [x] **Step 2: Prove desktop interaction and geometry**

At 1440x900 hover each populated “我的 / 关于 / 更多” dropdown without clicking and assert computed opacity/visibility/pointer-events plus a visible child link. In grid mode calculate each title center and its `::before` center; require absolute difference `<= 1px`.

- [x] **Step 3: Prove fonts are used, not merely shipped**

Capture same-origin requests ending in both `.ttf` files, call `document.fonts.load()` for both family names, require `document.fonts.check()` true, and assert computed `body.fontFamily` begins with Zen Maru Gothic followed by Loli.

- [x] **Step 4: Prove overlay effects and responsive isolation**

Select overlay, move all three sliders away from defaults, reload, and assert storage, output labels, CSS variables, fixed-layer geometry, hidden ordinary Banner, transparent cards, and content below navbar. Reset to `0.8/1.5/0.8`. At 390x844 seed overlay/grid and assert computed banner/list, hidden wallpaper/layout/effect controls, and untouched desktop storage.

- [x] **Step 5: Run the complete native verification matrix**

Run all PHP files through `php -l`, execute `npm.cmd test`, `npm.cmd run test:browser`, route-check home/search/posts/categories, and inspect both WordPress debug log and PHP server log from the verification start offset. Any theme warning/error/fatal fails the gate.

- [x] **Step 6: Rebuild and validate the archive**

Run `npm.cmd run test:package`; require one `mizuki-wp/` top-level directory, forward-slash paths, synchronized `1.0.9` metadata, both fonts, all new CSS, and no test/debug/planning files. Delete only the explicitly temporary `tools/debug-followup.mjs` after its evidence has moved into the permanent audit.

---

## Self-Review

- Spec coverage: the original five fixes remain represented; the approved ARIA/release/overlay work and all four new defects each have a root-cause section, implementation task, RED/GREEN test, and native-browser acceptance assertion.
- Placeholder scan: no deferred implementation placeholders remain.
- Type/state consistency: wallpaper uses `wallpaperMode`; layout uses `postListLayout`; overlay effects use upstream's `overlayOpacity`, `overlayBlur`, and `overlayCardOpacity`; mobile computes but does not persist banner/list overrides.
- Ownership consistency: announcement changes only `customizer.php`; navigation/accent changes only `mizuki-overrides.css`; fonts own a new stylesheet plus enqueue; the main overlay integration owns shared navbar/runtime/audit files.
- Scope: additional non-blocking theme defects will be reported separately rather than silently fixed.

## Baseline Completion Evidence (Phases 1-7)

- Automated regressions cover Unicode/entity counts, extension-independent reading time, 13 layout geometry contracts, asset version consistency, unified settings runtime behavior, full PHP template rendering, and legacy layout integration.
- Native Windows PHP 8.4.22 runs WordPress 7.0.1 with SQLite and the activated theme at `http://127.0.0.1:8888`; homepage, search, three posts, and two category archives return HTTP 200 without new WordPress/PHP diagnostics.
- Playwright covers banner/list, fullscreen/list, banner/grid, and mobile seeded with desktop preferences. It verifies first-viewport fullscreen cards, two-track grid geometry, hidden right sidebar, desktop-only wallpaper/layout state, settings interactions/resets, word-count metadata, and a clean browser console.
- The release archive is validated as a single `mizuki-wp/` top-level folder using ZIP-standard forward-slash paths.

## Follow-up Completion Gate (Phases 8-11)

- Complete. Tasks 6-11 have fresh RED/GREEN regressions, native WordPress admin/front-end evidence, browser geometry/network/font assertions, clean incremental logs and routes, and a newly validated `1.0.9` archive.
- The final browser gate covers five responsive/display scenarios, announcement save/sanitize/restore, desktop dropdown hover/focus/click/second-click/Escape/re-entry, seven HTTP routes, and both WordPress/PHP appended-log streams.
- The final archive has one safe `mizuki-wp/` root, synchronized metadata, all unconditional PHP dependencies and required runtime/font assets, and no Markdown/test/tool/debug/planning content.
