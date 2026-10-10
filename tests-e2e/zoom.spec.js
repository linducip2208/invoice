import { test, expect } from '@playwright/test';

// 200% zoom approximation: CSS zoom doubles layout width, which exercises
// the same reflow/overflow paths as browser zoom. Documented as approximation.
const URLS = ['/', '/pricing', '/docs', '/login', '/q/e2e-quote-token-000000000001'];

for (const url of URLS) {
  test(`zoom200 public ${url} has no overflow`, async ({ page }) => {
    await page.goto(url);
    await page.evaluate(() => { document.body.style.zoom = '200%'; });
    await page.waitForTimeout(400);
    const o = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    expect(o, `${url} @200%`).toBeLessThanOrEqual(2);
  });
}

test('zoom200 dashboard usable', async ({ page }) => {
  await page.goto('/login');
  await page.locator('#email').fill('e2e@invocely.test');
  await page.locator('#password').fill('password');
  await page.locator('form button[type="submit"]').click();
  await expect(page).toHaveURL(/dashboard/);
  await page.evaluate(() => { document.body.style.zoom = '200%'; });
  await page.waitForTimeout(400);
  const o = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
  expect(o, 'dashboard @200%').toBeLessThanOrEqual(2);
  await expect(page.locator('.page-title').first()).toBeVisible();
});
