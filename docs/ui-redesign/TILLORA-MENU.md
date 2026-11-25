# TILLORA — tema + menu mega + konektivitas (continuous execution)

## Shell Tillora penuh (Tabler tetap)
- Topbar navy: brand, workspace switcher, global search (Ctrl+K / `/`, + filter menu), quick-create, notification center dropdown, theme toggle, profile dropdown — sesuai pola pos-retail.
- Mega-menu horizontal per proses bisnis (klik, bukan hover): Dashboard | Penawaran | Penagihan | Pembayaran | Laporan | Otomatisasi | Pengaturan — dengan ikon + deskripsi + panel fitur. Mobile: drawer offcanvas + grup lipat.
- Bukti: `landing/dashboard-tillora.png`, `landing/dashboard-mega.png`.

## Tema Tillora (Tabler tetap, Tillora sebagai skin)
- Sumber: `D:\project laravel\notaris\web\assets\notarius\tillora.css` (+ tokens). Prinsip sama: komponen tetap Tabler murni, skin hanya token + polesan.
- Port ke `resources/css/tillora.css` (di-import setelah branding): `--app-*` tokens (primer diselaraskan ke brand Invocely #4338CA), tombol (quick/soft/ghost), kartu & tabel (radius 20, thead uppercase, hover), status-badge pills, breadcrumb pill + segmented + range-bar + toolbar, KPI (kpi-card + icon-box tones + delta), lists (list-app/cell-user), alert-strip, avatar tones, dropdown/search/create-menu, hero-card, dark mode, reduced-motion.
- Sengaja TIDAK diport: topbar-nav horizontal, POS, heatmap, stok, customizer, mobile offcanvas (tidak cocok sidebar vertikal Invocely).

## Menu proses bisnis (sidebar workspace)
- BERANDA → 01 PENAWARAN (quotes, produk, bundel, pelanggan) → 02 PENAGIHAN (invoice, recurring, credit note, persetujuan, proyek, time tracking) → 03 PEMBAYARAN (payments, kupon, aging, rekonsiliasi, expenses) → 04 LAPORAN → 05 OTOMATISASI → 06 PENGATURAN → LANGGANAN → BANTUAN.

## Koneksi fitur (audit → fix)
- Baru tersambung: Time Tracking, Kupon Diskon, Persetujuan, Email ke Expense, Embed Widget, Manual Assign (tombol di admin subscriptions), 2FA (link di Profile).
- Dihapus: stub `marketplace` (route+view, tak terpakai), duplikat rute `/compare/*` (`plans.compare` mati tertimpa — dipertahankan `seo.compare` tanpa constraint agar slug bebas tetap hidup; kasus ini tertangkap PseoTest lalu diperbaiki).
- Komentar basi `feature.coming` diluruskan (route dipertahankan sebagai mekanisme stub).
- Breadcrumb/kembali ditambahkan: time-entries, coupons, email-forwards, mobile quick, embed/demo, 2FA, kwitansi (tombol kembali, d-print-none).
- Master-detail (show/edit/create, nested invoice, level-2 admin blog) tetap via tombol induk — wajar, bukan yatim.

## General error check
- Full suite: 189 passed. `RenderSmokeTest` diperluas (bundles, coupons, approvals, email-forwards). `npm run build` sukses.
