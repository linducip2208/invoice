# FASE 12 — Ekspor CSV quotes & produk (continuous execution)

## J1. CSV Quotation + Produk [x] DONE
- `ExportController@quotes` (no, klien, status, revisi, tanggal, total, currency) dan `@products` (nama, kategori, harga, pajak, aktif) — pola sama + anti formula-injection.
- Route + kartu unduh di halaman Export.

## Tes
- `tests/Feature/Phase12Test.php`: 1 passed (isi CSV terverifikasi). Full suite: 189 passed. Build sukses.
