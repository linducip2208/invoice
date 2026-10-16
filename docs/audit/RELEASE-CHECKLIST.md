# RELEASE CHECKLIST — syarat naik ke READY (revisi 2026-10-10)

## Sudah hijau (bukti di TEST-EVIDENCE.md)

- [x] 144 automated tests (513 assertions) + 84 E2E browser serial tanpa console
  error (rerun penuh 2026-10-16 ronde 4, bukti di REDESIGN-TEST-RESULTS.md)
- [x] Tabler 100% UI aktif; sidebar 44/44 route PASS; /docs akurat + quotations
- [x] Quote approval 50/52 + E2E flows; isolasi tenant; webhook replay/amount/signature-code
- [x] Backup + restore teruji; migrasi quote rollback/re-apply bersih; `npm audit` 0
- [x] CSRF terbukti (curl 419); rate limiters per-route+IP; secret tak bocor ke log

## Wajib sebelum production publik

- [ ] B1/B2: isi secret gateway per workspace + verifikasi signature di sandbox
- [ ] B3: SMTP prod + worker antrean; uji kirim invoice nyata ke mailbox uji
- [ ] PWA install di Android + iOS nyata; start_url/scope di HTTPS prod
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `LICENSE_DEV_BYPASS=false`,
      `APP_URL=https://…`, `SESSION_SECURE_COOKIE=true`, `MAIL_FROM_*` resmi
- [ ] Migrate staging dari DB kosong; `storage:link`; cron + worker;
      backup harian mysqldump (password via env) + uji restore MySQL
- [ ] Rotasi password superadmin/demo; hapus akun demo bila tak perlu
- [ ] Rate-limit review: tambah throttle tenant-API bila traffic tinggi

## Operasional

- [ ] Monitor `/up`; awasi log `webhook_unverified`/`invalid signature` 7 hari
- [ ] Plafon late-fee; NPWP tidak diklaim resmi DJP; R1–R12 di REMAINING-RISKS.md
