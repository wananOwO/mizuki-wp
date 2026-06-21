import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
for (const [label,url] of [['ref','http://localhost:8899/'],['wp','http://localhost:8888/']]) {
  const ctx = await b.newContext({viewport:{width:1440,height:1000}});
  const p = await ctx.newPage();
  await p.goto(url, {waitUntil:'load',timeout:25000});
  await p.waitForTimeout(1200);
  // scroll down a bit so sidebars (below banner) are in view
  await p.evaluate(() => window.scrollTo(0, 560));
  await p.waitForTimeout(400);
  await p.screenshot({ path: `tools/visual-out/sidebars-${label}.png` });
  await ctx.close();
}
await b.close();
console.log('done');
