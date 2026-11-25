# FASE 11 — Konversi USD→IDR + Setting kurs (continuous execution)

## K1. Setting kurs di aplikasi [x] DONE
- `app_settings` group `fx`: mode Otomatis/Manual + `fx.manual_USD_IDR` + tombol refresh.
- UI di Superadmin → Settings: kurs berjalan + sumber (live/manual) + tanggal + form simpan.
- `CurrencyRateService`: prioritas manual-Setting > .env `CURRENCY_RATE_*` > API Frankfurter (fallback open.er-api, cache 12 jam) > manual fallback. Offline → null (angka asli tak tersentuh).

## K2. Tampil "setara Rp" [x] DONE
- MRR USD superadmin → "≈ Rp … (kurs …)".
- Detail invoice non-IDR → "Setara Rp (kurs hari ini)".

## Tes
- `tests/Feature/Phase11Test.php`: 5 passed (live fake, manual tanpa API, offline null, simpan setting, render MRR).
