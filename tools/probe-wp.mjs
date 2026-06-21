import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
for (const [label,w] of [['desktop',1440],['wide',1920],['mobile',390]]) {
  const ctx = await b.newContext({viewport:{width:w,height:900}});
  const p = await ctx.newPage();
  await p.goto('http://localhost:8888/', {waitUntil:'load',timeout:25000});
  await p.waitForTimeout(1000);
  const r = await p.evaluate(() => {
    const g = document.querySelector('#main-grid');
    return { htmlFs: getComputedStyle(document.documentElement).fontSize,
             htmlClass: document.documentElement.className.slice(0,55),
             dark: document.documentElement.classList.contains('dark'),
             hue: getComputedStyle(document.documentElement).getPropertyValue('--hue').trim(),
             gridCols: g?getComputedStyle(g).gridTemplateColumns:null };
  });
  console.log(`${label}(${w}): ${JSON.stringify(r)}`);
  await ctx.close();
}
await b.close();
