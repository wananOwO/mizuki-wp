import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
for (const [label,url] of [['REF','http://localhost:8899/'],['WP','http://localhost:8888/']]) {
  const out=[];
  for (const w of [1440,1280,1100,1000,800,767,500,390]) {
    const ctx = await b.newContext({viewport:{width:w,height:900}});
    const p = await ctx.newPage();
    await p.goto(url,{waitUntil:'load',timeout:25000});
    await p.waitForTimeout(700);
    const cols = await p.evaluate(()=>{const g=document.querySelector('#main-grid');const c=getComputedStyle(g).gridTemplateColumns;return c.split(' ').length;});
    out.push(`${w}:${cols}col`);
    await ctx.close();
  }
  console.log(label, out.join('  '));
}
await b.close();
