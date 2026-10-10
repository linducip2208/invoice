# KNOWN LIMITATIONS & RISIKO RESIDUAL

Tanggal: 2026-10-10. Kejujuran di atas skor: yang belum terbukti lulus
tercatat di sini, bukan diklaim lulus.

## 1. BLOCKED — butuh kredensial / layanan eksternal

| # | Item | Dampak | Jalan keluar |
|---|---|---|---|
| B1 | Verifikasi signature webhook payment (Midtrans/Xendit/Tripay/Doku/dll.) | Endpoint menerima callback tanpa HMAC; guards transaksional + replay + amount-wins membatasi, tapi spoofing penuh belum ditutup | Konfigurasi secret per gateway + verifikasi per format provider, uji dengan sandbox |
| B2 | Verifikasi signature webhook billing | Sama seperti B1 untuk subscription | Sama |
| B3 | Kirim email nyata (invoice, reminder, reset) | `MAIL_MAILER=log` default; tidak ada pengiriman nyata teruji | SMTP prod + uji kirim + verifikasi antrean |
| B4 | WhatsApp / QRIS / AI provider / OAuth real | Alur kode ada, tidak ada kredensial untuk uji end-to-end | Kredensial sandbox provider |
| B5 | Browser E2E (Playwright) + visual regression per viewport | Tidak dijalankan; tidak ada console-error proof | Jalankan di workstation dengan browser |

## 2. Keputusan arsitektur yang disengaja (deviasi dari misi)

| # | Item | Alasan |
|---|---|---|
| D1 | **Design system tetap Tailwind**, bukan migrasi penuh ke Tabler | 190 view konsisten pada satu sistem (palet/komponen/tipe terdefinisi di `app.css` + `tailwind.config.js`). Migrasi Tabler (berbasis Bootstrap) berarti rewrite total + risiko regresi pada seluruh alur yang baru dikeraskan, tanpa kemampuan verifikasi visual di environment ini. Mencampur keduanya dilarang misi. Ditunda sebagai proyek terpisah dengan QA visual. |
| D2 | Cross-workspace denial = 404 (scoped binding) atau 403 (guard) | Keduanya membuktikan isolasi; 404 tidak membocorkan eksistensi ID tenant lain. Test menerima keduanya secara eksplisit dan terdokumentasi. |
| D3 | Tanpa unique DB constraint baru untuk idempotensi webhook | Menghindari kegagalan migrasi pada database existing yang mungkin punya duplikat; idempotensi ditegakkan di level aplikasi (cek + row lock). Unique constraint direkomendasikan saat data sudah bersih. |
| D4 | Uang tetap `decimal(15,2)` + aritmetika float ter-rounding | Migrasi ke integer-sen/bcmath adalah refactor besar menyentuh semua service + laporan; risiko > manfaat dalam sesi ini. Inkonsistensi kapasitas quote/expense (12,2) vs invoice (15,2) tidak kritis untuk IDR. |

## 3. Risiko residual yang diterima

- **R1** LateFee compounding persen dari balance (tanpa cap) — atur kebijakan plafon sebelum pakai.
- **R2** `Quote::convertToInvoice` memakai tarif header quote untuk semua line (by design, pajak per-line quote tidak disimpan).
- **R3** Credit-note `apply` mengurangi balance tanpa menambah amount_paid (`amount_paid + balance_due ≠ total`) — konsisten internal, rekonsiliasi harus tahu.
- **R4** NPWP validator memakai checksum modulus-11 kustom (bukan checksum resmi DJP) + fallback `valid=true` offline — jangan klaim verifikasi DJP resmi.
- **R5** Tanpa SoftDeletes: hapus workspace/invoice permanen. Client ber-invoice tidak bisa dihapus (guard ramah).
- **R6** `lang/id.json` punya kunci duplikat (`Payments`/`payments`) — PHP toleran (last-wins), parser JSON ketat gagal. Bersihkan saat sentuh file itu.
- **R7** Scheduler tanpa `withoutOverlapping()`; queue/DB-cache butuh worker + cron + pruning di prod.
- **R8** Storage symlink di repo dev rusak (typo path) — jalankan `php artisan storage:link` di server.
- **R9** PWA: belum ada uji installability di device nyata; start_url `/`; transaksi offline tidak didukung (by design).
- **R10** Tidak ada rate-limit khusus login/API di luar default throttle — aktifkan sebelum expose publik.
