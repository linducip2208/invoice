# TEST EVIDENCE (2026-10-10, revisi premium-redesign)

## Automated (phpunit, sqlite :memory:, fakes)

138 passed / 0 failed / 451 assertions (lama 128 + ImportPlan 5 +
Ops 3 + AdminShell 2).

## Browser E2E (Playwright 1.64 + Chromium, server lokal + e2e.sqlite)

Semua hijau, NOL console error, **64/64 passed**:
- app.spec (14): guest redirect, login+KPI, client CRUD+reload, invoice
  create+total, mobile nav, dark-mode persist, public pages.
- quote-approval.spec (12): FLOW 1 review read-only, FLOW 2 portal approval
  + idempoten, FLOW 3 outsider/CSRF, FLOW 5 konversi idempoten, FLOW 6
  decline+withdraw, FLOW 8 mobile.
- responsive.spec (30): 11 viewport halaman publik + 4 halaman authed + keyboard.
- a11y.spec (8): axe WCAG 2A/2AA serius-nol di login/quote/docs/dashboard.
Overflow 0 + screenshot di `docs/audit/shots/`.

## Gates lain

- `npm run build`: sukses. `composer validate`: valid.
- `php artisan route:list`: 310.
- `npm audit`: 0 vulnerabilities.
- `node --check sw.js`: OK. Migrasi quote: up/down/up bersih di DB sekali pakai.
- CSRF: curl POST tanpa origin → 419; browser same-origin mengikuti rantai
  redirect aman (Laravel 13 Sec-Fetch-Site, terdokumentasi di threat model).
- Pint repo-wide: ditunda eksplisit (200-file churn pre-existing).
- CI: phpunit + vite build. E2E di mesin lokal (butuh browser + server),
  bukti di sini + INVOICE-WORKFLOW-E2E.md.
