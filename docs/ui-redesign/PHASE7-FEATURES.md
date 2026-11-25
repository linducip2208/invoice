# FASE 7 — Batch lanjutan (continuous execution)

## E1. Bulk jadwalkan pengingat [x] DONE
- `POST invoices/bulk-remind` (maks 20): pakai `ReminderScheduler` per invoice; lunas/batal/opt-out dilewati + dilaporkan. Tombol di list invoice.

## E2. Notifikasi pembayaran masuk [x] DONE
- Manual payment tercatat → notifikasi workspace (+link invoice).
- Webhook gateway settle → notifikasi (tanpa ubah verifikasi/settlement logic).
- Finalisasi rekonsiliasi → notifikasi ringkasan (bank + jumlah payment).

## Tes
- `tests/Feature/Phase7Test.php`: 3 passed. Full suite: 177 passed / 634 assertions. Build sukses.
