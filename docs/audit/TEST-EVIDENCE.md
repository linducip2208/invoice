# TEST EVIDENCE (2026-10-10)

## Automated (phpunit, sqlite :memory:, fakes)

73 passed / 0 failed / 234 assertions. Suite: Client 6 · Invoice 8 ·
Payment 5 · Blog 8 · Pseo 10 · Workspace 4 · Webhook 9 · EfakturXml 2 ·
Money 5 · Isolation 4 · Export 3 · Dashboard 1 · InvoiceFlow 1 · Pwa 3 ·
NavIntegrity 4.

## Browser E2E (Playwright 1.64 + Chromium, server lokal + e2e.sqlite)

14/14 passed (7 skenario × desktop 1366×768 + mobile 390×844), NOL console
error: guest redirect, login+KPI, client CRUD+reload, invoice create+total,
mobile nav, dark-mode persist, public pages. Overflow horizontal = 0 di
5 screenshot (`docs/audit/shots/`): desktop-dashboard, mobile-dashboard,
desktop-invoice-create, mobile-clients, desktop-docs.

## Gates lain

- `npm run build`: sukses. `composer validate`: valid. `php artisan route:list`: 301.
- `npm audit`: 0 vulnerabilities (setelah audit fix).
- `node --check sw.js`: OK. `php -l` pada file diubah: bersih.
- Pint: TIDAK dijalankan repo-wide (200-file churn pre-existing; didokumentasikan,
  bukan disembunyikan). CI tidak memaksakan pint agar gate tetap merekam status nyata.
- CI (`.github/workflows/ci.yml`): phpunit + vite build. E2E tidak di CI
  (butuh browser download; dijalankan lokal, bukti di sini).
