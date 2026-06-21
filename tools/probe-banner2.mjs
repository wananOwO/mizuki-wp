import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
for (const [label,url] of [['REF','http://localhost:8899/'],['WP','http://localhost:8888/']]) {
  const ctx = await b.newContext({viewport:{width:1440,height:900}});
  const p = await ctx.newPage();
  await p.goto(url, {waitUntil:'load',timeout:25000});
  await p.waitForTimeout(1200);
  const r = await p.evaluate(() => {
    const cs = el => el ? getComputedStyle(el) : null;
    const bw = document.querySelector('#banner-wrapper');
    const car = document.querySelector('#banner-carousel');
    const cw = document.querySelector('.absolute.w-full.z-30');
    const c = cs(bw), cc = cs(car), w = cs(cw);
    return {
      body: document.body.className.replace('wp-embed-responsive','').trim(),
      bw: c?{pos:c.position,top:c.top,height:c.height}:'X',
      carousel: cc?{height:cc.height}:'X',
      content: w?{top:w.top}:'X',
    };
  });
  console.log(label, JSON.stringify(r));
  await ctx.close();
}
await b.close();
