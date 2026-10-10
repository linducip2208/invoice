import { test, expect } from '@playwright/test';

const WIDTHS = [320, 360, 375, 390, 430, 768, 1024, 1280, 1366, 1440, 1920];
const HEIGHTS = { 320: 640, 360: 800, 375: 812, 390: 844, 430: 932, 768: 1024, 1024: 768, 1280: 800, 1366: 768, 1440: 900, 1920: 1080 };
const PUBLIC_PAGES = ['/', '/pricing', '/docs', '/blog', '/login', '/q/e2e-quote-token-000000000001'];
const AUTH_PAGES = ['/dashboard', '/invoices', '/invoices/create', '/clients'];

test.beforeEach(async ({ page }) => {
  const errs = [];
  page.on('console', (m) => {
    if (m.type() === 'error' && !m.text().startsWith('Failed to load resource')) errs.push(m.text());
  });
  page.on('pageerror', (e) => errs.push(String(e)));
  page.__errs = errs;
});

test.afterEach(async ({ page }) => {
  expect(page.__errs, `console errors: ${(page.__errs || []).join(' | ')}`).toEqual([]);
});

for (const w of WIDTHS) {
  test(`public pages at ${w}px have no overflow`, async ({ page }) => {
    await page.setViewportSize({ width: w, height: HEIGHTS[w] });
    for (const url of PUBLIC_PAGES) {
      await page.goto(url);
      const o = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
      expect(o, `${url} @${w}px`).toBeLessThanOrEqual(1);
    }
  });
}

for (const w of [390, 768, 1366, 1920]) {
  test(`authed pages at ${w}px have no overflow`, async ({ page }) => {
    await page.setViewportSize({ width: w, height: HEIGHTS[w] });
    await page.goto('/login');
    await page.locator('#email').fill('e2e@invocely.test');
    await page.locator('#password').fill('password');
    await page.locator('form button[type="submit"]').click();
    await expect(page).toHaveURL(/dashboard/);
    for (const url of AUTH_PAGES) {
      await page.goto(url);
      const o = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
      expect(o, `${url} @${w}px`).toBeLessThanOrEqual(1);
    }
    // Keyboard: sidebar toggler reachable and operable via keyboard.
    const toggler = page.locator('header .navbar-toggler').first();
    if (await toggler.isVisible()) {
      await toggler.focus();
      await expect(toggler).toBeFocused();
      await page.keyboard.press('Enter');
    }
  });
}
