const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const d = await (await b.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  await d.goto('http://127.0.0.1:8001/pricing', { waitUntil: 'networkidle', timeout: 30000 });
  await d.waitForTimeout(800);
  await d.screenshot({ path: 'docs/ui-redesign/shots/pricing-nav.png' });
  const links = await d.evaluate(() => [...document.querySelectorAll('header nav a, header ul a')].map(a => a.textContent.trim()).filter(Boolean));
  console.log('DESKTOP nav:', JSON.stringify(links));
  const m = await (await b.newContext({ viewport: { width: 390, height: 844 } })).newPage();
  await m.goto('http://127.0.0.1:8001/pricing', { waitUntil: 'networkidle', timeout: 30000 });
  await m.waitForTimeout(800);
  const vis = await m.evaluate(() => {
    const btn = document.querySelector('header .navbar-toggler');
    return btn ? { found: true, visible: getComputedStyle(btn).display !== 'none' && btn.offsetParent !== null } : { found: false };
  });
  console.log('MOBILE toggler:', JSON.stringify(vis));
  await m.screenshot({ path: 'docs/ui-redesign/shots/pricing-mobile.png' });
  await b.close();
})().catch(e => { console.error(e); process.exit(1); });
