# FASE 8 — Batch penutup (continuous execution)

## F1. Bulk mark sent [x] DONE
- `POST invoices/bulk-mark-sent` (maks 20): draft/cancelled → sent + `last_sent_at`, tanpa email. Untuk invoice yang dikirim via WA/cetak/langsung.

## Rekap fase 2–8
- P1 timesheet→invoice, P2 quote DP, P3 cashflow outflow+net, P4 saran match, P5 template WA
- Q1 quote expiry, Q2 bulk send, Q3 issue+apply, Q4 WA hours guard
- B1 pelunasan DP, B2 NPS review link, B3 timer timesheet
- C1 kupon manual, C2 past_due dunning
- D1 bulk PDF ZIP, D2 mutasi klien, D3 duplikat produk
- E1 bulk remind, E2 notifikasi pembayaran, F1 bulk mark sent
- Migrasi aditif: `invoices.quote_conversion_type`, `workspaces.google_review_url`.

## Tes
- `tests/Feature/Phase{2,3,4,5,6,7,8}Test.php`. Full suite: 178 passed / 638 assertions. Build sukses.
