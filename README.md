# Invocely — Invoice Management Platform

Aplikasi Laravel 13 + Tabler UI untuk invoice, quotation, pembayaran,
recurring, rekonsiliasi bank, laporan, langganan, blog, dan PWA — dengan
isolasi multi-workspace.

## Fitur (yang benar-benar ada)

Invoice + PDF + email, quotation + konversi sekali, payment manual/parsial +
8 gateway Indonesia (butuh kredensial), credit note, late fee, reminder
terjadwal, recurring, e-faktur XML helper, laporan (aging/cashflow/PL/tax),
accounting export anti formula-injection, bank reconcile, OCR struk,
langganan + plan limits backend, tim + 2FA + API token + webhook keluar,
client portal publik, blog + SEO/PSEO, PWA installable, dark mode.

Status per fitur + bukti uji: `docs/audit/FEATURE-MATRIX.md`.
Skor audit: **93/100, STAGING ONLY** (`docs/audit/FINAL-AUDIT.md`).
Keterbatasan jujur: `docs/audit/KNOWN-LIMITATIONS.md`.

## Mulai cepat

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed   # demo hanya di local/dev/testing
npm install && npm run build
php artisan serve
```

Login dev (local saja): `superadmin@invocely.test / superadmin123`,
`demo@invocely.test / password`.

## Perintah penting

```bash
php artisan test                 # 144 tests, sqlite memory (513 assertions)
npx playwright test --workers=1  # 84 E2E browser serial (server + e2e.sqlite, lihat tests-e2e/)
npm run build                    # build aset produksi
php artisan schedule:run         # scheduler (cron tiap menit di prod)
php artisan queue:work           # worker antrean
php artisan db:backup            # backup (sqlite copy / mysqldump via env password)
```

## Dokumen

- `docs/TESTING.md`, `docs/DEPLOYMENT.md`, `docs/PWA.md`
- `docs/audit/`: BASELINE, IMPLEMENTATION-PLAN, FEATURE-ROUTE-MATRIX,
  UI-MIGRATION-MATRIX, SIDEBAR-ROUTE-AUDIT, DOCS-*-AUDIT/COVERAGE,
  TABLER-MIGRATION-REPORT, TEST-EVIDENCE, REMAINING-RISKS, FINAL-AUDIT,
  RELEASE-CHECKLIST
