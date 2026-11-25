# FASE 3 — Batch lanjutan (continuous execution)

## Q1. Quote expiry follow-up [x] DONE
- Filter `status` + checkbox `expiring` (≤7 hari, sent/draft) di `quotes/index`; baris soon-highlight + badge.
- Controller: scope tanggal di `QuoteController@index`.

## Q2. Bulk send invoice [x] DONE
- `POST invoices/bulk-send` (maks 10/aksi): antre email+PDF per invoice (email klien wajib), draft→sent, EmailLog queued. Tombol di header list via `formaction`.

## Q3. Credit Issue + Apply 1-klik [x] DONE
- `CreditNoteController@issue` terima flag `apply` → langsung apply dalam request yang sama. Tombol "Issue + Apply" di detail.

## Q4. WA jam-kerja guard [x] DONE
- Alert di preview bila di luar 08:00–17:00 WIB. Tanpa blokir, tanpa ubah kontrak kirim.

## Tes
- `tests/Feature/Phase3Test.php`: 4 passed. Full suite: 164 passed / 588 assertions. Build sukses.
