# INVOICE WORKFLOW E2E (browser, real server + sqlite)

Suite: `tests-e2e/app.spec.js` (7 flows × desktop/mobile) +
`tests-e2e/quote-approval.spec.js` (flow1/2/3/5/8 × desktop/mobile).
Run: reseed e2e DB (`migrate:fresh` + `E2eSeeder`), serve :8001,
`npx playwright test --workers=1` (wajib serial: paralel flaky di
SQLite/server tunggal).

| Flow | Result |
|---|---|
| Guest redirect → login | PASS (both viewports) |
| Login + dashboard KPIs, no overflow | PASS |
| Client CRUD persists across reload | PASS |
| Invoice create → total → payments page | PASS |
| Mobile nav open/close (Escape) | PASS |
| Dark mode persists across reload | PASS |
| Public pages (/, pricing, docs, blog), no overflow | PASS |
| FLOW 1 safe quote review (view + reload, no approval UI) | PASS |
| FLOW 2 portal approval → evidence → idempotent repeat | PASS |
| FLOW 3 outsider fetch lands on verify; no approve button | PASS |
| FLOW 5 conversion exactly one invoice (repeat safe) | PASS |
| FLOW 8 mobile quote readable, no overflow | PASS |
| CSRF: curl POST without origin → 419 | PASS |
| Console errors across all flows | ZERO |

Not covered by E2E (documented): OTP email round-trip (needs SMTP;
covered by automated tests with Mail fake), true concurrent double-click
(MySQL-only), FLOW 4 revision-race and FLOW 6 decline/expiry/withdrawal UI
paths (covered by automated tests c-series/e-series instead).
Screenshots: `docs/audit/shots/` (5) + Playwright traces on failure only.
