# TEST EVIDENCE (2026-10-16, ronde 4)

## Automated (phpunit, sqlite :memory:, fakes)

144 passed / 0 failed / 513 assertions.

## Browser E2E (Playwright 1.64 + Chromium, server lokal + e2e.sqlite)

Semua hijau, NOL console error, **84/84 passed serial (`--workers=1`)**:
- app.spec (14): guest redirect, login+KPI, client CRUD+reload, invoice
  create+total, mobile nav, dark-mode persist, public pages.
- quote-approval.spec (12): FLOW 1 review read-only, FLOW 2 portal approval
  + idempoten, FLOW 3 outsider/CSRF, FLOW 5 konversi idempoten, FLOW 6
  decline+withdraw, FLOW 8 mobile.
- responsive.spec (30): 11 viewport halaman publik + 4 halaman authed + keyboard.
- a11y.spec (18): axe WCAG 2A/2AA serius-nol di 9 halaman × 2 viewport.
- zoom.spec (10): setengah-viewport (180/195/512/683) + dashboard usable.
Overflow 0.

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
