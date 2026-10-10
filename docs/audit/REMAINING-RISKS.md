# REMAINING RISKS (residual, jujur)

1. Webhook tanpa secret tetap diproses (guards aktif) — tutup dengan
   mengisi secret provider, lalu uji sandbox; kode verifikasi + 4 test siap.
2. Uang: decimal + float ter-rounding (D4 sebelumnya); migrasi minor-units
   ditunda (butuh rencana migrasi data).
3. Late-fee compounding tanpa cap (R1); tetapkan plafon kebijakan.
4. Credit-note invariant amount_paid+balance≠total setelah apply (R3, by design).
5. NPWP checksum kustom, bukan resmi DJP (R4); jangan klaim resmi.
6. Tanpa SoftDeletes; hapus workspace/invoice permanen (R5).
7. Scheduler tanpa withoutOverlapping; worker/cron/pruning tanggung jawab deploy (R7).
8. Rate limit: login mengandalkan LoginRequest 5x lockout; route throttle hanya
   register/password-email; webhook tak di-throttle (burst provider).
9. i18n: EN lengkap (identity), ID 473 kunci; ~1200 string lama Inggris-fallback
   (utang pre-existing, bukan regresi).
10. PWA install device-nyata belum difoto; start_url `/`.
11. Pint style debt repo-wide belum diadopsi.
12. Storage symlink dev sempat read-only (diperbaiki via attrib); pastikan
    permission di server (`storage/`, `bootstrap/cache` writable).
