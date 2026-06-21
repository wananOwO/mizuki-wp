import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
for (const w of [1920, 1440, 1000, 800, 767, 500, 390]) {
  const ctx = await b.newContext({viewport:{width:w,height:900}, deviceScaleFactor:1});
  const p = await ctx.newPage();
  await p.goto('http://localhost:8899/', {waitUntil:'load',timeout:25000});
  await p.waitForTimeout(1500);
  const r = await p.evaluate(() => ({
    fs: getComputedStyle(document.documentElement).fontSize,
    mqMd: window.matchMedia('(min-width: 768px)').matches,
    bodyFs: getComputedStyle(document.body).fontSize,
  }));
  console.log(`w=${w}  htmlFontSize=${r.fs}  mq(>=768)=${r.mqMd}  bodyFs=${r.bodyFs}`);
  await ctx.close();
}
await b.close();
