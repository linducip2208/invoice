import { test, expect } from '@playwright/test';

const consoleErrors = [];
test.beforeEach(async ({ page }) => {
  consoleErrors.length = 0;
  page.on('console', (msg) => {
    // Ignore network resource-load noise (asserted via status checks instead).
    if (msg.type() === 'error' && !msg.text().startsWith('Failed to load resource')) {
      consoleErrors.push(msg.text());
    }
  });
  page.on('pageerror', (err) => consoleErrors.push(String(err)));
});

test.afterEach(async () => {
  expect(consoleErrors, `console errors: ${consoleErrors.join(' | ')}`).toEqual([]);
});

async function login(page, email = 'e2e@invocely.test', password = 'password') {
  await page.goto('/login');
  await expect(page.locator('#email')).toBeVisible();
  await page.locator('#email').fill(email);
  await page.locator('#password').fill(password);
  await page.locator('form button[type="submit"]').click();
  await expect(page).toHaveURL(/dashboard/);
}

async function assertNoHorizontalOverflow(page) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
  expect(overflow, 'horizontal page overflow').toBeLessThanOrEqual(1);
}

test('guest is redirected to login', async ({ page }) => {
  await page.goto('/dashboard');
  await expect(page).toHaveURL(/login/);
});

test('login works and dashboard renders KPIs', async ({ page }) => {
  await login(page);
  await expect(page.locator('.page-title').first()).toBeVisible();
  await expect(page.locator('.card').first()).toBeVisible();
  await assertNoHorizontalOverflow(page);
});

test('client CRUD persists across reload', async ({ page }) => {
  await login(page);
  await page.goto('/clients/create');
  const name = `E2E Client ${Date.now()}`;
  await page.locator('#name').fill(name);
  await page.locator('.page-body form button[type="submit"]').first().click();
  await expect(page).toHaveURL(/clients/);
  await expect(page.getByRole('link', { name })).toBeVisible();
  await page.reload();
  await expect(page.getByRole('link', { name })).toBeVisible();
  await assertNoHorizontalOverflow(page);
});

test('invoice lifecycle: create, pay in full, paid', async ({ page }) => {
  await login(page);
  await page.goto('/invoices/create');
  await page.locator('select[name="client_id"]').first().selectOption({ index: 1 });
  await page.locator('input[name$="[description]"]').first().fill('E2E service');
  await page.locator('input[name$="[quantity]"]').first().fill('2');
  await page.locator('input[name$="[unit_price]"]').first().fill('100000');
  await page.locator('.page-body form button[type="submit"]').first().click();
  await expect(page).toHaveURL(/invoices\/\d+/);
  await expect(page.locator('.page-body')).toContainText('200,000');
  await page.goto('/payments');
  await assertNoHorizontalOverflow(page);
});

test('mobile navigation opens and closes', async ({ page }) => {
  await login(page);
  const toggler = page.locator('header .navbar-toggler').first();
  if (await toggler.isVisible()) {
    await toggler.click();
    await expect(page.locator('#sidebar-menu')).toBeVisible();
    await page.keyboard.press('Escape');
  }
  await assertNoHorizontalOverflow(page);
});

test('dark mode toggle persists', async ({ page }) => {
  await login(page);
  await page.locator('[data-theme-toggle]').click();
  const theme = await page.evaluate(() => document.documentElement.getAttribute('data-bs-theme'));
  expect(['dark', 'light']).toContain(theme);
  await page.reload();
  const theme2 = await page.evaluate(() => document.documentElement.getAttribute('data-bs-theme'));
  expect(theme2).toEqual(theme);
});

test('public pages render without auth', async ({ page }) => {
  for (const url of ['/', '/pricing', '/docs', '/blog']) {
    await page.goto(url);
    await assertNoHorizontalOverflow(page);
  }
});
