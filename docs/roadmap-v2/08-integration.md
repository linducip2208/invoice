# Integration & API — Sprint 4

> Buka ekosistem: API public, webhook, accounting export, Zapier connector. Distribusi melalui ekosistem 3rd party.

---

## 8.1 Public REST API + Token 🟦

**Status:** 🟦 (tabel `workspace_api_tokens` ada) | **Priority:** ⭐ High | **Effort:** 3 hari

### Why
Power user ingin integrate Invocely ke app sendiri (CRM, ERP, custom dashboard). Tanpa API public, mereka pindah ke kompetitor.

### Database Schema
**Tabel `workspace_api_tokens` SUDAH ADA**.

### Routes (API)
Prefix `/api/v1/`, middleware `auth:sanctum_workspace` (custom guard berbasis `workspace_api_tokens`).

```
GET    /api/v1/clients              api.clients.index
POST   /api/v1/clients              api.clients.store
GET    /api/v1/clients/{id}         api.clients.show
PUT    /api/v1/clients/{id}         api.clients.update
DELETE /api/v1/clients/{id}         api.clients.destroy

GET    /api/v1/products             api.products.*
GET    /api/v1/invoices             api.invoices.*    (CRUD)
POST   /api/v1/invoices/{id}/send   api.invoices.send
POST   /api/v1/invoices/{id}/pay    api.invoices.record-payment

GET    /api/v1/payments             api.payments.*
GET    /api/v1/quotes               api.quotes.*
GET    /api/v1/recurring            api.recurring.*

GET    /api/v1/reports/aging        api.reports.aging
GET    /api/v1/reports/revenue      api.reports.revenue

GET    /api/v1/me                   api.me            (token info)
```

### Routes (UI)
```
GET    /workspace/api-tokens               api-tokens.index
POST   /workspace/api-tokens               api-tokens.store    (returns plain token ONCE)
DELETE /workspace/api-tokens/{token}       api-tokens.destroy
PUT    /workspace/api-tokens/{token}       api-tokens.update    (rename, abilities)
```

### Auth Guard
- `App\Auth\WorkspaceTokenGuard` — implement `Guard` interface
- Decode header `Authorization: Bearer wsk_xxxxxxxxxxxx`
- Lookup `workspace_api_tokens` by hash → load workspace + user
- Scope global `WorkspaceScope` apply pakai workspace dari token

### Token Format
- `wsk_<12 char prefix>_<48 char random>` — prefix tampil di UI, secret hanya tampil sekali saat create

### View
- `resources/views/api-tokens/index.blade.php` — list tokens dengan prefix, last_used_at
- `resources/views/api-tokens/created.blade.php` — show full token sekali (`wsk_abc123_xyz...`), warning untuk save

### API Documentation
- Generate OpenAPI 3.0 spec via `darkaonline/l5-swagger`
- Serve di `/docs/api`
- Docs auto-update saat controller ada annotation

### Edge Cases
- Token leaked → user revoke via UI, semua request 401
- Rate limit per token: 1000 req/hour default, bisa di-bump per token
- Abilities (scope) — JSON array di token: `["read", "write"]` atau granular `["invoices.create", "invoices.update"]`
- Pagination via `?page=1&per_page=50` (max 100)

### Acceptance Criteria
- [ ] CRUD API untuk semua entity utama
- [ ] Bearer auth via header
- [ ] Pagination konsisten
- [ ] Error responses standar (RFC 7807 problem details)
- [ ] Rate limit + last_used_at tracking
- [ ] OpenAPI spec auto-generated
- [ ] Docs page `/docs/api`

---

## 8.2 Outbound Webhook 🟦

**Status:** 🟦 (tabel `tenant_webhooks` ada) | **Priority:** ⭐ High | **Effort:** 1.5 hari

### Why
User mau notify external system (Slack, internal CRM, custom dashboard) saat event terjadi. Tanpa webhook, polling = inefisien.

### Database Schema
**Tabel `tenant_webhooks` SUDAH ADA**.
**Tabel BARU `webhook_deliveries`** (lihat 01-DATABASE-RELATIONS Section 20).

### Events Catalog
| Event | When |
|---|---|
| `invoice.created` | New invoice saved |
| `invoice.sent` | Email sent |
| `invoice.paid` | Status → paid |
| `invoice.overdue` | Past due_date, status still sent/partial |
| `invoice.cancelled` | Cancelled |
| `payment.created` | New payment recorded |
| `payment.deleted` | Payment removed |
| `quote.accepted` | Klien accept |
| `quote.converted` | → Invoice |
| `client.created` | New client |
| `recurring.generated` | Recurring → new invoice |
| `subscription.upgraded` | Plan change |
| `credit_note.applied` | Applied |

### Routes
```
GET    /workspace/webhooks                 webhooks.index
POST   /workspace/webhooks                 webhooks.store
PUT    /workspace/webhooks/{webhook}       webhooks.update
DELETE /workspace/webhooks/{webhook}       webhooks.destroy
POST   /workspace/webhooks/{webhook}/test  webhooks.test    (fire test ping)
GET    /workspace/webhooks/{webhook}/deliveries  webhooks.deliveries  (history)
POST   /workspace/webhooks/deliveries/{delivery}/retry  webhooks.deliveries.retry
```

### Service
- `App\Services\Webhook\WebhookDispatcher`
  - `dispatch($workspaceId, $event, $payload)` — dispatched async via queue
  - Sign payload: `X-Webhook-Signature: sha256=...` HMAC dengan webhook.secret
  - HTTP POST → URL → record delivery row
  - Retry: 1, 5, 15, 60, 240 menit (exponential, max 5 attempts)
- `App\Listeners\Webhook\*Listener` — listen events di app, dispatch via WebhookDispatcher

### Payload Format
```json
{
  "event": "invoice.paid",
  "ts": 1746694800,
  "workspace_id": 123,
  "data": {
    "invoice": { "id": 456, "invoice_no": "INV-001", "total": 5000000, ... },
    "payment": { "id": 789, "amount": 5000000, ... }
  }
}
```

### Edge Cases
- Webhook URL down → retry exponential, mark as `exhausted` after max attempts
- Slow endpoint (>15 detik) → timeout, count as failure
- Auto-disable webhook kalau 10 failures berturut-turut → notif user
- Retry replay payload yang sama (idempotent) — provide `delivery_id` untuk dedup

### Acceptance Criteria
- [ ] CRUD webhook
- [ ] Pilih events to subscribe (multi-select)
- [ ] Test ping
- [ ] History deliveries dengan request/response
- [ ] Retry button
- [ ] Auto-disable failing webhook

---

## 8.3 Zapier Connector 🔒

**Status:** 🔒 (Blocked by 8.1 API) | **Priority:** 💎 Medium | **Effort:** 4 hari

### Why
Zapier → 5000+ apps. User connect Invocely tanpa coding. Distribusi gratis di Zapier marketplace.

### Required from Invocely Side
1. **Triggers** (events Zapier listen):
   - New Invoice, Invoice Paid, New Client, New Payment
2. **Actions** (Zapier call to Invocely):
   - Create Invoice, Create Client, Add Payment, Send Invoice

### Implementation
- Bukan code di Invocely — Zapier app definition di Zapier Developer Platform
- Pakai API yang sudah ada (Sprint 4.1)
- Pakai webhook untuk triggers (Sprint 4.2)
- Submit app ke Zapier untuk review (proses 2-4 minggu)

### Steps
1. Daftar Zapier developer account
2. Bikin app via Zapier CLI / UI
3. Define triggers (subscribe via webhook URL)
4. Define actions (call API endpoints)
5. Test via Zapier private mode
6. Submit for public listing

### Files
- Zapier app code di repo terpisah, bukan di Invocely repo
- Reference URL pakai env: `INVOCELY_API_BASE`

### Acceptance Criteria
- [ ] App live di Zapier marketplace
- [ ] 4 triggers + 4 actions working
- [ ] Auth via API token (already implemented)
- [ ] Documentation step-by-step di docs

---

## 8.4 Accounting Software Export ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 2 hari

### Why
User accountant pakai Mekari Jurnal / QuickBooks / Xero — mereka mau import data dari Invocely. Tanpa export terstandar, mereka manual input ulang.

### Database Schema
**Tabel BARU `accounting_exports`** (lihat 01-DATABASE-RELATIONS Section 18).

### Supported Formats
| Format | Description |
|---|---|
| `jurnal` | CSV format Mekari Jurnal (faktur penjualan, pembayaran) |
| `mekari_klikpajak` | XML format Klikpajak |
| `quickbooks` | IIF format QuickBooks Desktop |
| `xero` | CSV format Xero |
| `csv_generic` | Format generic dengan kolom configurable |

### Routes
```
GET  /accounting-exports                    accounting-exports.index
GET  /accounting-exports/create             accounting-exports.create
POST /accounting-exports                    accounting-exports.store     (generate)
GET  /accounting-exports/{export}/download  accounting-exports.download
DEL  /accounting-exports/{export}           accounting-exports.destroy
```

### Service
- `App\Services\Accounting\Exporters\` directory
  - `JurnalExporter`, `QuickbooksExporter`, `XeroExporter`, `MekariExporter`, `GenericCsvExporter`
- `App\Services\Accounting\ExportRunner::run($format, $start, $end): string`

### View
- `resources/views/accounting-exports/index.blade.php` — list past exports
- `resources/views/accounting-exports/create.blade.php` — pilih format + date range + entities

### Acceptance Criteria
- [ ] Generate file format target
- [ ] Verify import sukses di tools target (manual test)
- [ ] Re-download file existing
- [ ] Schedule auto-export bulanan (subscription pattern, lihat 7.7)

---

## 8.5 Email Forward → Expense ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 3 hari

### Why
Vendor kirim invoice via email → user forward ke `expenses+ws123@invocely.app` → AI extract → auto-create expense draft. Power feature.

### Database Schema
**Tabel BARU `email_inbox_forwards`** (lihat 01-DATABASE-RELATIONS Section 19).

### Implementation
1. Setup **inbound email** (pakai Postmark Inbound, Mailgun Routes, atau SES + S3)
2. Email masuk → webhook ke `/webhooks/email-inbox` di Invocely
3. Parse: lookup workspace via `+ws{id}` di alamat
4. Save raw email + attachment
5. Trigger OCR job kalau attachment PDF/image (lihat 5.2)
6. Create draft `expense` dengan ai_extracted data
7. Notify user via app_notifications + email

### Routes
```
POST /webhooks/email-inbox             webhooks.email-inbox      (provider callback, no auth, signature-verified)
GET  /email-forwards                   email-forwards.index      (history)
GET  /email-forwards/{forward}         email-forwards.show
POST /email-forwards/{forward}/convert email-forwards.convert    (manual trigger)
```

### Setup
- Workspace settings → tampil alamat unik: `expenses+ws123@invocely.app` (copy-paste UI)
- User bisa whitelist email pengirim (default: terima semua, security: cuma list whitelist)

### Edge Cases
- Spam → drop with log
- Email tanpa attachment → tetap save sebagai expense kosong (draft)
- Duplicate email (same Message-ID) → dedup
- Vendor pakai forward chain → cek attachment paling akhir

### Acceptance Criteria
- [ ] Inbound email parsed
- [ ] Attachment OCR'd
- [ ] Draft expense auto-created
- [ ] User review & approve

---

## 8.6 Embed / iFrame Widget ❌

**Status:** ❌ | **Priority:** 🌱 Low | **Effort:** 1.5 hari

### Why
User punya website/portal klien → embed widget "Lihat Invoice Saya" jadi inline iframe atau javascript snippet.

### Routes
```
GET /embed/invoice/{token}         embed.invoice    (X-Frame-Options: ALLOWALL untuk domain whitelist)
GET /widget/invocely.js            embed.widget-js  (JS snippet client-side)
```

### Implementation
- Mode 1: iframe → halaman `/i/{token}` dengan layout minimal
- Mode 2: JS widget → fetch via API, render in user's site

### Workspace Settings
- `widget_allowed_domains JSON` — whitelist domain (untuk CSP / X-Frame-Options)

### Acceptance Criteria
- [ ] Iframe embed-able
- [ ] JS widget dengan customizable styling
- [ ] CSP / domain whitelist
- [ ] Demo page di `/embed/demo`
