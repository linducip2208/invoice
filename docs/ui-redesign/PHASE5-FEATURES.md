# FASE 5 — Batch lanjutan (continuous execution)

## C1. Redeem kupon untuk pembayaran manual [x] DONE
- Input kode kupon di form Pay Manual (pricing). Validasi: aktif/belum habis, cocok plan + currency; gateway + kupon ditolak jujur.
- Diskon tersimpan di `subscriptions.metadata`; halaman instruksi manual tampilkan total diskon; saat approve admin: invoice diskon + `uses_count++` + `CouponRedemption`.

## C2. Dunning past_due [x] DONE
- Banner + CTA "Bayar Sekarang" (#plan-list) di billing bila status past_due.

## Tes
- `tests/Feature/Phase5Test.php`: 3 passed. Full suite: 171 passed / 619 assertions. Build sukses.
