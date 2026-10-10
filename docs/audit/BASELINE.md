# BASELINE — finalization mission (2026-10-10)

## Git

- Branch: `main`, HEAD `b0867b6`, sinkron dengan `origin/main`, tree bersih.
- History: `361d9de` initial → `395a475` P0/P1 → `334c054` numbering/webhook/PWA → `b0867b6` gateway/e-faktur/docs/CI.

## Versi

PHP 8.3.30 · Composer 2.8.4 · Node v24.15.0 · npm 11.13.0 · Laravel 13.8.0 ·
DB: sqlite (test) / mysql (prod) · Frontend: Tailwind 3.1 + Alpine 3.4 + Vite 8 (pra-Tabler).

## Inventaris

- Routes: **301** (88 controller groups) → `FEATURE-ROUTE-MATRIX.md`
- Views: **191** `.blade.php`; 149 pakai class unicorn; 94 pakai `x-app-layout`; 7 `@extends`
- Models: 60 (45 pakai `BelongsToWorkspace`) · Migrations: 25 · Policies: 0 (middleware + guards)
- Layout aktif: `layouts/app` (unicorn+Tailwind), `layouts/guest`, `layouts/seo`, komponen `layouts.seo`
- PWA: manifest + ikon 192/512/maskable valid, SW v2 network-only `/api/`
- Docs: FEATURE-MATRIX, FINAL-AUDIT (75/100 NOT READY), KNOWN-LIMITATIONS, RELEASE-CHECKLIST, TESTING, DEPLOYMENT, PWA

## Baseline gates (sebelum misi ini)

- `php artisan test`: **48 passed / 0 failed** (93 assertions)
- `npm run build`: sukses (~14.5s)
- `composer validate`: valid · `php -l`: bersih · `node --check sw.js`: OK
- Skor terakhir: 75/100, NOT READY (webhook unsigned = critical terbuka)
