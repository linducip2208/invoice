# REDESIGN ISSUES (temuan nyata, file:baris)

1. `welcome.blade.php`: hero kecil, klaim sudah dibersihkan tapi komposisi
   datar (header + kartu + grid 3 kolom berulang), preview invoice kecil,
   tanpa pricing ringkas, tanpa use-case editorial.
2. Sidebar 8 grup: fungsional tapi bertele-tele (`layout.blade.php`);
   tidak ada breadcrumb.
3. Dashboard: KPI + tren + donat + aging ada; belum ada rentang tanggal
   (backend tak mendukung — disengaja tidak ditambah).
4. Invoice create: terstruktur, tapi tanpa ringkasan live/preview dokumen.
5. `format_money()` belum dipakai di view lama (migrasi bertahap).
6. Superadmin shell sudah terpisah (misi lalu) — verifikasi ulang.
7. Lihat juga FRONTEND-BEFORE-AFTER.md (temuan ronde sebelumnya, selesai).
