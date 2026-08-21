# Mobile Article Containment and QQ Icon Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Fix mobile article-card clipping in Chromium and Firefox and render the correct QQ brand icon without changing Mizuki's upstream visual design.

**Architecture:** Restore the single-post card width contract from pinned upstream Mizuki commit `9ccb27375b938b5cb0b1bc1dbeb45c7fcfad8c94`, then replace the port's hand-drawn QQ glyph with the SVG emitted by upstream's configured `fa7-brands:qq` Iconify icon. Keep wrapping, typography, spacing, colors, and interactions untouched.

**Tech Stack:** WordPress/PHP templates, Tailwind-compatible bundled CSS utilities, inline SVG, Node.js, Playwright Chromium and Firefox.

## Global Constraints

- Preserve unrelated working-tree changes.
- Follow current upstream source rather than inventing WordPress-only visual rules.
- Do not add global `overflow-wrap`, `word-break`, or font-size changes: upstream does not use them for prose containment.
- Add regression coverage before modifying production files.
- Verify both browser engines against a real local WordPress render at a 390px mobile viewport.

---

### Task 1: Lock the upstream contracts in a failing regression

**Files:**

- Create: `tests/mobile-content-social-regression.mjs`
- Modify: `package.json`
- Read: `theme/mizuki-wp/single.php`
- Read: `theme/mizuki-wp/inc/customizer.php`

**Step 1: Add source-level assertions**

- Extract the `#post-container` class list and require upstream's `w-full` contract.
- Extract the `qq` icon map entry and require the official `fa7-brands:qq` `0 0 640 640` viewBox and path.
- Require `currentColor` and `1em` dimensions so the existing social-button styling remains authoritative.

**Step 2: Add computed-layout coverage**

- Build a self-contained mobile flex-card fixture from the actual class list and bundled theme CSS.
- Put a long URL and ordinary Chinese text inside the card.
- In both Chromium and Firefox at 390x844, assert that the card and ordinary paragraph stay within the outer card bounds.

**Step 3: Register and run the focused test**

Run:

```bash
node tests/mobile-content-social-regression.mjs
```

Expected: FAIL on the existing `#post-container` class and QQ SVG; the browser geometry should reproduce the oversized flex item.

### Task 2: Restore mobile single-post containment

**Files:**

- Modify: `theme/mizuki-wp/single.php:13`
- Test: `tests/mobile-content-social-regression.mjs`

**Step 1: Apply the minimal upstream markup fix**

Add `w-full` to `#post-container`, matching upstream `src/pages/posts/[...slug].astro`:

```php
<div id="post-container" class="card-base z-10 px-6 md:px-9 pt-6 pb-6 w-full">
```

**Step 2: Do not alter prose wrapping**

Keep Markdown link, table, code, KaTeX, font, and line-breaking CSS unchanged. The fixed card width restores the containing block while preserving upstream rendering.

### Task 3: Replace the QQ glyph with the upstream brand asset

**Files:**

- Modify: `theme/mizuki-wp/inc/customizer.php:362`
- Test: `tests/mobile-content-social-regression.mjs`

**Step 1: Replace only the `qq` map value**

Use the exact path returned for `fa7-brands:qq`, retaining the local icon API's `width="1em"`, `height="1em"`, and `fill="currentColor"` contract.

**Step 2: Run the focused regression**

Run:

```bash
node tests/mobile-content-social-regression.mjs
```

Expected: PASS in static SVG checks and in Chromium/Firefox geometry checks.

### Task 4: Verify the full theme and real WordPress output

**Files:**

- Verify: `theme/mizuki-wp/single.php`
- Verify: `theme/mizuki-wp/inc/customizer.php`
- Verify: `tests/mobile-content-social-regression.mjs`
- Verify: `package.json`

**Step 1: Run automated gates**

Run the focused regression, complete `npm test`, PHP lint for every theme PHP file, JavaScript syntax checks, and `git diff --check`.

**Step 2: Run real cross-browser acceptance**

- Render a control article and a long-URL article through local WordPress.
- At 390x844 in Chromium and Firefox, require `#post-container` to match the outer card width and all ordinary content bounds to remain visible.
- Render the QQ/GitHub/Bilibili social row and visually confirm the QQ logo while confirming adjacent buttons are unchanged.
- Inspect browser console and PHP logs.

**Step 3: Restore the isolated fixture**

Restore the original active-plugin option, remove temporary posts recoverably, stop temporary servers, and remove only the test junction created during this task.
