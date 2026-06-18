/**
 * 视觉对比工具:Mizuki 原版(:4321) vs WP 主题(:8888) 截图对比。
 * 输出到 tools/visual-out/ 目录,供人工逐页核对视觉一致性。
 */
import { chromium } from 'playwright';
import fs from 'node:fs';

const PAGES = [
  { name: 'home', ref: '/', wp: '/' },
  { name: 'archive', ref: '/archive/', wp: '/?cat=1' },
];

const OUT = 'tools/visual-out';
fs.mkdirSync(OUT, { recursive: true });

const shot = async (ctx, url, file) => {
  const page = await ctx.newPage();
  await page.setViewportSize({ width: 1440, height: 900 });
  try {
    await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
  } catch (e) {
    console.warn(`  warning: ${url} — ${e.message.split('\n')[0]}`);
  }
  await page.screenshot({ path: file, fullPage: true });
  await page.close();
};

const browser = await chromium.launch({ headless: true });
const ctx = await browser.newContext();

for (const p of PAGES) {
  console.log(`capturing ${p.name}...`);
  await shot(ctx, `http://localhost:4321${p.ref}`, `${OUT}/${p.name}-ref.png`);
  await shot(ctx, `http://localhost:8888${p.wp}`, `${OUT}/${p.name}-wp.png`);
  console.log(`  → ${OUT}/${p.name}-ref.png vs ${OUT}/${p.name}-wp.png`);
}

await browser.close();
console.log('\n对比图已生成。请逐页打开 *-ref.png 和 *-wp.png 并排核对视觉一致性。');
