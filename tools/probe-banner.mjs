import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
const ctx = await b.newContext({viewport:{width:1440,height:900}});
const p = await ctx.newPage();
const errs=[]; p.on('console',m=>{if(m.type()==='error')errs.push(m.text())});
await p.goto('http://localhost:8888/', {waitUntil:'load',timeout:25000});
await p.waitForTimeout(1000);
const r = await p.evaluate(() => {
  const bw = document.querySelector('#banner-wrapper');
  const bn = document.querySelector('#banner');
  const img = document.querySelector('#banner img');
  const cw = document.querySelector('.absolute.w-full.z-30');
  const cs = el => el ? getComputedStyle(el) : null;
  const bwc = cs(bw), cwc = cs(cw);
  return {
    bodyClass: document.body.className,
    bannerWrapper: bw ? {pos:bwc.position, top:bwc.top, height:bwc.height, display:bwc.display, vis:bwc.visibility, overflow:bwc.overflow, z:bwc.zIndex} : 'MISSING',
    bannerImg: img ? {src: img.src.split('/').slice(-2).join('/'), w: img.naturalWidth, h: img.naturalHeight, dispW: img.clientWidth, dispH: img.clientHeight} : 'NO IMG',
    contentWrapper: cw ? {top:cwc.top, pos:cwc.position} : 'MISSING',
  };
});
console.log(JSON.stringify(r,null,1));
console.log('JS errors:', errs.slice(0,5));
await b.close();
