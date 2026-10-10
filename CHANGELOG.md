# CHANGELOG

## 2026-10-10 — Enterprise hardening (audit misi 100/100)

### Fixed (P0/P1)

- `workspace()` tidak lagi menonaktifkan tenant-scope di console → isolasi
  workspace aktif di tests/queue; 14 test gagal → hijau
- Route `workspace.create/store` + view + controller (baru tanpa workspace
  tadinya 500); 3 test WorkspaceTest
- Blog admin kini superadmin-only (+ sidebar gated, regression test 403)
- Komponen `x-layouts.seo` (blog publik 500)
- Guard 403 eksplisit Client/Invoice/Payment/Quote/CreditNote; validasi
  client_id/product_id scoped per workspace
- Numbering race-safe: invoice `lockForUpdate`, quote per-workspace + retry,
  credit-note retry; convert quote anti double (row lock + 409)
- `invoice_layout_id` kini tersimpan; `bank_reconcile_line_id` + referensi
  finalize rekonsiliasi; opt-out reminder per client (form + validasi)
- Webhook payment/billing: transaksi + row lock + replay guard +
  stored-amount-wins; Xendit/Doku fail-closed saat provider menolak
- E-faktur: escaping terverifikasi empiris, filename sanitized, status
  approved tidak regresi (2 unit tests)
- Demo credentials disembunyikan di luar local (login + docs); seeder demo
  tidak jalan di production tanpa `SEED_DEMO=true`
- Dashboard widgets reorder shadow; migrasi down `last_invoice_id`;
  email invoice responsif; branded error pages 403/404/419/500/503

### Added

- PWA: ikon 192/512/maskable, manifest diperbaiki, SW v2 network-only `/api/`
- CI GitHub Actions (phpunit + vite build)
- Docs: FEATURE-MATRIX, KNOWN-LIMITATIONS, FINAL-AUDIT (75/100),
  RELEASE-CHECKLIST, TESTING, DEPLOYMENT, PWA; README produk

### Tests

48 passed / 0 failed (sebelumnya 37 dengan 14 gagal). E2E browser + gateway
sandbox tetap BLOCKED (butuh kredensial/perangkat).
