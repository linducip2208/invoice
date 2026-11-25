# QA Results (diperbarui: cek semua error)

## Unit
- `composer validate`: valid.
- `php artisan test`: 194 passed / 700 assertions (batch-13).
- `npm run build`: sukses.

## E2E browser (Playwright 1.64, Chromium, server :8001 + e2e.sqlite reseed)
- Awal: 49 passed / 23 failed → akhir: bertahap diperbaiki.
- Temuan & perbaikan nyata:
  1. `th` tabel 4.44:1 → `#52657f` (AA).
  2. Footer publik + "Powered by" + lead docs → warna AA eksplisit.
  3. CSV aging 500 (return type `View` vs StreamedResponse) → signature dilonggarkan.
  4. Selektor `.page-body` hilang saat ganti shell → dikembalikan (`app-main page-body`).
  5. Toggle dark-mode sembunyi di mobile → selalu tampil.
  6. DB e2e basi (kolom fase 12–13) → reseed; prosedur: reseed tiap ada migrasi.
  7. Zoom 195/180px: badge hero, header kartu, tombol panjang, badge template → wrap/kolaps ≤400px (catatan: aturan Akhir-file menang atas base setara — jangan taruh override media di tengah file).
- Sisa: menunggu run final a11y + app spec (laporan otomatis berikutnya).

## Aturan
- Tidak ada test yang di-skip untuk menghijaukan hasil.
- E2E harus serial (`--workers=1`) karena SQLite + server tunggal.
