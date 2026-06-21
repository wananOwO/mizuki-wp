/**
 * Side-by-side capture: reference Mizuki dist (8899) vs WordPress port (8888).
 * Captures full-page screenshots at desktop (1440) and mobile (390) for each
 * page pair, plus a few computed-layout probes for diagnosis.
 *
 * Usage: node tools/capture-compare.mjs [pageKey]
 */
import { chromium } from 'playwright';
import fs from 'node:fs';

const REF = 'http://localhost:8899';
const WP = 'http://localhost:8888';
const OUT = 'tools/visual-out';
fs.mkdirSync(OUT, { recursive: true });

// page key -> [ref path, wp path]
const PAGES = {
	home:     ['/',              '/'],
	post:     ['/posts/guide/',  '/hello-world/'],
	friends:  ['/friends/',      '/friends/'],
	anime:    ['/anime/',        '/anime/'],
	diary:    ['/diary/',        '/diary/'],
	timeline: ['/timeline/',     '/timeline/'],
	projects: ['/projects/',     '/projects/'],
	skills:   ['/skills/',       '/skills/'],
	about:    ['/about/',        '/about/'],
};

const VIEWPORTS = { desktop: { width: 1440, height: 900 }, mobile: { width: 390, height: 844 } };

const only = process.argv[2];
const keys = only ? [only] : Object.keys(PAGES);

const browser = await chromium.launch({ headless: true });

async function shoot(url, file, vp) {
	const ctx = await browser.newContext({ viewport: vp, deviceScaleFactor: 1 });
	const page = await ctx.newPage();
	let probe = {};
	try {
		await page.goto(url, { waitUntil: 'load', timeout: 25000 });
		await page.waitForTimeout(1200);
		probe = await page.evaluate(() => {
			const grid = document.querySelector('#main-grid');
			const gcs = grid ? getComputedStyle(grid) : null;
			const card = document.querySelector('.post-card-item, .card-base');
			const ccs = card ? getComputedStyle(card) : null;
			return {
				gridCols: gcs ? gcs.gridTemplateColumns : null,
				gridGap: gcs ? gcs.gap : null,
				cardBg: ccs ? ccs.backgroundColor : null,
				cardRadius: ccs ? ccs.borderRadius : null,
				bodyBg: getComputedStyle(document.body).backgroundColor,
			};
		});
		await page.screenshot({ path: `${OUT}/${file}`, fullPage: true });
	} catch (e) {
		probe = { error: String(e).split('\n')[0] };
	}
	await ctx.close();
	return probe;
}

for (const key of keys) {
	const [refPath, wpPath] = PAGES[key];
	for (const [vpName, vp] of Object.entries(VIEWPORTS)) {
		const r = await shoot(REF + refPath, `${key}-ref-${vpName}.png`, vp);
		const w = await shoot(WP + wpPath, `${key}-wp-${vpName}.png`, vp);
		console.log(`\n[${key} / ${vpName}]`);
		console.log('  REF', JSON.stringify(r));
		console.log('  WP ', JSON.stringify(w));
	}
}

await browser.close();
console.log('\nDone ->', OUT);
