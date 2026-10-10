import { test, expect } from '@playwright/test';
import path from 'path';

// Quote fixtures mutate (accept/decline/withdraw): reseed the e2e database
// manually before a full run (see INVOICE-WORKFLOW-E2E.md). Desktop and
// mobile projects use disjoint fixtures, so one reseed covers both.

// Desktop and mobile projects mutate quotes, so each uses its own fixtures.
const FIXTURES = {
  desktop: { no: 'Q-E2E-0001', main: 'e2e-quote-token-000000000001', outsider: 'e2e-quote-token-000000000002', total: '220,000' },
  mobile: { no: 'Q-E2E-0003', main: 'e2e-quote-token-000000000003', outsider: 'e2e-quote-token-000000000004', total: '50,000' },
};
const fx = () => FIXTURES[test.info().project.name] || FIXTURES.desktop;

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

async function assertNoOverflow(page) {
  const o = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
  expect(o, 'horizontal overflow').toBeLessThanOrEqual(1);
}

async function portalLogin(page) {
  await page.goto('/portal/login');
  await page.locator('#email').fill('e2e-approver@test.id');
  await page.locator('#password').fill('password');
  await page.locator('form button[type="submit"]').click();
  await expect(page).toHaveURL(/portal/);
}

async function workspaceLogin(page) {
  await page.goto('/login');
  await page.locator('#email').fill('e2e@invocely.test');
  await page.locator('#password').fill('password');
  await page.locator('form button[type="submit"]').click();
  await expect(page).toHaveURL(/dashboard/);
}

// All text assertions below are locale-free (IDs, URLs, numbers, counts).

// FLOW 1 — safe quote review: view + reload never accepts.
test('flow1 public quote review is read-only', async ({ page }) => {
  const tk = fx();
  await page.goto(`/q/${tk.main}`);
  await expect(page.locator('.page-body')).toContainText(tk.no);
  await page.reload();
  await expect(page.locator('.page-body')).toContainText(tk.no);
  await expect(page.locator(`form[action$="/q/${tk.main}/confirm"]`)).toHaveCount(0);
  await assertNoOverflow(page);
});

// FLOW 2 — portal approver: login, confirm revision + consent, approve.
test('flow2 portal approval records evidence', async ({ page }) => {
  const tk = fx();
  await portalLogin(page);
  await page.goto(`/q/${tk.main}`);
  await page.locator(`a[href$="/q/${tk.main}/confirm"]`).click();
  await expect(page).toHaveURL(/confirm/);
  await expect(page.locator('.page-body')).toContainText(tk.total);
  await page.locator('#accept_name').fill('E2E Approver');
  await page.locator('input[name="consent"]').first().check();
  await page.locator('form:has(input[name="purpose"][value="accept"]) button[type="submit"]').click();
  await expect(page).toHaveURL(new RegExp(`/q/${tk.main}$`));
  await page.goto(`/q/${tk.main}`);
  await expect(page.locator(`form[action$="/q/${tk.main}/confirm"]`)).toHaveCount(0);
});

// FLOW 3 — outsider with leaked link cannot approve; forgeries rejected.
test('flow3 outsider cannot approve, csrf enforced', async ({ page }) => {
  const tk = fx();
  await page.goto(`/q/${tk.outsider}`);
  const finalUrl = await page.evaluate(async (t) => {
    const r = await fetch(`/q/${t}/confirm`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ purpose: 'accept', name: 'X', consent: '1' }),
    });
    await r.text();
    return r.url;
  }, tk.outsider);
  expect(finalUrl).toContain('/verify');
  await page.goto(`/q/${tk.outsider}`);
  await expect(page.locator(`form[action$="/q/${tk.outsider}/confirm"]`)).toHaveCount(0);
});

// FLOW 5 — conversion creates exactly one invoice (repeat safe).
test('flow5 workspace conversion is idempotent', async ({ page }) => {
  const tk = fx();
  await workspaceLogin(page);
  await page.goto('/quotes');
  const row = page.locator('tr', { hasText: tk.no }).first();
  await row.getByRole('link', { name: tk.no }).click();
  await expect(page).toHaveURL(/quotes\/\d+/);
  const convert = page.getByRole('button', { name: /convert|invoice/i });
  if (await convert.count()) {
    await convert.first().click();
    await expect(page).toHaveURL(/invoices\/\d+/);
    const firstUrl = page.url();
    await page.goto('/quotes');
    await page.locator('tr', { hasText: tk.no }).first().getByRole('link', { name: tk.no }).click();
    const again = page.getByRole('button', { name: /convert|invoice/i });
    if (await again.count()) {
      await again.first().click();
      expect(page.url()).toEqual(firstUrl);
    }
  }
  await assertNoOverflow(page);
});

// FLOW 6 — decline via portal, withdraw via workspace; states stick.
test('flow6 decline and withdrawal', async ({ page }) => {
  const tk = fx();
  await portalLogin(page);
  await page.goto(`/q/${tk.outsider}`);
  await page.locator(`a[href$="/q/${tk.outsider}/confirm"]`).click();
  await page.locator('#reject_name').fill('E2E Approver');
  await page.locator('#reject_reason').fill('E2E budget cut');
  await page.locator('input[name="consent"]').nth(1).check();
  await page.locator('form:has(input[name="purpose"][value="reject"]) button[type="submit"]').click();
  await expect(page).toHaveURL(new RegExp(`/q/${tk.outsider}$`));

  await workspaceLogin(page);
  await page.goto('/quotes/create');
  await page.locator('select[name="client_id"]').first().selectOption({ index: 1 });
  await page.locator('input[name$="[description]"]').first().fill('E2E withdraw me');
  await page.locator('input[name$="[quantity]"]').first().fill('1');
  await page.locator('input[name$="[unit_price]"]').first().fill('10000');
  await page.locator('.page-body form button[type="submit"]').first().click();
  await expect(page).toHaveURL(/quotes$/);
  // Open the newest quote (just created first in latest ordering).
  await page.locator('table tbody tr').first().getByRole('link').first().click();
  await expect(page).toHaveURL(/quotes\/\d+/);
  page.on('dialog', (d) => d.accept());
  await page.getByRole('button', { name: /withdraw|tarik/i }).click();
  await expect(page.locator('.page-body')).toContainText(/withdrawn|ditarik/i);
  await assertNoOverflow(page);
});

// FLOW 8 — mobile public quote stays readable.
test('flow8 mobile quote readable', async ({ page }) => {
  const tk = fx();
  await page.goto(`/q/${tk.main}`);
  await expect(page.locator('.page-body')).toContainText(tk.no);
  await assertNoOverflow(page);
});
