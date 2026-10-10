# Invocely — Invoice Management Platform

Aplikasi Laravel 13 untuk invoice, quotation, pembayaran, recurring,
rekonsiliasi bank, laporan, langganan, blog, dan PWA — dengan isolasi
multi-workspace.

## Fitur (yang benar-benar ada)

Invoice + PDF + email, quotation + konversi, payment manual/parsial +
7 gateway Indonesia, credit note, late fee, reminder terjadwal, recurring,
e-faktur XML, laporan (aging/cashflow/PL/tax), accounting export, bank
reconcile, OCR struk, langganan + plan limits, tim + 2FA + API token +
webhook keluar, client portal publik, blog + SEO/PSEO, PWA installable.

Status per fitur + bukti uji: `docs/audit/FEATURE-MATRIX.md`.
Keterbatasan jujur: `docs/audit/KNOWN-LIMITATIONS.md`.

## Mulai cepat

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed   # demo hanya di local/dev/testing
npm install --ignore-scripts && npm run build
php artisan serve
```

Login dev (local saja): `superadmin@invocely.test / superadmin123`,
`demo@invocely.test / password`.

## Perintah penting

```bash
php artisan test                 # 48 tests, sqlite memory
npm run build                    # build aset produksi
php artisan schedule:run         # scheduler (cron tiap menit di prod)
php artisan queue:work           # worker antrean
```

## Dokumen

- `docs/TESTING.md`, `docs/DEPLOYMENT.md`, `docs/PWA.md`
- `docs/audit/FINAL-AUDIT.md` (skor 75/100, NOT READY publik)
- `docs/audit/RELEASE-CHECKLIST.md` (syarat naik READY)
