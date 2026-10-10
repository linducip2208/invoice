import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

// Automated accessibility sweep (axe-core): landmarks, labels, names,
// contrast, keyboard-relevant structure. Screen-reader pass stays manual.
const PAGES = [
  ['login', '/login', false],
  ['quote-public', '/q/e2e-quote-token-000000000001', false],
  ['docs', '/docs', false],
];

test.beforeEach(async ({ page }) => {
  const errs = [];
  page.on('pageerror', (e) => errs.push(String(e)));
  page.__errs = errs;
});

for (const [name, url, authed] of PAGES) {
  test(`axe: ${name} has no serious violations`, async ({ page }) => {
    if (authed) {
      await page.goto('/login');
      await page.locator('#email').fill('e2e@invocely.test');
      await page.locator('#password').fill('password');
      await page.locator('form button[type="submit"]').click();
      await expect(page).toHaveURL(/dashboard/);
    }
    await page.goto(url);
    const results = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa'])
      .analyze();
    const serious = results.violations.filter((v) => ['serious', 'critical'].includes(v.impact));
    for (const v of serious) {
      console.log(`AXE[${v.id}]: ${v.description} @ ${v.nodes.map((n) => n.target).join(',').slice(0, 200)}`);
    }
    expect(serious, JSON.stringify(serious.map((v) => v.id))).toEqual([]);
    expect(page.__errs).toEqual([]);
  });
}

test('axe: dashboard has no serious violations', async ({ page }) => {
  await page.goto('/login');
  await page.locator('#email').fill('e2e@invocely.test');
  await page.locator('#password').fill('password');
  await page.locator('form button[type="submit"]').click();
  await expect(page).toHaveURL(/dashboard/);
  const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa']).analyze();
  const serious = results.violations.filter((v) => ['serious', 'critical'].includes(v.impact));
  for (const v of serious) {
    console.log(`AXE[${v.id}]: ${v.description} @ ${v.nodes.map((n) => n.target).join(',').slice(0, 200)}`);
  }
  expect(serious, JSON.stringify(serious.map((v) => v.id))).toEqual([]);
});
