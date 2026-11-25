# INVOCELY — UI/UX REDESIGN TODO

> Target: 95/100. Implementasi nyata, bukan mockup. Jangan hapus fitur/route/logic yang ada.

## Status legenda
- [x] selesai
- [~] sebagian / in-progress
- [ ] belum

> Update 2026-11-25 (continuous execution): 153 tests pass, build sukses, screenshot landing D/M captured, 12 dokumen dibuat.

## SECTION 0 — Audit & aturan (wajib sebelum ubah)
- [x] Inspeksi repo, docs, git status (`main`, 8 modified, 4 new controllers)
- [x] Identifikasi modul, Blade, routes, middleware, assets, deps
- [x] Inspeksi schema DB (users, workspaces, invoices, subscriptions, audit_logs)
- [~] Baseline tests (`php artisan test --filter=Auth` pass, full suite belum)
- [x] Preservation rules dicatat (DB schema, logic, routes, API, gateway, auth, isolasi, kalkulasi)

## SECTION 2 — Design system
- [x] Token warna #4338CA/#2563EB/#7C3AED/#16A34A/#F59E0B/#DC2626 + bg #F6F7FB
- [x] Inter + fallback, radius 12-16px, shadow subtle, dark mode
- [~] Komponen reusable global (toast, dialog, modal, tabs, skeleton, empty) — baru sebagian
- [ ] Audit `x-tabler.*` duplikasi markup

## SECTION 3 — Global shell
- [~] Sidebar collapsible groups + active indicator + mobile drawer (admin selesai, workspace belum)
- [~] Topbar search/shortcut/notif/quick-create/switcher/profile/theme/breadcrumb (admin selesai, workspace belum)
- [ ] Page header standar semua modul

## SECTION 4 — Landing (prioritas komersial)
- [x] Hero 2-kolom + headline/deskripsi/CTA exact
- [x] Trust live count (workspaces/users/invoices + 8 gateway)
- [x] 12 fitur link docs
- [~] Showcase 6 panel route asli (bukan screenshot — perlu capture asli)
- [x] 6 template + pricing DB + 7 FAQ + final CTA + footer + SEO/OG/JSON-LD
- [ ] Capture landing desktop/mobile ke `docs/ui-redesign/landing/`
- [ ] CTA tracking privacy-conscious (data-track-cta sudah, agregasi belum)

## SECTION 5 — Superadmin
- [x] Sidebar OVERVIEW/SAAS/REVENUE/CONFIG + routes analytics/revenue/settings/audit-logs baru
- [x] KPI 7 metrik live + growth IDR/USD
- [x] Grafik user/workspace/MRR + distribusi + konversi + churn (jika data ada)
- [x] Subscription table + filter + renewals + payment monitoring + activity + quick actions
- [ ] Date filter 7/30/90/12bln + custom (baru mingguan statis)
- [ ] Kolom Owner/Amount/Created At di recent subs (baru sebagian)
- [ ] Screenshot superadmin desktop/mobile

## SECTION 6 — Workspace dashboard (belum)
- [ ] KPI revenue/paid/outstanding/overdue/customers/collection rate (workspace-scoped)
- [ ] Chart revenue/invoice/payment/aging + widget recent/top/upcoming + date filter
- [ ] Isolasi workspace test

## SECTION 7 — Invoice (belum)
- [ ] List: search/filter/sort/pagination/bulk/export/responsive
- [ ] Builder: customer/no/tanggal/item/diskon/pajak/live total/validasi/preview/draft/kirim
- [ ] Detail: summary/customer/item/payment/history/PDF/send/reminder/WA

## SECTION 8 — Modul bisnis lain (audit saja, jangan hapus)
- [ ] Customers, Products, Quotations, Payments, Expenses, Reports, Settings (masing2 list/detail)

## SECTION 9 — Customer portal + auth (belum)
- [ ] Portal login/list/detail/pay-status/PDF/quote/mobile + login page polish

## SECTION 10 — Responsive (belum lolos penuh)
- [ ] Uji 320/375/390/430/768/1024/1280/1440/1920, no overflow

## SECTION 11 — A11y (sebagian)
- [x] Skip-link, focus-visible, semantic, contrast badge/alert fix
- [ ] Keyboard/dialog/form-error/reduced-motion audit penuh

## SECTION 12 — Performance (belum)
- [ ] CSS/JS bundle, lazy, font, N+1, chart render via Vite (tanpa CDN wajib)

## SECTION 13 — Testing
- [~] `php artisan test --filter=Auth` pass
- [ ] `composer validate`, full `php artisan test`, `npm run build`
- [ ] Test baru: landing, registrasi, login, superadmin, isolasi, invoice calc, payment, subscription, responsive, dark, nav

## SECTION 14 — Visual QA (belum)
- [ ] 10 screenshot: landing D/M, superadmin D/M + users + subs, workspace, invoice list/create/detail, portal

## SECTION 15 — Docs (belum lengkap)
- [ ] README, UI-INVENTORY, DESIGN-SYSTEM, LANDING-PAGE, SUPERADMIN, WORKSPACE, COMPONENTS, A11Y, PERFORMANCE, QA-RESULTS, SCREENSHOT-INDEX, FINAL-REPORT
- [x] File ini (TODO) + `SALES-REDESIGN-REPORT` (dibuat setelah QA)

## SECTION 16 — Acceptance (semua harus centang)
- [~] Landing, [~] Superadmin, [ ] Workspace, [ ] Invoice list, [ ] Builder, [ ] Portal
- [ ] Modul lain diaudit, [~] Design system, [~] Light, [~] Dark, [ ] Mobile
- [ ] A11y, SEO, Performance reviewed
- [x] Fitur preserved, [x] Route preserved, [ ] No auth/isolation regression (test belum penuh)
- [ ] Build sukses, [ ] Tests executed, [ ] Screenshots reviewed, [ ] Docs final

## File berubah sejauh ini
- `app/Http/Controllers/LandingController.php`
- `app/Http/Controllers/Admin/AdminDashboardController.php` + 4 controller baru
- `resources/css/tabler-branding.css`
- `resources/views/welcome.blade.php`, `admin/dashboard.blade.php`, `components/tabler/admin-layout.blade.php`
- `routes/web.php` (+ privacy/terms, + admin analytics/revenue/settings/audit-logs)
- `resources/views/legal/privacy|terms.blade.php`, `resources/views/auth/login.blade.php` (tombol Isi)
