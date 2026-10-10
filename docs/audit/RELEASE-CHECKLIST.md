# RELEASE CHECKLIST — syarat naik ke READY

## Wajib sebelum production publik

- [ ] B1/B2: konfigurasi secret gateway + verifikasi signature webhook payment & billing, uji replay + amount-mismatch di sandbox
- [ ] B3: SMTP prod, uji kirim invoice + reminder + reset password, worker antrean jalan
- [ ] B5: E2E browser (desktop + mobile 360px): login → invoice → bayar → webhook → laporan, tanpa console error
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `LICENSE_DEV_BYPASS=false`, `APP_URL=https://…`, `SESSION_SECURE_COOKIE=true`, `MAIL_FROM_*` resmi
- [ ] `php artisan migrate --force` di staging dari DB kosong + rollback test di DB uji
- [ ] `php artisan storage:link`, cron `schedule:run`, queue worker (Supervisor/systemd), backup harian + uji restore
- [ ] Rate limit login/API, rotasi password superadmin/demo, hapus akun demo bila tidak perlu
- [ ] Unique constraint `gateway_transaction_id` / `external_invoice_id` setelah data bersih (D3)

## Operasional

- [ ] Health `/up` dimonitor; log webhook diawasi 7 hari pertama
- [ ] Plafon late-fee ditetapkan (R1); NPWP tidak diklaim resmi DJP (R4)
- [ ] PWA diuji install di Android + iOS nyata (R9)
