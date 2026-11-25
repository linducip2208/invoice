# FASE 10 — Paket / Bundel produk (continuous execution)

## H1. CRUD paket + komponen [x] DONE
- Migrasi aditif `product_bundles` + `product_bundle_items`.
- `BundleController` (index/create/store/show/edit/update/destroy), validasi produk se-workspace, link sidebar SALES → Bundles.

## H2. Paket → draft invoice 1-klik [x] DONE
- `POST bundles/{bundle}/invoice` (plan-gated): harga paket fixed → 1 baris; tanpa harga paket → 1 baris per komponen (qty × harga + pajak produk). Notes "Dari paket: X".

## Tes
- `tests/Feature/Phase10Test.php`: 3 passed (fixed price, expand komponen, isolasi). Full suite: 188 passed. Build sukses.
