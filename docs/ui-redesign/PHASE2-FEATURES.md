# FASE 2 — Pengembangan Fitur (continuous execution)

Sumber ide: audit modul + quick wins revenue/retensi. Aturan: tambah, jangan ubah logika inti; migrasi aditif bila perlu dan didokumentasikan.

## P1. Timesheet → Invoice 1-klik [x] DONE
- `POST time-entries/invoice` (`ids[]`): validasi klien sama + billable + belum ditagih → draft invoice, tandai `invoiced`/`invoice_id`. Transaksional, workspace-scoped.
- UI: checkbox + tombol di `time-entries/index`.

## P2. Quote → DP invoice (persen) [x] DONE
- Migrasi aditif: `invoices.quote_conversion_type` string default 'full' (idempotency full vs DP).
- `QuoteConversionService::convertDownPayment($quote, $percent, $actor)`: 1–99%, 1 baris "DP x% dari Quote …", total = round(quote_total * p).
- Route `POST quotes/{quote}/down-payment`, tombol + input persen di `quotes/show` (hanya accepted). Idempoten 1 DP per revisi.

## P3. Cashflow: outflow + net [x] DONE
- Rata-rata expense 3 bulan terakhir → outflow mingguan; kolom net per minggu + total net. Murni tampilan dari data ada.

## P4. Saran match per-baris di rekonsiliasi [x] DONE
- `Matcher::suggest(line, limit 3)`: referensi > nominal+tanggal > nama klien di deskripsi; tampilkan alasan di show view + tombol pilih cepat.

## P5. Template pesan WhatsApp [x] DONE
- 4 template (tagihan baru / H-3 / overdue / kwitansi lunas) + picker di preview view; tanpa ubah kontrak kirim/log.

## Tes
- `tests/Feature/Phase2Test.php`: 7 passed. Full suite: 160 passed / 576 assertions. `npm run build`: sukses.
