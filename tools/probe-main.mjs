import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
for (const [label,url] of [['ref','http://localhost:8899/'],['wp','http://localhost:8888/']]) {
  const ctx = await b.newContext({viewport:{width:1440,height:1100}});
  const p = await ctx.newPage();
  await p.goto(url, {waitUntil:'load',timeout:25000});
  await p.waitForTimeout(1200);
  await p.evaluate(() => window.scrollTo(0, 520));
  await p.waitForTimeout(400);
  // crop just the main content column (center)
  await p.screenshot({ path: `tools/visual-out/maincol-${label}.png`, clip: {x:470, y:0, width:720, height:1100} });
  await ctx.close();
}
await b.close();
console.log('done');
