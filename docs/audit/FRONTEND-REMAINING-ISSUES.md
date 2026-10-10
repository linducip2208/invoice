# FRONTEND REMAINING ISSUES

1. Zoom browser 200% belum diverifikasi (butuh sesi browser manual).
2. Screen-reader audit belum dilakukan (label/fokus/keyboard sudah; semantik belum diuji NVDA/VoiceOver).
3. ~1200 string lama Inggris-fallback di locale id (utang pre-existing; string baru lengkap ID+EN).
4. Grafik tren memakai SVG inline (disengaja, tanpa library);Tooltip hanya via `<title>` (terbatas di touch).
5. `number_format` legacy bervariasi antar view lama; helper `format_money()` tersedia untuk migrasi bertahap.
6. Mode gelap pada PDF/email tidak diterapkan (by design: dokumen cetak terang).
