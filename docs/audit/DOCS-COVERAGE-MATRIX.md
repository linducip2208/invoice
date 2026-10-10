# DOCS COVERAGE MATRIX — misi §4.1 (15 artikel /docs)

| # | Artikel | Cakupan | Status |
|---|---|---|---|
| 1 | Getting Started | definisi, syarat workspace, setup bisnis, checklist | PASS |
| 2 | Dashboard | KPI, definisi metrik, empty state | PASS |
| 3 | Clients & Products | CRUD, validasi, search, CSV + baris gagal | PASS |
| 4 | Invoices | create, item/diskon/pajak/due, preview/PDF/duplicate/edit/status, share/email/attachment | PASS |
| 5 | Quotations | create, accept/reject/sign, konversi | PASS |
| 6 | Payments | manual/parsial/full, 8 gateway, webhook, credit-note | PASS |
| 7 | Recurring & Reminders | jadwal, scheduler/queue, opt-out | PASS |
| 8 | Finance & Reports | aging/cashflow/PL/tax, reconcile, export | PASS |
| 9 | Team & Security | role/workspace/invite, 2FA, token, audit | PASS |
| 10 | Billing & Plans | limit, upgrade/cancel/resume, manual | PASS |
| 11 | Integrations | SMTP, gateway, AI, WA, OAuth, webhook (tanpa endpoint karangan) | PASS |
| 12 | PWA & Mobile | install, offline limits, logout safety | PASS |
| 13 | Troubleshooting | login/mail/queue/scheduler/PDF/permission/migrasi/storage/cache/deploy + tabel sebab→fix | PASS |
| 14 | Developer/API | endpoint nyata, auth, pagination, error, webhook events/signature/retry | PASS |
| 15 | Release notes & status | Active/Partial/Blocked/Coming Soon + tanggal | PASS |

UX: search client-side semua artikel, sidebar collapsible (offcanvas mobile),
breadcrumbs implisit (nav), anchor stabil, empty-search jelas, keyboard
accessible (input + link + heading), SEO title/description/canonical,
ID+EN fallback via __(). Tanpa JS pun artikel tetap terbuka (anchor HTML biasa).
