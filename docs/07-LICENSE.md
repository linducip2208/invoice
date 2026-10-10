# License Pairing v3 — Invocely

> Source code Invocely di-protect oleh **License Client Kit v3 (Pairing Edition)** dari [whitelabel.co.id](https://whitelabel.co.id). Setiap copy yang dijual harus di-pair ke domain customer sebelum bisa diakses.

**Last Updated:** 2026-05-07
**Kit Version:** v3 (Pairing)
**Status:** ✅ Installed & verified (3 routes registered)

---

## 🔧 Yang Sudah Ter-Install

```
app/Http/Middleware/RequirePair.php          ← block semua route kalau belum paired
app/Http/Controllers/PairController.php      ← handler wizard
app/Services/LicenseClient.php               ← komunikasi ke whitelabel.co.id API
config/license.php                           ← konfigurasi
public/marketplace.public.pem                ← RSA-2048 public key (verify signed payloads)
resources/views/license/pair-wizard.blade.php
resources/views/license/pair-success.blade.php
routes/pair-routes.php                       ← /__pair, /__pair POST, /__pair/success
```

Plus:
- `bootstrap/app.php` → `RequirePair` di-registered di web middleware
- `routes/web.php` → `require base_path('routes/pair-routes.php')`
- `.env` → `LICENSE_SERVER_URL=https://whitelabel.co.id` + `LICENSE_DEV_BYPASS=true`
- `.gitignore` → `storage/app/.license.lock` (encrypted lock file, never commit)

---

## 🎯 Customer Flow

Customer beli source code di `whitelabel.co.id/marketplace` → dapat activation key (format `XXXXX-XXXXX-XXXXX-XXXXX`).

1. Upload source code ke server, setup web server + `.env`
2. Buka domain mereka di browser
3. **Wizard `/__pair` muncul otomatis** (semua route lain di-block oleh `RequirePair` middleware)
4. Paste activation key dari `whitelabel.co.id/user/licenses`
5. Klik **Aktivasi** → success page tampilkan nama produk & versi
6. `storage/app/.license.lock` di-generate (encrypted, signed) → app accessible normal
7. Heartbeat otomatis tiap 24 jam (grace 7 hari kalau marketplace down)

---

## 🛡️ Security Model

| Layer | Mekanisme |
|---|---|
| **Activation** | Activation key di-validate ke API `https://whitelabel.co.id/api/license/activate` dengan `domain` |
| **Lock file** | Response API ditandatangani RSA-2048 dengan `marketplace.private.pem`; di-verify lokal pakai `marketplace.public.pem` (embed di kit) |
| **Persistence** | `.license.lock` di-encrypt pakai Laravel APP_KEY; isi: license_key, domain, product, expires_at, signature |
| **Domain binding** | License terikat ke domain — pindah server harus revoke dulu di `/user/licenses` |
| **Heartbeat** | Tiap 24 jam ping `/api/license/validate`; kalau `revoked` atau `expired` → app block lagi |
| **Offline grace** | Kalau marketplace unreachable, lock file masih valid 7 hari (offline checksum verify) |

---

## ⚙️ Environment Configuration

### Local Development (default — saat sekarang)

```env
APP_ENV=local
LICENSE_SERVER_URL=https://whitelabel.co.id
LICENSE_DEV_BYPASS=true
```

→ License check **di-skip otomatis** kalau host = `localhost`, `127.0.0.1`, `*.test`, `*.localhost`.
→ `php artisan serve` jalan normal tanpa harus aktivasi.

### Production (saat customer deploy)

```env
APP_ENV=production
LICENSE_SERVER_URL=https://whitelabel.co.id
LICENSE_DEV_BYPASS=false
LICENSE_HEARTBEAT_INTERVAL=86400  # 24h
LICENSE_HEARTBEAT_GRACE=604800    # 7d
```

→ Wajib pairing. Tanpa lock file = redirect ke `/__pair`.

---

## 🚀 Workflow Saat Mau Jualan

1. Tag versi baru di marketplace whitelabel.co.id, set harga
2. Zip project Invocely **tanpa** `vendor/`, `node_modules/`, `storage/app/.license.lock`, `.env`
   ```bash
   zip -r invocely-v1.0.zip . -x "vendor/*" "node_modules/*" "storage/app/.license.lock" ".env" ".git/*"
   ```
3. Upload zip ke marketplace whitelabel.co.id
4. Customer beli → dapat email berisi activation key
5. Customer download → install → buka domain → wizard muncul → paste key → ✅

---

## 🔍 Bypass / Test di Lokal

Sudah otomatis bypass di local dev (`APP_ENV=local` + dev hosts). Kalau mau **test wizard di lokal**:

```bash
# Set .env sementara
APP_ENV=production
LICENSE_DEV_BYPASS=false

php artisan config:clear
php artisan serve

# Buka http://127.0.0.1:8000
# → akan redirect ke /__pair otomatis
# Paste activation key dari https://whitelabel.co.id/user/licenses
```

Jangan lupa restore ke `local` setelah testing.

---

## 🆘 Troubleshooting

| Gejala | Solusi |
|---|---|
| Wizard tidak muncul di production | Cek `APP_ENV=production`, `LICENSE_DEV_BYPASS=false`, dan `bootstrap/app.php` ada `RequirePair::class` |
| "Invalid signature" di pair | Public key `public/marketplace.public.pem` rusak/missing — re-copy dari kit |
| "Domain limit reached" | Customer pakai key yang sudah di-pair ke domain lain. Revoke dulu di `/user/licenses` |
| Heartbeat fail terus | Cek server bisa hit `https://whitelabel.co.id/api/license/validate` (firewall, DNS) |
| Lock file corrupt | Hapus `storage/app/.license.lock` → wizard muncul lagi → re-pair |

---

## 📚 Reference

- Kit source: `D:\project laravel\whitelabel\whitelabel\Pseo\license-client-kit-v3\`
- Design doc: `D:\project laravel\whitelabel\whitelabel\docs\LICENSE_PAIRING_DESIGN.md`
- API docs: `D:\project laravel\whitelabel\ini license kode penting.txt`
- Marketplace dashboard: https://whitelabel.co.id/admin
