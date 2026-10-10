# FRONTEND BEFORE → AFTER (audit nyata, 2026-10-10)

Basis: 167 Blade views, Tabler 1.6.1 satu-satunya framework (Tailwind/unicorn
dihapus misi lalu; grep-zero terverifikasi), 74 ikon SVG lokal, E2E overflow 0.

## Temuan nyata (file:baris) → tindakan

1. Sidebar 9 section, dominan di laptop kecil (`components/tabler/layout.blade.php`)
   → konsolidasi ke WORKSPACE/SALES/CASH FLOW/INSIGHTS/CONFIGURATION/BILLING/HELP.
2. Dashboard tanpa tren & tanpa daftar overdue/quote-menunggu (`dashboard/index`, `DashboardController@index`)
   → tambah SVG trend koleksi 8 minggu + kartu overdue + quote pending (query agregat, diuji).
3. Invoice detail tanpa timeline audit (`invoices/show`, padahal `audit_logs` ada)
   → tambah timeline dari `audit_logs` + status pengiriman (sudah ada) dalam satu kartu aktivitas.
4. Copy whimsy ("magical", "sparkly", "Conjure") di `dashboard/index`, subtitle layout
   → nada profesional, brand tetap.
5. `x-tabler.stat` 5 kartu sebaris tidak seimbang → grid 4 KPI finansial + baris kedua operasional.
6. Emoji hanya tersisa di `emails/*` (exempt, klien email) → nol di UI aplikasi.
7. Kontras/fokus: token Tabler + `:focus-visible` global sudah ada → verifikasi keyboard via E2E.
8. Print: chrome admin sudah `d-print-none`; halaman show memakai layout yang sama → OK.
9. `number_format` inkonsisten (0 vs 2 desimal antar view) → helper `money()` terpusat + dipakai di view yang diubah.
10. Page header sudah konsisten via layout (pretitle/title/actions) → dipertahankan, tambah quick action New Quotation di dashboard.

Tidak ada: tombol palsu (NavIntegrity test), dead link shell (test), duplikat Bootstrap,
Tailwind sisa, atau angka dummy (KPI dari DB, dibuktikan DashboardTest).
