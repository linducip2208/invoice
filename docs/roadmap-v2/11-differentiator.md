# Differentiator & Wow Features — Sprint 6 + 7

> Fitur yang ngebedain Invocely dari "yet another invoice tool". Bisa jadi marketing hook utama.

---

## 11.1 E-Signature pada Estimate / Invoice ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 2 hari

### Why
Klien setuju quote → langsung tanda tangan digital di link → otomatis convert ke invoice. Lebih cepat dari kirim PDF + tanda tangan basah + scan.

### Database Schema
**Tabel BARU `signatures`** (lihat 01-DATABASE-RELATIONS Section 23).

### Routes
```
GET  /q/{token}/sign                  public.quotes.sign
POST /q/{token}/sign                  public.quotes.sign-store

GET  /i/{token}/sign                  public.invoices.sign
POST /i/{token}/sign                  public.invoices.sign-store

GET  /signatures/{signature}/verify   signatures.verify    (public verification page)
```

### Implementation
- Frontend: `signature_pad` JS library (canvas-based)
- Atau type-to-sign (input nama, render ke font cursive)
- Hash document state (canonical JSON of quote/invoice) → SHA-256 → store sebagai `verification_hash`

### Service
- `App\Services\Signature\SignatureCapture`
  - `capture($subject, $signerName, $signerEmail, $signatureBlob): Signature`
  - Validate IP + UA logged
  - Generate hash dari document state (including line items, totals)
- `App\Services\Signature\SignatureVerifier`
  - `isValid(Signature): bool` — cek apakah document state berubah setelah signed

### View
- `resources/views/public/quotes/sign.blade.php`
  - Display quote
  - Canvas signature pad
  - Tickbox "Saya setuju dengan terms ini"
  - Submit → tampil "Tanda tangan tersimpan" dengan verification link
- `resources/views/signatures/verify.blade.php`
  - Show signature image, signer info, signed timestamp, document hash
  - Status: ✓ Document utuh / ⚠️ Document changed sejak signed

### Edge Cases
- Document edited setelah signed → flag `signature.is_valid=false`, prompt re-sign
- Signature pad gagal di mobile (small screen) → fallback type-to-sign
- Signer pakai akun bukan miliknya → IP + UA bisa jadi evidence (limited; advanced: pakai email magic link untuk auth)

### Acceptance Criteria
- [ ] Klien tanda tangan via mobile/desktop
- [ ] Signature image disimpan
- [ ] Document hash untuk verifikasi keutuhan
- [ ] Verification page public
- [ ] Quote signed → auto-convert ke invoice (configurable)

---

## 11.2 Pay-by-Link Email Embed ❌

**Status:** ❌ | **Priority:** 🔥 Critical | **Effort:** 1 hari

### Why
Standar di kompetitor Stripe/Wave: tombol "Pay Now" di email langsung. Klik = checkout page, no account needed. Reduce friction = faster payment.

### Implementation
Modifikasi `resources/views/emails/invoice.blade.php`:

```html
<a href="{{ route('public.invoice.show', ['token' => $invoice->public_token, 'pay' => 1]) }}"
   style="display:inline-block; background:#A78BFA; color:#fff; padding:14px 32px; border-radius:12px; text-decoration:none; font-weight:bold;">
    💳 Bayar Sekarang
</a>
```

Di `PublicInvoiceController::show()`, kalau query `?pay=1` → auto-scroll ke section payment + auto-trigger active payment gateway redirect.

### Routes (existing reuse)
- `/i/{token}` already public-able
- Add query handling

### View
Tambahin section payment di `resources/views/invoices/public.blade.php`:
- Active payment gateways listed (radio button)
- Tombol "Lanjut Bayar" → redirect ke gateway URL

### Edge Cases
- Invoice paid → tombol disabled + show paid status
- Multiple gateways aktif → klien pilih
- 1 gateway → auto-redirect

### Acceptance Criteria
- [ ] Tombol bayar di email working
- [ ] Auto-redirect ke gateway active
- [ ] Webhook update status setelah bayar

---

## 11.3 Public Comment / Q&A Thread di Invoice ❌

**Status:** ❌ (depend on 6.3) | **Priority:** 💎 Medium | **Effort:** included in 6.3

(Sudah dispek di [06-collaboration.md § 6.3](06-collaboration.md#63-comment-thread-di-invoice). Listed di sini untuk completeness — sebagai differentiator: klien bisa langsung tanya di link publik tanpa email.)

---

## 11.4 NPS Post-Payment ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 1 hari

### Why
Klien baru bayar → "Bagaimana experience kerja sama dengan {{workspace}}? 0-10". Loop feedback untuk owner.

### Database Schema
**Tabel BARU `nps_responses`** (lihat 01-DATABASE-RELATIONS Section 16).

### Routes
```
GET  /nps/{token}                     nps.show
POST /nps/{token}                     nps.submit
GET  /reports/nps                     reports.nps           (workspace owner view)
```

### Service
- `App\Services\Nps\NpsTrigger::triggerForPayment(Payment $p)`
  - Saat payment marks invoice paid, generate token + link
  - Schedule email H+1 setelah paid
  - Email berisi link `/nps/{token}` 1-tap rating
- `App\Services\Nps\NpsAnalyzer::score($workspace, $period): array`
  - Calculate NPS = %promoter (9-10) - %detractor (0-6)

### View
- `resources/views/public/nps/show.blade.php`
  - Big 0-10 buttons
  - Optional comment box
  - Submit → thank you
- `resources/views/reports/nps.blade.php`
  - NPS score (dari -100 ke +100)
  - Trend chart 12 bulan
  - Recent comments dengan score

### Edge Cases
- Token expired (>30 hari) → reject
- Already submitted → display "Terima kasih sudah memberi rating"
- Workspace bisa opt-out lewat settings

### Acceptance Criteria
- [ ] Auto-email NPS link H+1 paid
- [ ] Klien rating 1-tap
- [ ] Owner dashboard NPS
- [ ] Trend chart

---

## 11.5 White-Label Custom Domain (CNAME) ❌

**Status:** ❌ | **Priority:** ⭐ High (premium plan only) | **Effort:** 3 hari

### Why
Agency klien serius mau invoice di-host di `invoice.brandmereka.com`. Premium feature, lock-in factor besar.

### Database Schema
**Tabel BARU `custom_domains`** (lihat 01-DATABASE-RELATIONS Section 15).

### Routes
```
GET    /workspace/custom-domain                 custom-domain.show
POST   /workspace/custom-domain                 custom-domain.store
POST   /workspace/custom-domain/verify          custom-domain.verify
DELETE /workspace/custom-domain                 custom-domain.destroy
```

### Implementation
1. User input hostname → save with status='pending'
2. Show DNS instructions:
   ```
   CNAME  invoice.brand.com  →  cname.invocely.app
   TXT    _invocely-verify   →  invocely-verify=abc123def456
   ```
3. User setup DNS at registrar
4. User click "Verify" → backend cek DNS via PHP `dns_get_record` → status='verified'
5. SSL otomatis via Caddy / Traefik / nginx with on-demand TLS (Let's Encrypt)
6. Setup catch-all subdomain di reverse proxy: setiap req ke `*.invocely.app` atau custom domain → route based on `Host` header → match `custom_domains.hostname` → load workspace

### Reverse Proxy Setup (Caddy example)
```caddy
*.invocely.app, https:// {
    @custom expression {http.request.host} != "invocely.app"
    handle @custom {
        reverse_proxy app:8000 {
            header_up Host {http.request.host}
            header_up X-Custom-Domain {http.request.host}
        }
    }
}
```

App middleware `ResolveCustomDomain` cek `request->getHost()` → kalau match `custom_domains` → set workspace context + render branded view.

### Plan Gating
- Plan: Business+ only
- Free/Pro: tampil upgrade prompt

### Edge Cases
- DNS belum propagate → verify fail, user retry
- SSL renewal failed → notify, keep using old cert sampai 7 hari grace
- Hostname conflict (1 hostname, 2 workspace) → unique constraint reject

### Acceptance Criteria
- [ ] CNAME setup berjalan
- [ ] SSL otomatis
- [ ] Verify DNS button
- [ ] Branded URL serves workspace's invoice

---

## 11.6 Invoice Watermark per Status ❌

**Status:** ❌ | **Priority:** 🌱 Low | **Effort:** 0.5 hari

### Why
Saat ini cuma ada watermark "FREE TIER" untuk plan free. Tambahin status-based watermark di PDF:
- DRAFT → biar ga keliru kirim ke klien
- PAID → stempel hijau "LUNAS" + tanggal
- OVERDUE → stempel merah "TERLAMBAT"
- CANCELLED → stempel "DIBATALKAN"

### Implementation
Modifikasi `app/View/Components/FreeTierWatermark.php` jadi `InvoiceWatermark` yang generic:
```blade
<x-invoice-watermark :invoice="$invoice" />
```

Kalau invoice.status='draft' → big diagonal text "DRAFT" 60% opacity
Kalau status='paid' → stempel hijau dengan checkmark
dst.

### Acceptance Criteria
- [ ] Watermark per status
- [ ] Customizable di workspace settings (turn off / on)
- [ ] Tampil di PDF dan public view

---

## 11.7 Bilingual Invoice (Side-by-Side) ❌

**Status:** ❌ | **Priority:** 🌱 Low | **Effort:** 1.5 hari

### Why
Klien luar (China/Japan/Korea) — invoice 2 bahasa side by side: Indonesian + English. Lebih premium dari translate (lihat 5.5) yang ngeganti language.

### Implementation
- Tambah field di invoice settings: `bilingual_locale ENUM(null,'en','zh','ja','ko')`
- Saat render PDF, layout 2 kolom: ID kiri / target kanan
- Pakai AI translator (5.5) atau user fill manual

### Acceptance Criteria
- [ ] Toggle bilingual mode per invoice
- [ ] PDF 2-column layout
- [ ] AI auto-fill translation (optional)

---

## 11.8 Auto Invoice Number Customization ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 1 hari

### Why
User minta format invoice number complex: `INV/2026/05/001` atau `BRAND-FY26-Q2-NNN`. Default sekarang flat `INV-001`.

### Database Schema
Modifikasi `workspaces`:
- `invoice_number_format VARCHAR(100) DEFAULT 'INV-{seq:000}'`

Token support:
- `{seq}` → next_invoice_no
- `{seq:000}` → padded 3 digit
- `{year}` → 4-digit year
- `{year:2}` → 2-digit
- `{month}` → 01-12
- `{month_short}` → Jan/Feb
- `{quarter}` → Q1-Q4
- `{client_code}` → first 3 char client name uppercase
- `{prefix}` → workspace.invoice_prefix

### Service
- `App\Services\InvoiceNumberGenerator::next($workspace, ?Client): string`
  - Parse format, replace tokens
  - Increment `next_invoice_no` atomically (DB lock)
  - Return generated number
  - Validate uniqueness, retry if collision

### Edge Cases
- Format mengandung year/month → reset counter setiap year/month? → optional via `reset_period ENUM('never','monthly','yearly') DEFAULT 'never'`
- Custom format invalid (token typo) → fall back ke default + warn user

### Acceptance Criteria
- [ ] User edit format di settings
- [ ] Preview real-time
- [ ] Reset period option
- [ ] Backward compat: existing invoice numbers preserved

---

## 11.9 Marketplace / Template Gallery ❌

**Status:** ❌ | **Priority:** 🌱 Low (long-term) | **Effort:** 5 hari

### Why
User community share invoice template / layout / email template / contract template. Dibrowse + 1-klik install.

### Database Schema
Tabel BARU `marketplace_items`:
```sql
id BIGINT PK
type ENUM('invoice_template','email_template','contract_template','workflow')
name VARCHAR(120)
slug VARCHAR(120) UNIQUE
description TEXT
preview_image_path VARCHAR(255)
content JSON  -- the actual template/layout
author_user_id BIGINT FK→users
download_count INT DEFAULT 0
rating_avg DECIMAL(3,2) DEFAULT 0
is_featured BOOLEAN DEFAULT FALSE
is_published BOOLEAN DEFAULT FALSE
price_idr INT DEFAULT 0  -- 0 = free
created_at, updated_at
```

### Acceptance Criteria
- [ ] User browse public templates
- [ ] Filter by type, rating, free/paid
- [ ] Install ke workspace 1-klik
- [ ] Author bisa publish & monetize

---

## 11.10 AI-Generated Invoice from Email ❌

**Status:** ❌ | **Priority:** 🌱 Low (long-term) | **Effort:** 4 hari

### Why
Klien forward email "Saya butuh design landing page, budget Rp 5jt, deadline 14 hari" → AI parse → bikin invoice draft otomatis.

### Implementation
- Inbound email seperti 8.5 (forward → AI extract → entity create)
- Tapi tujuan ke `invoices+ws123@invocely.app`
- Extract: client name (dari sender), description, amount, due_date
- Create draft invoice → notify user untuk review

### Dependency
- Need 8.5 inbound email infrastructure
- Need AI provider (5.0)

### Acceptance Criteria
- [ ] Forward email ke specific address
- [ ] AI extract → draft invoice
- [ ] User review + send
