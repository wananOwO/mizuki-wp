import { chromium } from 'playwright';

const baseUrl = process.argv[2] || 'http://127.0.0.1:8888';
const credentials = {
	username: 'mizuki_admin',
	password: 'Mizuki-Local-Test-2026!',
};

const browser = await chromium.launch({ headless: true });
const context = await browser.newContext();
const page = await context.newPage();

await page.goto(`${baseUrl}/wp-admin/install.php`, {
	waitUntil: 'domcontentloaded',
	timeout: 30_000,
});

if (await page.locator('select[name="language"]').count()) {
	await page.selectOption('select[name="language"]', 'zh_CN');
	await page.locator('input[type="submit"]').click();
	await page.waitForLoadState('domcontentloaded');
}

if (await page.locator('#weblog_title').count()) {
	await page.fill('#weblog_title', 'Mizuki Native Test');
	await page.fill('#user_login', credentials.username);
	await page.fill('input[name="admin_password"]', credentials.password);
	await page.fill('#admin_email', 'mizuki@example.test');
	const discourage = page.locator('#blog_public');
	if (await discourage.count()) await discourage.uncheck();
	await page.locator('#submit').click();
	await page.waitForLoadState('domcontentloaded');
}

const result = {
	url: page.url(),
	title: await page.title(),
	body: (await page.locator('body').innerText()).replace(/\s+/g, ' ').slice(0, 500),
	credentials,
};

console.log(JSON.stringify(result, null, 2));

await browser.close();
