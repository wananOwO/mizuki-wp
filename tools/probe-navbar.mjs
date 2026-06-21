import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
for (const [label,url] of [['ref','http://localhost:8899/'],['wp','http://localhost:8888/']]) {
  const ctx = await b.newContext({viewport:{width:1440,height:900}});
  const p = await ctx.newPage();
  await p.goto(url, {waitUntil:'load',timeout:25000});
  await p.waitForTimeout(1000);
  // crop the navbar area
  await p.screenshot({ path: `tools/visual-out/navbar-${label}.png`, clip: {x:0,y:0,width:1440,height:90} });
  const info = await p.evaluate(() => {
    const links = [...document.querySelectorAll('#navbar-links-container a')];
    return {
      count: links.length,
      first: links[0] ? {h: links[0].clientHeight, hasSvg: !!links[0].querySelector('svg'), text: links[0].textContent.trim().slice(0,20), display: getComputedStyle(links[0]).display} : null,
    };
  });
  console.log(label, JSON.stringify(info));
  await ctx.close();
}
await b.close();
