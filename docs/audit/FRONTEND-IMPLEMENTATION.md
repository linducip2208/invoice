# FRONTEND IMPLEMENTATION (2026-10-10)

Stack: Tabler 1.6.1 + Bootstrap 5.3 (satu-satunya framework), Alpine (interaksi),
Vite. Tanpa Tailwind, tanpa CDN UI, tanpa webfont (74 SVG lokal via `x-ti`).

## Shell & navigasi (`components/tabler/layout.blade.php`)

IA: WORKSPACE / SALES / CASH FLOW / INSIGHTS / CONFIGURATION / BILLING / HELP /
SUPERADMIN (role-gated). Active state via `routeIs`; offcanvas mobile via
toggler + Escape; workspace switcher; quick-create dropdown (invoice/quote/
customer); bell notifikasi dengan badge unread (satu query count, tenant-scoped);
theme toggle persisten; user menu. Semua 44 item terverifikasi di
SIDEBAR-ROUTE-AUDIT (44/44 PASS) + NavIntegrityTest.

## Komponen (`components/tabler/*`, `components/ti.blade.php`)

layout, guest, public, alert (flash + validation, aman di error pages),
stat, empty, field (label+error), delete-button (konfirmasi), status-badge
(status→warna konsisten), ti (ikon lokal + fallback). Pagination Bootstrap 5
via `Paginator::useBootstrapFive()`. Aturan: dot-notation wajib
(`x-tabler.*`; dash tidak resolve di Laravel 13).

## Layar inti

- Dashboard: 4 KPI finansial (agregat tunggal + bucket PHP), SVG tren koleksi
  8 minggu (tanpa library), overdue + quote pending, recent, quick actions.
- Invoice list/detail/create/edit: toolbar filter berfungsi, tabel responsif,
  nominal rata kanan, badge konsisten, timeline audit, delivery log, payment
  history, rotate-link, PDF/print (`d-print-none` pada chrome).
- Quote: lifecycle states + approval flow (OTP/portal/consent) di Tabler;
  public/portal konsisten dengan shell publik.
- Auth/guest/errors: card terpusat; 403/404/419/429/500/503.
- Email/PDF/print: dikecualikan by design (kompatibilitas klien email & cetak).

## Tokens (`tabler-branding.css`)

`--tblr-primary` ungu brand, dark mode `data-bs-theme`, radius/spacing/
shadow mengikuti Tabler, focus-visible global, touch target ≥2.5rem mobile,
system font stack (tanpa fetch eksternal).
