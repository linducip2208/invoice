# IMPLEMENTATION PLAN — Tabler 100% + finalisasi

## Keputusan arsitektur

1. **Tabler `@tabler/core@1.6.1` + `@tabler/icons@3.49.0`** via npm/Vite (pin exact).
   Bootstrap 5.3 ikut sebagai dependency Tabler (satu-satunya CSS framework).
2. **Tailwind dihapus total** setelah migrasi: uninstall + hapus config/postcss
   + hapus `@tailwind` + hapus `app.css` unicorn. Satu sumber kebenaran.
3. **Alpine.js dipertahankan** hanya untuk interaksi (dropdown/sidebar/offcanvas),
   karena Tabler butuh Bootstrap JS juga — keduanya boleh koeksistensi untuk JS,
   tapi komponen visual harus Tabler.
4. **Komponen Blade reusable** (`components/tabler/*`): page-header, card,
   stat-card, table-card, empty-state, form input/select/checkbox, badge,
   confirm-button, pagination bawaan Laravel di-style via
   `pagination::bootstrap-5`. Tidak ada duplikasi markup per halaman.
5. **Brand via CSS variables** (`tabler-branding.css`): `--tblr-primary` ungu
   brand (#A78BFA), font Inter + display font, dark mode via
   `data-bs-theme="dark"` + toggle persisten.
6. Migrasi per modul, diverifikasi: `php artisan test` + `npm run build` +
   `git grep` sisa unicorn/tailwind per batch. Email + PDF TIDAK disentuh
   (HTML email client & print layout tetap).
7. i18n: semua label baru pakai `__()`; sinkron `lang/en.json` + `lang/id.json`
   tanpa duplikat (perbaiki duplikat Payments).

## Urutan

1. Install + Vite wiring + tokens + layout shell + sidebar IA (§3 misi).
2. Auth/guest/errors/public + docs Tabler.
3. Modul bisnis (clients→products→invoices→quotes→payments→recurring→
   credit→reports→finance→workspace→team→billing→admin→portal→blog).
4. Hapus Tailwind; grep-zero verification.
5. Sidebar-route audit + nav integrity tests.
6. Webhook signature per-provider + isolation negative tests + rate limits.
7. Dashboard KPI + money/CSV/mail/queue/PDF tests; /docs rewrite.
8. PWA automated tests; Playwright attempt; docs; skor; push.

## Definisi selesai per view

Terdaftar di UI-MIGRATION-MATRIX (Status=MIGRATED), memakai layout/components
Tabler, tanpa class unicorn, tanpa utility Tailwind sebagai layout utama,
tanpa emoji ikon, label via `__()`, tabel responsif (`.table-responsive`),
form dengan error + old input, empty state beraksi.
