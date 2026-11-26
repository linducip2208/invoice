# FASE 14 — Stok produk + dunning langganan (continuous execution)

## I1. Stok produk opt-in [x] DONE
- Migrasi aditif: `track_stock`, `stock_qty`, `low_stock_at`. Mati = perilaku lama, nol perubahan.
- Kurang saat invoice dibuat/duplikat/konversi paket; kembali saat ubah/hapus draft; badge stok + "menipis" di list.
- Quote (DP/pelunasan/penuh) tidak menyentuh stok (tanpa product_id) — didokumentasikan.

## I2. Dunning langganan [x] DONE
- Command `subscriptions:dun` (jadwal mingguan): email owner/admin workspace berstatus past_due + link billing. `--dry-run` tersedia.

## Tes
- `tests/Feature/Phase14Test.php`: 3 passed (kurang-kembali-hapus, non-track untouched, dunning queued). Build sukses.
