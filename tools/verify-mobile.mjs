import { chromium } from 'playwright';
const b = await chromium.launch({ headless: true });
const c = await b.newContext();
const p = await c.newPage();
await p.setViewportSize({ width: 390, height: 844 });
await p.goto('http://localhost:8888/', { waitUntil: 'networkidle', timeout: 20000 });
const home = await p.evaluate(() => {
  const cont = document.getElementById('post-list-container');
  const card = document.querySelector('.post-card-item.card-base');
  return {
    containerOpacity: cont ? getComputedStyle(cont).opacity : 'none',
    hasJsInit: cont ? cont.classList.contains('js-initialized') : false,
    cardBg: card ? getComputedStyle(card).backgroundColor : 'none',
    cardVisible: card ? (card.offsetWidth>0 && getComputedStyle(card).opacity!=='0') : false,
  };
});
console.log('HOME MOBILE:', JSON.stringify(home));
// + menu
const plus = await p.evaluate(() => {
  const btn = document.getElementById('nav-menu-switch');
  const panel = document.getElementById('nav-menu-panel');
  btn.click();
  return { closed: panel.classList.contains('float-panel-closed'), links: panel.querySelectorAll('a').length,
           labels: Array.from(panel.querySelectorAll('a')).map(a=>a.textContent.trim()) };
});
console.log('PLUS MENU MOBILE:', JSON.stringify(plus));
await p.screenshot({ path: 'tools/visual-out/wp-home-mobile.png', fullPage: false });
await b.close();
