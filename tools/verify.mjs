/**
 * 自动化验证: 卡片可见性 + "+"菜单按钮 + 主题色面板。
 */
import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: true });
const ctx = await browser.newContext();
const page = await ctx.newPage();
await page.setViewportSize({ width: 1440, height: 900 });
await page.goto('http://localhost:8888/', { waitUntil: 'networkidle', timeout: 20000 });

// 1) 卡片背景是否不透明
const cardInfo = await page.evaluate(() => {
  const el = document.querySelector('.post-card-item.card-base');
  if (!el) return { found: false };
  const cs = getComputedStyle(el);
  return { found: true, bg: cs.backgroundColor, backdrop: cs.backdropFilter };
});
console.log('CARD:', JSON.stringify(cardInfo));

// 2) 桌面端主题色面板(齿轮)切换
const gearToggle = await page.evaluate(() => {
  const btn = document.getElementById('display-settings-switch');
  const panel = document.getElementById('display-setting');
  if (!btn || !panel) return { ok: false, reason: 'missing' };
  const before = panel.classList.contains('float-panel-closed');
  btn.click();
  const afterOpen = panel.classList.contains('float-panel-closed');
  const vis = getComputedStyle(panel).visibility;
  return { ok: true, before, afterOpen, visibility: vis };
});
console.log('GEAR PANEL:', JSON.stringify(gearToggle));

// 3) 移动端 "+" 菜单按钮
await page.setViewportSize({ width: 390, height: 844 });
await page.reload({ waitUntil: 'networkidle' });
const plusBtn = await page.evaluate(() => {
  const btn = document.getElementById('nav-menu-switch');
  const panel = document.getElementById('nav-menu-panel');
  if (!btn || !panel) return { ok: false, reason: 'missing' };
  const before = panel.classList.contains('float-panel-closed');
  btn.click();
  const afterOpen = panel.classList.contains('float-panel-closed');
  const cs = getComputedStyle(panel);
  const links = panel.querySelectorAll('a').length;
  return { ok: true, closedBefore: before, closedAfter: afterOpen, visibility: cs.visibility, opacity: cs.opacity, links };
});
console.log('PLUS MENU:', JSON.stringify(plusBtn));

await page.setViewportSize({ width: 1440, height: 900 });
await page.goto('http://localhost:8888/', { waitUntil: 'networkidle' });
await page.screenshot({ path: 'tools/visual-out/wp-home-verify.png', fullPage: false });
console.log('screenshot: tools/visual-out/wp-home-verify.png');

await browser.close();
