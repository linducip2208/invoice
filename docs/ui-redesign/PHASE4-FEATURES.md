# FASE 4 — Batch lanjutan (continuous execution)

## B1. DP → invoice pelunasan 1-klik [x] DONE
- `QuoteConversionService::convertRemainder`: sisa = total quote − sum DP revisi; idempoten 1 pelunasan/revisi (tipe `remainder`).
- Route `POST quotes/{quote}/remainder`; tombol "Buat Pelunasan" di detail quote (muncul bila sudah ada DP).

## B2. NPS promoter → link ulasan [x] DONE
- Migrasi aditif `workspaces.google_review_url`; field di Company Settings; tombol review di halaman thanks bila skor ≥9 + link terisi.

## B3. Timer timesheet [x] DONE
- Start/stop via session; stop → redirect dengan jam terhitung (min 0.25) terisi otomatis di form entry. Tanpa schema baru.

## Tes
- `tests/Feature/Phase4Test.php`: 4 passed. Full suite: 168 passed / 604 assertions. Build sukses.
