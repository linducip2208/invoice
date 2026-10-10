# Compliance & Security — Sprint 6 + 7

> Audit log, GDPR/UU PDP, 2FA, SSO, data residency. Wajib untuk enterprise.

---

## 10.1 Audit Log Full Coverage 🟦

**Status:** 🟦 (tabel ada, perlu observer pattern lengkap) | **Priority:** ⭐ High | **Effort:** 1.5 hari

(Sudah dispek di [06-collaboration.md § 6.5](06-collaboration.md#65-activity-feed-audit-log-surface) — lihat sana untuk detail.)

Yang perlu di-cover di compliance angle:
- Retention policy: keep audit_logs minimal 1 tahun, archive 5 tahun, untuk regulasi
- Read-only — tidak bisa edit/delete row (hardcoded di model: no `delete()` method, no `update()`)
- Export untuk auditor: filter date range → CSV signed (PGP optional untuk premium)

---

## 10.2 2FA / TOTP ❌

**Status:** ❌ | **Priority:** 🔥 Critical | **Effort:** 1 hari

### Why
Account takeover risk → 2FA standar. Google Authenticator / Authy / 1Password.

### Database Schema
**Tabel BARU `user_2fa_secrets`** (lihat 01-DATABASE-RELATIONS Section 13).

**Modifikasi `users`:**
- `two_factor_enabled BOOLEAN DEFAULT FALSE`

### Routes
```
GET    /profile/2fa                       2fa.show         (status + enable button)
POST   /profile/2fa/enable                2fa.enable       (generate secret, show QR)
POST   /profile/2fa/confirm               2fa.confirm      (user input code untuk verify setup)
POST   /profile/2fa/disable               2fa.disable
GET    /profile/2fa/recovery-codes        2fa.recovery-codes  (regenerate)
GET    /2fa/verify                        2fa.challenge    (intercepted setelah login)
POST   /2fa/verify                        2fa.verify
```

### Service
- `App\Services\TwoFactor\TotpService`
  - `generateSecret(): string` — base32, 32 char
  - `qrCodeUrl(string $email, string $secret): string` — `otpauth://totp/...`
  - `verify(string $secret, string $code): bool` — TOTP RFC 6238, ±30 detik tolerance
- `App\Services\TwoFactor\RecoveryCodeService`
  - `generate(): array` — 8 codes, 10 char alphanumeric
  - `verifyAndConsume(User, string $code): bool` — single-use

### Flow
1. Setup: User → /profile/2fa → enable → tampil QR (data:image PNG via `endroid/qr-code`)
2. User scan dengan Authenticator app
3. User input 6-digit code → confirm → enabled
4. Tampil 8 recovery codes (bagi user untuk save)
5. Login flow:
   - email + password → kalau 2fa enabled → redirect /2fa/verify
   - input 6-digit OR recovery code → verify → session full

### Library
- `pragmarx/google2fa` (Composer) untuk TOTP
- `endroid/qr-code` untuk QR

### Edge Cases
- Recovery codes habis → user setup ulang 2FA
- User lost device → support ticket → admin reset 2fa via DB
- Time drift di device → tolerance 30 detik OK, tapi >2 menit reject

### Acceptance Criteria
- [ ] Enable 2FA via UI dengan QR
- [ ] Login butuh 2 step
- [ ] Recovery codes untuk emergency
- [ ] Disable dengan password confirm

---

## 10.3 SSO (Google / Microsoft) ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 2 hari

### Why
Enterprise pakai Google Workspace / Microsoft 365. Login langsung pakai akun corporate = no password fatigue.

### Database Schema
**Tabel BARU `external_oauth_identities`** (lihat 01-DATABASE-RELATIONS Section 14).

### Routes
```
GET /auth/sso/{provider}/redirect       sso.redirect       (Google/Microsoft)
GET /auth/sso/{provider}/callback       sso.callback
POST /profile/sso/disconnect/{provider} sso.disconnect
```

### Library
- `laravel/socialite` + provider packages:
  - `socialiteproviders/microsoft` untuk Azure AD

### Service
- `App\Services\Auth\SsoLoginService`
  - `handleCallback($provider, $oauthUser): User`
  - Lookup `external_oauth_identities` by provider+provider_user_id
  - Kalau ada → login user
  - Kalau ga ada → cek email match existing user → link
  - Kalau email belum ada → register new user

### Workspace SSO Enforcement
- Workspace setting: `sso_required ENUM('none','google','microsoft','any') DEFAULT 'none'`
- Kalau enforced, user di workspace ini ga boleh login pakai password biasa

### Edge Cases
- User punya account email A di Google, daftar email B di Invocely → tidak auto-link, butuh manual confirm
- SSO provider down → fallback to password (kecuali enforced)

### Acceptance Criteria
- [ ] Google login working
- [ ] Microsoft login working
- [ ] Link/unlink dari profile page
- [ ] Workspace bisa enforce SSO untuk member

---

## 10.4 GDPR / UU PDP Tools ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 1 hari

### Why
UU PDP (Indonesia, sejak 2022) + GDPR (Europe) — wajib kasih user kontrol data: export semua, hapus permanen.

### Routes
```
POST /profile/gdpr/export-data        gdpr.export        (queue full export)
GET  /profile/gdpr/exports            gdpr.exports.index
GET  /profile/gdpr/exports/{export}/download  gdpr.exports.download
POST /profile/gdpr/delete-account     gdpr.delete-request
```

### Tabel BARU `data_export_requests`:
```sql
id BIGINT PK
user_id BIGINT FK ON DELETE CASCADE
status ENUM('pending','processing','ready','expired') DEFAULT 'pending'
file_path VARCHAR(255) NULL
expires_at TIMESTAMP NULL  -- auto-delete file 7 hari
requested_at TIMESTAMP
ready_at TIMESTAMP NULL
INDEX(user_id, status)
```

### Service
- `App\Jobs\GenerateDataExportJob`
  - Collect ALL user-related data: profile, workspace memberships, invoices created, audit logs
  - Bundle as ZIP (JSON files per entity)
  - Save to `storage/app/private/exports/{uuid}.zip`
  - Send email link

### Account Deletion Flow
1. User request delete
2. Confirmation email dengan link (anti-accident)
3. Click confirm → 7-day grace period (cancellable)
4. After 7 days → permanent delete:
   - User row deleted (CASCADE handles most)
   - Workspace owned by user: prompt user untuk transfer ownership atau delete workspace
   - Audit logs: keep but anonymize (user_id → NULL, name → "Deleted User")

### Acceptance Criteria
- [ ] Export ZIP berisi semua data user
- [ ] Account deletion 7-day grace
- [ ] Anonymize audit logs (preserve trail tapi PII removed)
- [ ] Email confirmation untuk delete

---

## 10.5 Data Residency (Indonesia) ❌

**Status:** ❌ | **Priority:** 🌱 Low (infra-level) | **Effort:** Significant infra

### Why
Bank/government client requirement: data tidak boleh keluar Indonesia. Infrastruktur level decision.

### Architecture Options
1. **Single-region deploy**: Server di Indonesia (Biznet, IDcloudhost). All workspace di sana.
2. **Multi-region**: Workspace pilih region saat create. Database per region. App layer routing via header/subdomain.
3. **Hybrid**: Public app di Singapore, copy data ke Indonesia mirror untuk client yang require.

### Steps
1. Pilih hosting Indonesia (recommend: BiznetGIO, AWS Jakarta region)
2. Setup S3-compatible storage Indonesia (BiznetGIO Object Storage atau AWS S3 ap-southeast-3)
3. Database: MySQL/PostgreSQL managed di Indonesia
4. CDN: Cloudflare dengan rules untuk Indonesia traffic via PoP Indonesia (Jakarta)
5. Update `app.timezone` → `Asia/Jakarta`
6. Compliance certificate: ISO 27001 (paid)

### Acceptance Criteria
- [ ] All infrastructure di Indonesia (verifiable)
- [ ] DPA template untuk client yang minta
- [ ] Latency dari Jakarta < 50ms
- [ ] Compliance docs (DPIA, data-flow map)

---

## 10.6 Encryption At Rest Audit ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 0.5 hari

### Audit Checklist
- [ ] `payment_gateways.api_key_encrypted` — confirm Laravel Crypt used
- [ ] `payment_gateways.secret_encrypted`
- [ ] `ai_providers.api_key_encrypted` — when implemented
- [ ] `external_oauth_identities.token_encrypted` — when implemented
- [ ] `user_2fa_secrets.secret_encrypted`
- [ ] `user_2fa_secrets.recovery_codes_encrypted`
- [ ] `client_portal_users.password_hash` — bcrypt (cost 12)
- [ ] `users.password` — bcrypt (cost 12) — already
- [ ] `workspace_api_tokens.token_hash` — already (SHA-256)
- [ ] Database backup encryption — check hosting

### Database-level (production)
- MySQL: `innodb_encrypt_tables=ON`
- File system: LUKS / dm-crypt

### Acceptance Criteria
- [ ] All sensitive columns encrypted via `cast` ke `encrypted` di model
- [ ] No plain-text secrets in logs (test by grep `storage/logs/`)
- [ ] No secrets in API responses (test all endpoints)
- [ ] Backup encryption verified

---

## 10.7 Brute Force Protection ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 0.5 hari

### Approach
- Login throttle: Laravel `RateLimiter` already (5 attempts / minute by default)
- Tighten: 5 attempts → 15 min lock per IP+email combo
- Captcha after 3 failed attempts (hCaptcha or Cloudflare Turnstile)
- Email alert ke user saat ada login attempt dari IP/UA baru
- Logging ke `audit_logs` untuk failed login attempts (suspicious_logins source)

### Implementation
- Use `Illuminate\Cache\RateLimiter` (already in Laravel)
- Custom middleware `LoginAttemptThrottle`

### Acceptance Criteria
- [ ] 5 attempts → 15 min lock
- [ ] Captcha after 3 fails
- [ ] Email alert "Login dari device baru"
- [ ] Audit log

---

## 10.8 Permission Granular Audit ❌

**Status:** ❌ | **Priority:** 🌱 Low | **Effort:** 1 hari

### Why
Saat 6.1 multi-user implemented, audit semua controller method punya proper authorization gate. Tanpa gate, member bisa akses fitur owner-only.

### Methodology
1. List semua controller method
2. Cek setiap method punya `authorize()` call
3. Define gates di `app/Providers/AuthServiceProvider.php`
4. Test setiap gate dengan user yang punya/tidak punya ability

### Acceptance Criteria
- [ ] 100% controller method ada authorize call
- [ ] Test coverage untuk forbid scenario
- [ ] Audit log untuk denied access attempts
