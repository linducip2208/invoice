# QA Results

- `composer validate`: perlu dijalankan manual (composer tersedia).
- `php artisan test`: 153 passed, 553 assertions (144 bawaan + 9 RedesignTest).
- `npm run build`: sukses (Vite v8.3.4).
- E2E Playwright: infra v1.64.0 tersedia; screenshot landing desktop/mobile berhasil.
- A11y/responsive: diperiksa via kode + `tests-e2e/{a11y,responsive,zoom}.spec.js` (full run manual).
- Tidak ada test yang di-skip untuk menghijaukan hasil.
