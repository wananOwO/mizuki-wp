import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
const ctx = await b.newContext({viewport:{width:1440,height:900}});
const p = await ctx.newPage();
await p.goto('http://localhost:8899/', {waitUntil:'load',timeout:25000});
await p.waitForTimeout(1200);
for (const y of [0, 100, 200, 300, 315, 400, 600]) {
  await p.evaluate(sy => window.scrollTo(0, sy), y);
  await p.waitForTimeout(250);
  const r = await p.evaluate(() => {
    const nv = document.querySelector('#navbar');
    return { scrolled: nv?.classList.contains('scrolled'), scrollY: Math.round(window.scrollY) };
  });
  console.log(`scrollTo ${y} => scrolled=${r.scrolled} (actualY=${r.scrollY})`);
}
await b.close();
