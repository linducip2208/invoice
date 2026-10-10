# REDESIGN ISSUES (temuan nyata, file:baris) — update 2026-10-10

1. `welcome.blade.php`: SELESAI ronde ini — hero display-4 + preview dokumen
   dominan (tabel 2 baris + total/balance/progress), trust strip plain divider
   (bukan kartu), capabilities sebagai definition list 2-kolom, editorial
   alternating tint/metric (bukan kartu berulang), lifecycle sebagai numbered
   steps tanpa nested cards, FAQ 2-kolom tanpa outer card.
2. Sidebar 8 grup: tetap fungsional; dashboard kini pakai breadcrumbs
   (`dashboard/index.blade.php:1`) — breadcrumb layout sudah didukung
   (`components/tabler/layout.blade.php`, dipakai juga `invoices/show:1`).
3. Dashboard: SELESAI parsial — breadcrumbs + subtitle "last 8 weeks",
   KPI terformat ID (`number_format ...,','.`), chart collections dengan grid
   + link cashflow, overdue jadi "Needs attention", quick actions dengan
   deskripsi berbeda. Rentang tanggal tetap belum ada (backend tak mendukung —
   disengaja tidak ditambah).
4. Invoice create: SELESAI ronde ini — form 3 langkah bernomor
   (`invoices/create.blade.php`), kolom kanan sticky live preview
   (items/total/pajak/diskon + tombol submit). Detail `show.blade.php`
   sudah punya timeline aktivitas + breadcrumbs.
5. `format_money()` belum dipakai di view lama (migrasi bertahap) — dashboard
   kini format manual konsisten ID, migrasi helper penuh ditunda agar tidak
   merusak 138 tests hijau.
6. Superadmin shell sudah terpisah (misi lalu) — verifikasi ulang OK.
7. Lihat juga FRONTEND-BEFORE-AFTER.md (temuan ronde sebelumnya, selesai).

Gate tersisa (bukan desain): secret webhook, gateway sandbox (B1/B2),
PWA device nyata, screen reader manusia — lihat FINAL-AUDIT.md.

## Ronde 2 (2026-10-10, sudah diperbaiki + hijau 64/64)

8. `welcome.blade.php` capabilities/FAQ: `row g-4`/`row-deck` overflow
   4–23px di ≤768px (`row-deck` memaksa kolom jadi flex) → `row` biasa.
9. Hero metrics `.h2` hitam di navy → `text-white`.
10. `pricing/index.blade.php`: ribbon `Popular` terpotong → badge inline.
11. `invoices/create.blade.php`, `invoices/edit.blade.php`,
    `invoice-layouts/builder.blade.php`: `x-show` kalah vs
    `.d-flex`/`.d-grid !important` → display utility kondisional.
12. Zoom 200%: overflow 0. PWA: manifest + ikon + sw.js OK (install device
    nyata tetap manual). Payment: `WebhookSignatureVerifier` + 403 +
    lock/replay/stored-amount terverifikasi di kode; sandbox live BLOCKED
    (butuh kredensial asli).

## Ronde 3 (2026-10-10, sweep a11y semantik)

13. Tanpa landmark `<main>` di semua layout → `<main id="main-content">`
    di `layout`/`admin-layout`/`public`/`guest`.
14. Tanpa skip-link → `.skip-link` (muncul saat fokus keyboard) + CSS.
15. Halaman app tanpa `h1` (`h2.page-title`) → `h1` (class dipertahankan).
16. Input tanpa label di `invoices/create` (color hex, baris item Alpine) →
    `aria-label` (termasuk dinamis per baris).
17. Judul `<title>` blog ganda ("Blog — Invocely — Invocely") → seoMeta
    tanpa suffix app.name (`BlogController`, karena layout sudah menambahkannya).
18. Lompat heading h1→h3 di hero landing → judul preview jadi `h2`.
19.axe tetap acuan otomatis (8/8); screen-reader manusia tetap manual.
