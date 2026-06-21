import { chromium } from 'playwright';
const b = await chromium.launch({headless:true});
const ctx = await b.newContext({viewport:{width:1440,height:900}});
const p = await ctx.newPage();
await p.goto('http://localhost:8899/', {waitUntil:'load',timeout:25000});
await p.waitForTimeout(800);
const data = await p.evaluate(() => {
  const el = document.documentElement;
  const out = [];
  for (const sheet of document.styleSheets) {
    let rr; try { rr = sheet.cssRules; } catch(e){ continue; }
    const walk = (rules, media) => {
      for (const rule of rules) {
        if (rule.type === CSSRule.MEDIA_RULE) walk(rule.cssRules, (media?media+' && ':'')+rule.conditionText);
        else if (rule.selectorText && rule.style && rule.style.fontSize) {
          let m=false; try{ m = el.matches(rule.selectorText); }catch(e){}
          if (m) out.push({sel: rule.selectorText, media, fs: rule.style.fontSize, mq: media?window.matchMedia(rule.parentRule?.conditionText||'all').matches:true});
        }
      }
    };
    walk(rr, '');
  }
  return {classes: el.className, inlineFs: el.style.fontSize, computed: getComputedStyle(el).fontSize, rules: out};
});
console.log(JSON.stringify(data, null, 1));
await b.close();
