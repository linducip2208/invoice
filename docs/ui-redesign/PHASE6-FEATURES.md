# FASE 6 — Batch lanjutan (continuous execution)

## D1. Bulk PDF ZIP [x] DONE
- `POST invoices/bulk-pdf` (maks 10): gabung PDF jadi ZIP via `generatePdf` yang sudah ada. Tombol di list invoice.

## D2. Mutasi klien [x] DONE
- `GET clients/{client}/statement`: total ditagih/dibayar/sisa + tabel invoice + pembayaran + tombol Print/PDF browser. Tombol "Mutasi" di detail klien.

## D3. Duplikat produk [x] DONE
- `POST products/{product}/duplicate`: salinan nonaktif "(copy)" → langsung ke edit untuk review. Mempercepat bikin varian/paket harga.

## Tes
- `tests/Feature/Phase6Test.php`: 3 passed. Full suite: 174 passed / 627 assertions. Build sukses.
