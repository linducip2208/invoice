# CHANGELOG

## 2026-10-10 (malam 4) — Premium redesign (93/100 tetap)

### Redesign

- Landing rewrite total: hero + visual produk besar, trust strip jujur,
  editorial alternating per segmen, lifecycle, pricing dari DB, FAQ —
  rating 4.9/1247 customer/testimoni dibuang, JSON-LD dibersihkan
- Sidebar navy + IA 8 grup; shell superadmin terpisah; breadcrumb 3 halaman;
  topbar quick-create + bell; tokens + kontras AA
- Dashboard: donat status + bar aging SVG data real; invoice detail timeline
- Import hardening, Ops/AdminShell tests, email queued, named limiters
- LandingController (plans real);SMTP/queue tetap butuh prod
- 138/138 phpunit, 64/64 Playwright (11 viewport + axe 9/9), build green

## 2026-10-10 (malam 3) — Frontend rescue & redesign (93/100 tetap)

### Redesign

- Sidebar IA 8 grup + topbar quick-create + bell unread; shell superadmin
  terpisah (PLATFORM/REVENUE/OPERATIONS); hapus section ganda
- Landing rewrite: hero jujur, preview ilustratif berlabel, 6 kapabilitas
  terverifikasi, workflow, pricing pointer, trust terverifikasi, FAQ, CTA —
  rating/customer-count/testimoni palsu dibuang + JSON-LD dibersihkan
- Dashboard: donat status + bar aging (SVG data real), copy profesional
- Invoice detail: rotate-link + timeline audit; helper `format_money()`
- A11y: badge/alert/footer/primary kontras AA terverifikasi axe 9/9
  (badge -lt override, primary #6d4ae0, prose underline); branding default
  diperbaiki (factory + helper)
- i18n batch; ImportPlan/Import hardening (baris-gagal terisolasi, email
  tervalidasi); OpsTest (scheduler/mail/PDF); AdminShellTest; email_logs
  status 'queued' (migrasi + rollback teruji); named rate limiters
- 138/138 phpunit, 64/64 Playwright (4 suite), 11 viewport overflow 0

## 2026-10-10 (malam 2) — Secure quote approval + financial integrity (93/100)

### Keamanan approval (P0)

- Endpoint instant accept/reject/sign DIHAPUS; diganti alur verified:
  review read-only, OTP terikat (quote+revisi+recipient+purpose), halaman
  consent eksplisit, mode portal approver, kebijakan high-risk
- State machine withdrawn/revisi; evidence append-only + hash dokumen
  kanonis + snapshot consent; konversi idempoten per (quote, revisi)
- Tantangan hash-only (tidak ada OTP/token plaintext); throttle per-route+IP;
  no-store/no-referrer; rotasi token; revokasi portal immediat; CSRF terbukti
- 50/52 matrix hijau + E2E FLOW 1/2/3/5/6/8 dua viewport, nol console error

### Finansial

- Invarian teruji (total dari lines, balance, credit apply/cancel, late fee
  sekali + reverse); late-fee TOCTOU dikunci; konversi pajak per-line
- Tombol remind di aging; rotate-link invoice + quote

### Dokumen

- Threat model, state machine, test matrix, evidence, invariants,
  payment gates, E2E workflow; FINAL-AUDIT 93/100 STAGING ONLY

## 2026-10-10 (malam) — Finalisasi enterprise: Tabler 100%, E2E, skor 88/100

### Migrasi UI

- Tabler `@tabler/core@1.6.1` + icons 3.49.0 via Vite; Tailwind + unicorn
  + 18 komponen lama + 4 layout lama + stub views dihapus (grep-zero)
- App shell + sidebar IA 9 section/44 item (role-gated, active state,
  offcanvas mobile); reusable `x-tabler.*` + 74 ikon SVG lokal
- 132/167 views MIGRATED (sisa EXEMPT: print/PDF/email/XML/partial)
- Auth (6, termasuk fix `route('home')` hilang), errors (6 + 429),
  admin (18), publik/blog/portal (23), /docs rewrite 15 section + search
- Dark mode persisten; system font stack (opsi font mati dihapus)
- i18n: ID +130 kunci, EN lengkap, duplikat dibuang

### Keamanan & data

- WebhookSignatureVerifier (midtrans/xendit/tripay/duitku/doku) + 4 test;
  throttle register/reset; login lockout tetap di LoginRequest
- Export CSV anti formula-injection + test; backup password via env +
  BackupRestoreTest hijau; `bootstrap/cache` writable diperbaiki

### Pengujian

- 74 automated hijau (Isolation, Money, Export, Dashboard KPI, InvoiceFlow,
  Pwa, NavIntegrity, BackupRestore, signature) + **14/14 Playwright nyata**
  (desktop+mobile, nol console error, overflow 0, 5 screenshot)
- `npm audit` 0 vuln; E2E suite + seeder terisolasi dikomit

### Dokumen

- BASELINE, IMPLEMENTATION-PLAN, FEATURE-ROUTE-MATRIX, UI-MIGRATION-MATRIX,
  SIDEBAR-ROUTE-AUDIT, DOCS-ACCURACY/COVERAGE, TABLER-MIGRATION-REPORT,
  TEST-EVIDENCE, REMAINING-RISKS; FINAL-AUDIT 88/100 STAGING ONLY

## 2026-10-10 — Enterprise hardening (audit misi 100/100)

### Fixed (P0/P1)

- `workspace()` tidak lagi menonaktifkan tenant-scope di console → isolasi
  workspace aktif di tests/queue; 14 test gagal → hijau
- Route `workspace.create/store` + view + controller (baru tanpa workspace
  tadinya 500); 3 test WorkspaceTest
- Blog admin kini superadmin-only (+ sidebar gated, regression test 403)
- Komponen `x-layouts.seo` (blog publik 500)
- Guard 403 eksplisit Client/Invoice/Payment/Quote/CreditNote; validasi
  client_id/product_id scoped per workspace
- Numbering race-safe: invoice `lockForUpdate`, quote per-workspace + retry,
  credit-note retry; convert quote anti double (row lock + 409)
- `invoice_layout_id` kini tersimpan; `bank_reconcile_line_id` + referensi
  finalize rekonsiliasi; opt-out reminder per client (form + validasi)
- Webhook payment/billing: transaksi + row lock + replay guard +
  stored-amount-wins; Xendit/Doku fail-closed saat provider menolak
- E-faktur: escaping terverifikasi empiris, filename sanitized, status
  approved tidak regresi (2 unit tests)
- Demo credentials disembunyikan di luar local (login + docs); seeder demo
  tidak jalan di production tanpa `SEED_DEMO=true`
- Dashboard widgets reorder shadow; migrasi down `last_invoice_id`;
  email invoice responsif; branded error pages 403/404/419/500/503

### Added

- PWA: ikon 192/512/maskable, manifest diperbaiki, SW v2 network-only `/api/`
- CI GitHub Actions (phpunit + vite build)
- Docs: FEATURE-MATRIX, KNOWN-LIMITATIONS, FINAL-AUDIT (75/100),
  RELEASE-CHECKLIST, TESTING, DEPLOYMENT, PWA; README produk

### Tests

48 passed / 0 failed (sebelumnya 37 dengan 14 gagal). E2E browser + gateway
sandbox tetap BLOCKED (butuh kredensial/perangkat).
