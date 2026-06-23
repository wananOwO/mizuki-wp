/**
 * 验证文章页右侧 TOC 不再被吸顶导航栏遮挡。
 *
 * 复现用户场景:#navbar-wrapper 始终 sticky top-0(高 72px),滚动后吸顶;
 * 修复前 TOC 内联 top-14(56px)→ 顶部 16px 藏在导航栏后("顶到 nav 下面")。
 * 修复后 TOC 应 top:5.5rem(88px)→ 落在导航栏下方。
 *
 * 判定:滚动 400px 让导航栏吸顶后,#toc-inner-wrapper 的 rect.top >= #navbar 的 rect.bottom。
 * 用法:WP_BASE=http://localhost:8888 node tools/verify-toc.mjs
 */
import { chromium } from 'playwright';

const BASE = process.env.WP_BASE || 'http://localhost:8888';

const browser = await chromium.launch({ headless: true });
const ctx = await browser.newContext({ viewport: { width: 1600, height: 1000 } });
const page = await ctx.newPage();

const goto = (url) => page.goto(url, { waitUntil: 'networkidle', timeout: 20000 }).catch((e) => console.warn('  goto warn:', e.message.split('\n')[0]));

// 1) 从首页找一篇带标题的文章链接
await goto(BASE + '/');
const postLink = await page.evaluate(() => {
  const links = [...document.querySelectorAll('a')]
    .map((a) => a.href)
    .filter((h) => h.startsWith(location.origin) && /(\?p=\d+|\/archives\/|\/\d{4}\/|\.html$)/.test(h));
  return links[0] || null;
});
console.log('article link:', postLink);
if (!postLink) {
  console.error('未找到文章链接,无法验证');
  await browser.close();
  process.exit(2);
}

// 2) 进入文章页,滚动让吸顶导航栏就位
await goto(postLink);
await page.waitForTimeout(1000);
await page.evaluate(() => window.scrollBy(0, 400));
await page.waitForTimeout(400);

// 3) 测量
const m = await page.evaluate(() => {
  const nav = document.querySelector('#navbar');
  const navWrap = document.querySelector('#navbar-wrapper');
  const toc = document.querySelector('#toc-inner-wrapper');
  const rect = (el) => (el ? el.getBoundingClientRect().toJSON() : null);
  const navR = rect(nav);
  const tocR = rect(toc);
  const tocCS = toc ? getComputedStyle(toc) : null;
  return {
    bodyClass: document.body.className,
    hasNoBannerMode: document.body.classList.contains('no-banner-mode'),
    hasEnableBanner: document.body.classList.contains('enable-banner'),
    navWrapPosition: navWrap ? getComputedStyle(navWrap).position : null,
    navBottom: navR ? Math.round(navR.bottom) : null,
    navHeight: navR ? Math.round(navR.height) : null,
    tocTopRect: tocR ? Math.round(tocR.top) : null,
    tocTopComputed: tocCS ? tocCS.top : null,
    tocVisible: !!(tocCS && tocCS.display !== 'none' && tocR && tocR.width > 0),
  };
});
console.log('MEASUREMENTS:', JSON.stringify(m, null, 2));

// 4) 判定
const ok = m.tocVisible && m.tocTopRect != null && m.navBottom != null && m.tocTopRect >= m.navBottom - 1;
console.log('VERDICT:', ok ? 'PASS — TOC 落在导航栏下方' : 'FAIL — TOC 仍被导航栏遮挡');

await page.screenshot({ path: 'tools/visual-out/toc-fix-after.png' });
console.log('screenshot → tools/visual-out/toc-fix-after.png');

await browser.close();
process.exit(ok ? 0 : 1);
