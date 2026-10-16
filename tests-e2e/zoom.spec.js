import { test, expect } from '@playwright/test';

// 200% zoom methodology: real browser zoom halves the layout viewport, so
// testing at half-width viewports IS the honest equivalent. (The CSS `zoom`
// property was tried first and REJECTED: it scales `vw` units up instead of
// shrinking the layout viewport, which no real zoom does.)
// 195 = 200% of 390px phone, 180 = 200% of 360px, 512 = 200% of 1024px,
// 683 = 200% of 1366px laptop.
const URLS = ['/', '/pricing', '/docs', '/login', '/q/e2e-quote-token-000000000001'];

for (const w of [195, 180, 512, 683]) {
  test(`zoom200 public pages at ${w}px layout width`, async ({ page }) => {
    await page.setViewportSize({ width: w, height: 800 });
    for (const url of URLS) {
      await page.goto(url, { waitUntil: 'networkidle' });
      await page.waitForTimeout(800);
      const o = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
      expect(o, `${url} @${w}px`).toBeLessThanOrEqual(2);
    }
  });
}

test('zoom200 dashboard usable', async ({ page }) => {
  await page.setViewportSize({ width: 683, height: 800 });
  await page.goto('/login');
  await page.locator('#email').fill('e2e@invocely.test');
  await page.locator('#password').fill('password');
  await page.locator('form button[type="submit"]').click();
  await expect(page).toHaveURL(/dashboard/);
  const o = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
  expect(o, 'dashboard @683px').toBeLessThanOrEqual(2);
  await expect(page.locator('.page-title').first()).toBeVisible();
});
