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
PWA device nyata, zoom 200% + screen reader — lihat FINAL-AUDIT.md.
