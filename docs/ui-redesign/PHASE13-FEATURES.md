# FASE 13 — Jadwal kirim, credit limit, PDF laporan (continuous execution)

## H1. Kirim invoice terjadwal [x] DONE
- Tombol "Jadwalkan" di modal kirim (datetime wajib > now) → `ScheduledInvoiceSend` (pending).
- Command `invoices:send-scheduled` per jam: kirim PDF+email, update status invoice, tandai sent; lewati lunas/batal; gagal → failed + alasan. Daftar + batal di halaman Reminders.
- Bug mirip ditemukan & dihindari: return-type `View` vs file download (lihat H3).

## H2. Credit limit klien [x] DONE
- Kolom `clients.credit_limit` (migrasi aditif) + input di form klien + fillable/validasi.
- Saat buat invoice: tolak bila piutang aktif + total baru > limit (pesan rinci). Tanpa limit = bebas.

## H3. PDF semua laporan [x] DONE
- `?format=pdf` di ringkasan, aging, cashflow, P&L, pajak → blade PDF minimalis + tombol PDF di tiap halaman.
- Bonus fix: `ReportController::aging()` CSV branch mengembalikan StreamedResponse padahal signature `: View` (= 500 saat dipakai) → signature dilonggarkan ke semua 5 method.

## Tes
- `tests/Feature/Phase13Test.php`: jadwal (tunda + jalan), tolak masa lalu/lunas, limit blokir/lolos, 5 PDF terunduh.

## Temuan QA & perbaikan
- CSV aging 500 (return type `View` vs StreamedResponse) → signature dilonggarkan di 5 method; tambah test PDF yang memaksa eksekusi semua branch format.
- E2E DB basi (kolom baru tak ada) → prosedur: reseed e2e tiap ada migrasi baru.
- Kontras `th` tabel (4.44:1) → `#52657f`; footer + "Powered by" + lead docs dikunci ke warna AA.
- Selektor E2E `.page-body` hilang saat ganti shell → dikembalikan sebagai kelas ganda `app-main page-body`; toggle dark-mode tampil di mobile.
- Zoom 195/180px: badge hero, header kartu, tombol panjang, badge+button template → wrap/kolaps di ≤400px.