# Database Relations — Roadmap v2

> Full ERD termasuk tabel **existing (30)** + tabel **baru yang diusulkan (~25)** untuk roadmap v2.
> Semua tabel multi-tenant via `workspace_id` (kecuali `users`, `plans`, `coupons`, `affiliates`, `app_settings`).

---

## Master Entity Map

```
USERS ─┬─ workspace_user (pivot, role) ─┬─ WORKSPACES ─┬─ CLIENTS ─┬─ INVOICES ─┬─ INVOICE_ITEMS
       │                                 │              │           │            ├─ PAYMENTS
       │                                 │              │           │            ├─ INVOICE_ATTACHMENTS
       │                                 │              │           │            ├─ INVOICE_PUBLIC_VIEWS
       │                                 │              │           │            ├─ EMAIL_LOGS
       │                                 │              │           │            ├─ INVOICE_REMINDERS *
       │                                 │              │           │            ├─ CREDIT_NOTES *
       │                                 │              │           │            ├─ INVOICE_COMMENTS *
       │                                 │              │           │            └─ INVOICE_TAX_DETAILS *  (PPh23/PPN/eFaktur)
       │                                 │              │           │
       │                                 │              │           ├─ QUOTES ─── (convert) ─→ INVOICES
       │                                 │              │           ├─ TIME_ENTRIES ─→ (invoice_id)
       │                                 │              │           ├─ EXPENSES ─────→ (invoice_id, billable)
       │                                 │              │           ├─ PROJECTS *
       │                                 │              │           └─ CLIENT_PORTAL_USERS *
       │                                 │              │
       │                                 │              ├─ PRODUCTS
       │                                 │              ├─ INVOICE_TEMPLATES
       │                                 │              ├─ INVOICE_LAYOUTS
       │                                 │              ├─ RECURRING_INVOICES ─→ INVOICES (generates)
       │                                 │              ├─ PAYMENT_GATEWAYS
       │                                 │              ├─ AUDIT_LOGS
       │                                 │              ├─ APP_NOTIFICATIONS
       │                                 │              ├─ WORKSPACE_INVITES
       │                                 │              ├─ WORKSPACE_API_TOKENS
       │                                 │              ├─ TENANT_WEBHOOKS
       │                                 │              ├─ APPROVAL_RULES *
       │                                 │              ├─ WHATSAPP_LOGS *
       │                                 │              ├─ BANK_RECONCILE_BATCHES *
       │                                 │              ├─ AI_PROVIDERS *
       │                                 │              ├─ AI_USAGE_LOGS *
       │                                 │              ├─ EXTERNAL_OAUTH_CONNECTIONS *
       │                                 │              ├─ CUSTOM_DOMAINS *
       │                                 │              ├─ NPS_RESPONSES *
       │                                 │              └─ DASHBOARD_WIDGETS *
       │                                 │
       │                                 └─ SUBSCRIPTIONS ─→ PLANS
       │
       ├─ user_2fa_secrets *
       ├─ external_oauth_identities *  (Google/Microsoft SSO)
       ├─ AFFILIATES (1:1 user)
       └─ SUPPORT_TICKETS ─ SUPPORT_MESSAGES

* = NEW tables proposed in roadmap v2
```

Note: Tabel `coupons` direlasikan ke `subscriptions` via `coupon_id` (workspace-scoped use). Tabel `app_settings` global.

---

## Existing Tables (30) — Quick Reference

| # | Table | PK | Key FKs | Owner Scope |
|---|---|---|---|---|
| 1 | `users` | id | — | global |
| 2 | `workspaces` | id | — | global |
| 3 | `workspace_user` | id | workspace_id, user_id | pivot |
| 4 | `clients` | id | workspace_id | workspace |
| 5 | `products` | id | workspace_id | workspace |
| 6 | `invoice_templates` | id | workspace_id | workspace |
| 7 | `invoice_layouts` | id | workspace_id | workspace |
| 8 | `recurring_invoices` | id | workspace_id, client_id | workspace |
| 9 | `invoices` | id | workspace_id, client_id, recurring_invoice_id, invoice_layout_id | workspace |
| 10 | `invoice_items` | id | invoice_id, product_id | invoice |
| 11 | `invoice_attachments` | id | invoice_id, workspace_id | invoice |
| 12 | `invoice_public_views` | id | invoice_id | invoice |
| 13 | `payments` | id | invoice_id, workspace_id | invoice |
| 14 | `email_logs` | id | invoice_id, workspace_id | invoice |
| 15 | `payment_gateways` | id | workspace_id | workspace |
| 16 | `plans` | id | — | global |
| 17 | `subscriptions` | id | workspace_id, plan_id | workspace |
| 18 | `app_settings` | id | — | global |
| 19 | `audit_logs` | id | workspace_id, user_id | workspace |
| 20 | `app_notifications` | id | workspace_id, user_id | workspace |
| 21 | `time_entries` | id | workspace_id, client_id, user_id, invoice_id | workspace |
| 22 | `expenses` | id | workspace_id, client_id, invoice_id | workspace |
| 23 | `quotes` | id | workspace_id, client_id, converted_invoice_id | workspace |
| 24 | `coupons` | id | plan_id | global |
| 25 | `support_tickets` | id | workspace_id, user_id | mixed |
| 26 | `support_messages` | id | ticket_id, user_id | ticket |
| 27 | `affiliates` | id | user_id (unique) | user |
| 28 | `workspace_invites` | id | workspace_id, invited_by | workspace |
| 29 | `workspace_api_tokens` | id | workspace_id, user_id | workspace |
| 30 | `tenant_webhooks` | id | workspace_id | workspace |

---

## NEW Tables (Roadmap v2)

### 1. `invoice_reminders`  (Sprint 1 — Reminder Sequence)
```sql
id BIGINT PK
workspace_id BIGINT FK→workspaces ON DELETE CASCADE
invoice_id BIGINT FK→invoices ON DELETE CASCADE
schedule_offset INT  -- days relative to due_date (-3 = 3 hari sebelum, +7 = 7 hari setelah)
sent_at TIMESTAMP NULL
template_subject VARCHAR(200)
template_body TEXT
status ENUM('pending','sent','failed','cancelled') DEFAULT 'pending'
fail_reason VARCHAR(500) NULL
created_at, updated_at
INDEX(workspace_id, sent_at)
INDEX(invoice_id)
```
**Relasi:** invoice 1:N reminders. Workspace 1:N reminders.

### 2. `invoice_late_fees`  (Sprint 1 — Late Fee Otomatis)
```sql
id BIGINT PK
workspace_id BIGINT FK
invoice_id BIGINT FK ON DELETE CASCADE
applied_at TIMESTAMP
amount DECIMAL(12,2)
type ENUM('percent','fixed')
percent_value DECIMAL(5,2) NULL
fixed_amount DECIMAL(12,2) NULL
description VARCHAR(200) DEFAULT 'Late fee'
created_at, updated_at
INDEX(invoice_id)
```
**Relasi:** invoice 1:N late_fees (incremental, bisa diapply tiap minggu late). Saat applied, push ke `invoice_items` virtual atau direct increment ke `invoices.total`.

### 3. `credit_notes`  (Sprint 1 — Credit Note / Refund)
```sql
id BIGINT PK
workspace_id BIGINT FK
invoice_id BIGINT FK ON DELETE CASCADE  -- credit notes always tied to original invoice
credit_no VARCHAR(50)  -- mis. CN-2026-001
reason VARCHAR(300)
amount DECIMAL(12,2)
currency VARCHAR(3)
issued_at DATE
status ENUM('draft','issued','applied','cancelled') DEFAULT 'draft'
notes TEXT NULL
created_by BIGINT FK→users ON DELETE SET NULL
created_at, updated_at
UNIQUE(workspace_id, credit_no)
INDEX(workspace_id, issued_at)
```
**Relasi:** invoice 1:N credit_notes. Saat status=applied, reduce `invoices.balance_due`.

### 4. `invoice_comments`  (Sprint 3 — Comment Thread + Differentiator)
```sql
id BIGINT PK
workspace_id BIGINT FK
invoice_id BIGINT FK ON DELETE CASCADE
author_user_id BIGINT FK→users ON DELETE SET NULL  -- NULL = client portal author
author_client_id BIGINT FK→clients ON DELETE SET NULL  -- alt: client portal user
author_name VARCHAR(120)  -- denormalized untuk public posting
author_email VARCHAR(255)  -- denormalized
body TEXT
is_internal BOOLEAN DEFAULT FALSE  -- TRUE = cuma tim yg lihat, FALSE = visible di portal/public
parent_id BIGINT NULL FK→invoice_comments ON DELETE CASCADE  -- threaded reply
attachments_json JSON NULL  -- file paths
created_at, updated_at
INDEX(invoice_id, created_at)
```
**Relasi:** invoice 1:N comments. Comment N:1 user OR client (XOR). Self-ref untuk thread.

### 5. `invoice_tax_details`  (Sprint 2 — PPh 23 + e-Faktur)
```sql
id BIGINT PK
workspace_id BIGINT FK
invoice_id BIGINT FK ON DELETE CASCADE  -- 1:1 normally
ppn_rate DECIMAL(5,2) DEFAULT 11.00
ppn_amount DECIMAL(12,2) DEFAULT 0
pph23_rate DECIMAL(5,2) DEFAULT 0  -- 0 atau 2 (jasa) atau 4 (non-NPWP)
pph23_amount DECIMAL(12,2) DEFAULT 0
pph23_object_code VARCHAR(20) NULL  -- mis. 24-104-01 (jasa konsultan)
efaktur_status ENUM('not_applicable','draft','approved','rejected') DEFAULT 'not_applicable'
efaktur_no VARCHAR(50) NULL  -- nomor seri faktur DJP
efaktur_qr_code VARCHAR(500) NULL
efaktur_xml_path VARCHAR(255) NULL  -- path to generated XML
coretax_response_json JSON NULL  -- response dari Coretax API
created_at, updated_at
UNIQUE(invoice_id)
INDEX(workspace_id, efaktur_status)
```
**Relasi:** invoice 1:1 tax_details (kalau workspace PKP). Independent dari `invoices.tax_amount` yang lebih sederhana.

### 6. `whatsapp_logs`  (Sprint 2 — WhatsApp Send)
```sql
id BIGINT PK
workspace_id BIGINT FK
invoice_id BIGINT FK ON DELETE SET NULL
quote_id BIGINT FK→quotes ON DELETE SET NULL
to_phone VARCHAR(20)
template_name VARCHAR(100)  -- WA Business approved template
message_body TEXT
status ENUM('queued','sent','delivered','read','failed') DEFAULT 'queued'
provider_msg_id VARCHAR(120) NULL  -- ID dari provider WA
fail_reason VARCHAR(500) NULL
sent_at TIMESTAMP NULL
delivered_at TIMESTAMP NULL
read_at TIMESTAMP NULL
created_at, updated_at
INDEX(workspace_id, sent_at)
INDEX(invoice_id)
```
**Relasi:** invoice 1:N whatsapp_logs (parallel ke `email_logs`).

### 7. `bank_reconcile_batches` + `bank_reconcile_lines`  (Sprint 7 — Bank Auto-Reconcile)
```sql
-- Batch
id BIGINT PK
workspace_id BIGINT FK
bank_name VARCHAR(50)  -- BCA, Mandiri, dll (free-text, dynamic)
account_no VARCHAR(50)
file_path VARCHAR(255)
file_format ENUM('csv','ofx','mt940','json')
imported_by BIGINT FK→users ON DELETE SET NULL
total_lines INT
matched_count INT DEFAULT 0
unmatched_count INT DEFAULT 0
status ENUM('processing','review','completed') DEFAULT 'processing'
created_at, updated_at

-- Line
id BIGINT PK
batch_id BIGINT FK→bank_reconcile_batches ON DELETE CASCADE
workspace_id BIGINT FK
trx_date DATE
amount DECIMAL(12,2)
description VARCHAR(500)
matched_invoice_id BIGINT FK→invoices ON DELETE SET NULL
matched_payment_id BIGINT FK→payments ON DELETE SET NULL
match_confidence ENUM('high','medium','low','manual','none') DEFAULT 'none'
match_method ENUM('auto_amount_date','auto_reference','manual','none') DEFAULT 'none'
notes VARCHAR(500) NULL
created_at, updated_at
INDEX(batch_id)
INDEX(workspace_id, trx_date)
```
**Relasi:** workspace 1:N batch, batch 1:N lines, line N:1 invoice/payment (nullable). Saat line matched, auto-create payment record.

### 8. `projects`  (Sprint 5 — Project Tracking)
```sql
id BIGINT PK
workspace_id BIGINT FK
client_id BIGINT FK ON DELETE CASCADE
name VARCHAR(200)
slug VARCHAR(100)
description TEXT NULL
status ENUM('active','on_hold','completed','cancelled') DEFAULT 'active'
budget_amount DECIMAL(12,2) NULL  -- total project budget
budget_hours DECIMAL(8,2) NULL
hourly_rate DECIMAL(12,2) NULL
start_date DATE NULL
end_date DATE NULL
color VARCHAR(7) NULL  -- hex
created_at, updated_at
INDEX(workspace_id, status)
UNIQUE(workspace_id, slug)
```
**Relasi:** workspace 1:N projects. project N:1 client. project 1:N invoices (perlu kolom `project_id` di `invoices`), 1:N time_entries, 1:N expenses (perlu `project_id`).

**Tabel modifikasi:** add `project_id BIGINT NULL FK→projects ON DELETE SET NULL` ke `invoices`, `time_entries`, `expenses`, `quotes`.

### 9. `client_portal_users`  (Sprint 3 — Customer Portal Login)
```sql
id BIGINT PK
client_id BIGINT FK→clients ON DELETE CASCADE
workspace_id BIGINT FK  -- denormalized for fast scope check
email VARCHAR(255)
password_hash VARCHAR(255)
last_login_at TIMESTAMP NULL
remember_token VARCHAR(100) NULL
email_verified_at TIMESTAMP NULL
created_at, updated_at
UNIQUE(client_id, email)
INDEX(workspace_id)
```
**Relasi:** client 1:N portal_users (mis. 1 perusahaan punya 3 staf yang login). Auth guard `client_portal` terpisah dari `web`.

### 10. `approval_rules` + `approval_requests`  (Sprint 4 — Approval Workflow)
```sql
-- Rules: konfigurasi
id BIGINT PK
workspace_id BIGINT FK
name VARCHAR(120)
applies_to ENUM('invoice','quote','expense','credit_note')
condition_type ENUM('amount_gte','always','custom_field') DEFAULT 'amount_gte'
condition_value DECIMAL(12,2) NULL
approver_user_ids JSON  -- array of user IDs
require_all BOOLEAN DEFAULT FALSE  -- TRUE = all must approve, FALSE = any
is_active BOOLEAN DEFAULT TRUE
created_at, updated_at
INDEX(workspace_id, applies_to)

-- Request: instance per item
id BIGINT PK
workspace_id BIGINT FK
rule_id BIGINT FK→approval_rules ON DELETE SET NULL
subject_type VARCHAR(100)  -- 'invoice', 'quote', etc
subject_id BIGINT
requested_by BIGINT FK→users ON DELETE SET NULL
status ENUM('pending','approved','rejected','expired') DEFAULT 'pending'
approver_responses JSON  -- [{user_id, status, comment, responded_at}]
final_decision_at TIMESTAMP NULL
notes TEXT NULL
created_at, updated_at
INDEX(workspace_id, status)
INDEX(subject_type, subject_id)
```
**Relasi:** rule 1:N requests. Polymorphic `subject` ke invoice/quote/expense.

### 11. `ai_providers`  (Sprint 5 — AI Features Generic Adapter)
```sql
id BIGINT PK
workspace_id BIGINT FK
name VARCHAR(120)  -- user input bebas mis. "OpenAI Production"
api_format ENUM('openai_compatible','anthropic','gemini','image_generic','ocr_generic')
base_url VARCHAR(500)
api_key_encrypted TEXT  -- encrypted at rest, NEVER returned
extra_headers JSON NULL
default_model VARCHAR(100) NULL  -- preset suggestion, user editable
default_pricing_input DECIMAL(10,6) NULL  -- per 1k token input cost
default_pricing_output DECIMAL(10,6) NULL
is_active BOOLEAN DEFAULT TRUE
created_at, updated_at
INDEX(workspace_id, is_active)
```
**Relasi:** workspace 1:N providers. Tidak ada lock to a feature — user pilih provider per-feature di settings.

### 12. `ai_usage_logs`  (Sprint 5 — AI cost tracking)
```sql
id BIGINT PK
workspace_id BIGINT FK
provider_id BIGINT FK→ai_providers ON DELETE SET NULL
user_id BIGINT FK→users ON DELETE SET NULL
feature VARCHAR(50)  -- 'ocr_receipt', 'smart_description', 'predict_payment'
model VARCHAR(100)
input_tokens INT
output_tokens INT
cost_estimate DECIMAL(10,4)
duration_ms INT
status ENUM('success','error','timeout') DEFAULT 'success'
error_message VARCHAR(500) NULL
created_at, updated_at
INDEX(workspace_id, created_at)
INDEX(provider_id)
```

### 13. `user_2fa_secrets`  (Sprint 6 — 2FA / TOTP)
```sql
id BIGINT PK
user_id BIGINT FK→users ON DELETE CASCADE
secret_encrypted TEXT
recovery_codes_encrypted JSON
enabled_at TIMESTAMP NULL
last_used_at TIMESTAMP NULL
created_at, updated_at
UNIQUE(user_id)
```

### 14. `external_oauth_identities`  (Sprint 7 — SSO)
```sql
id BIGINT PK
user_id BIGINT FK→users ON DELETE CASCADE
provider ENUM('google','microsoft','apple','github')
provider_user_id VARCHAR(255)
email VARCHAR(255)
name VARCHAR(120) NULL
avatar_url VARCHAR(500) NULL
token_encrypted TEXT NULL  -- refresh token
linked_at TIMESTAMP
created_at, updated_at
UNIQUE(provider, provider_user_id)
INDEX(user_id)
```

### 15. `custom_domains`  (Sprint 7 — White-Label CNAME)
```sql
id BIGINT PK
workspace_id BIGINT FK
hostname VARCHAR(253)  -- mis. invoice.brand-mereka.com
verification_token VARCHAR(64)
verification_method ENUM('cname','txt')
status ENUM('pending','verified','failed','disabled') DEFAULT 'pending'
ssl_issued_at TIMESTAMP NULL
ssl_expires_at TIMESTAMP NULL
verified_at TIMESTAMP NULL
last_check_at TIMESTAMP NULL
created_at, updated_at
UNIQUE(hostname)
INDEX(workspace_id)
```

### 16. `nps_responses`  (Sprint 6 — NPS Post-Payment)
```sql
id BIGINT PK
workspace_id BIGINT FK
invoice_id BIGINT FK ON DELETE SET NULL
client_id BIGINT FK→clients ON DELETE SET NULL
score TINYINT  -- 0..10
comment TEXT NULL
submitted_at TIMESTAMP
ip VARCHAR(45) NULL
ua VARCHAR(500) NULL
created_at, updated_at
INDEX(workspace_id, score)
```

### 17. `dashboard_widgets`  (Sprint 7 — Custom Dashboard)
```sql
id BIGINT PK
workspace_id BIGINT FK
user_id BIGINT FK→users ON DELETE CASCADE
widget_type VARCHAR(50)  -- 'revenue_chart','aging','top_clients','recent_invoices', etc
position INT DEFAULT 0
size ENUM('sm','md','lg','xl') DEFAULT 'md'
config JSON NULL  -- per-widget settings
is_visible BOOLEAN DEFAULT TRUE
created_at, updated_at
INDEX(workspace_id, user_id, position)
```

### 18. `accounting_exports`  (Sprint 4 — Accounting Export)
```sql
id BIGINT PK
workspace_id BIGINT FK
format ENUM('jurnal','mekari','quickbooks','xero','csv_generic')
period_start DATE
period_end DATE
file_path VARCHAR(255)
record_count INT
generated_by BIGINT FK→users ON DELETE SET NULL
created_at, updated_at
INDEX(workspace_id, created_at)
```

### 19. `email_inbox_forwards`  (Sprint 4 — Forward email → Expense)
```sql
id BIGINT PK
workspace_id BIGINT FK
inbound_address VARCHAR(255)  -- mis. expenses+ws123@invocely.app
forwarded_at TIMESTAMP
from_email VARCHAR(255)
subject VARCHAR(500)
attachments_count INT
status ENUM('processing','converted','duplicate','failed','manual_review') DEFAULT 'processing'
expense_id BIGINT FK→expenses ON DELETE SET NULL
raw_email_path VARCHAR(255)
ai_extracted_data JSON NULL
error_message VARCHAR(500) NULL
created_at, updated_at
INDEX(workspace_id, status)
```

### 20. `webhook_deliveries`  (Sprint 4 — Outbound Webhook log)
```sql
id BIGINT PK
webhook_id BIGINT FK→tenant_webhooks ON DELETE CASCADE
event VARCHAR(50)
payload JSON
status_code INT NULL
response_body TEXT NULL
attempt INT DEFAULT 1
delivered_at TIMESTAMP NULL
next_retry_at TIMESTAMP NULL
final_status ENUM('pending','success','failed','exhausted') DEFAULT 'pending'
created_at, updated_at
INDEX(webhook_id, final_status)
INDEX(next_retry_at)
```

### 21. `qris_codes`  (Sprint 2 — QRIS Embed)
```sql
id BIGINT PK
workspace_id BIGINT FK
invoice_id BIGINT FK ON DELETE CASCADE
provider_id BIGINT FK→payment_gateways ON DELETE SET NULL
amount DECIMAL(12,2)
qr_payload TEXT  -- raw QRIS string
qr_image_path VARCHAR(255)
expires_at TIMESTAMP
status ENUM('active','paid','expired','cancelled') DEFAULT 'active'
paid_at TIMESTAMP NULL
provider_ref VARCHAR(120) NULL
created_at, updated_at
INDEX(workspace_id, status)
UNIQUE(invoice_id, provider_id)
```

### 22. `npwp_validations`  (Sprint 2 — NPWP Validator cache)
```sql
id BIGINT PK
npwp_number VARCHAR(20)
canonical_npwp VARCHAR(16)  -- normalized 16 digit
is_valid BOOLEAN
name_djp VARCHAR(255) NULL  -- nama wajib pajak from DJP
status_djp VARCHAR(50) NULL  -- aktif/non-aktif/cabut
last_checked_at TIMESTAMP
provider_response JSON
created_at, updated_at
UNIQUE(canonical_npwp)
INDEX(last_checked_at)
```
Cache global (semua workspace share). NPWP itu public.

### 23. `signatures`  (Sprint 6 — E-Signature)
```sql
id BIGINT PK
workspace_id BIGINT FK
subject_type VARCHAR(100)  -- 'quote','invoice','contract'
subject_id BIGINT
signer_name VARCHAR(120)
signer_email VARCHAR(255)
signer_role VARCHAR(50) NULL  -- 'client','workspace_user'
signature_image_path VARCHAR(255)
signature_typed VARCHAR(120) NULL  -- alternative typed name
ip VARCHAR(45)
ua VARCHAR(500)
signed_at TIMESTAMP
verification_hash VARCHAR(64)  -- SHA-256 of canonical doc state
created_at, updated_at
INDEX(workspace_id, subject_type, subject_id)
```

---

## Column Modifications Needed (Existing Tables)

| Table | New Column | Type | FK | Reason |
|---|---|---|---|---|
| `invoices` | `project_id` | BIGINT NULL | →projects | Group invoice per project |
| `invoices` | `quote_id` | BIGINT NULL | →quotes | Track converted quote |
| `invoices` | `pph23_rate` | DECIMAL(5,2) DEFAULT 0 | — | Quick PPh23 (alt: gunakan tax_details table) |
| `invoices` | `pph23_amount` | DECIMAL(12,2) DEFAULT 0 | — | |
| `invoices` | `late_fee_total` | DECIMAL(12,2) DEFAULT 0 | — | Akumulasi late fee |
| `invoices` | `signed_at` | TIMESTAMP NULL | — | Tracking e-signature |
| `time_entries` | `project_id` | BIGINT NULL | →projects | |
| `expenses` | `project_id` | BIGINT NULL | →projects | |
| `quotes` | `project_id` | BIGINT NULL | →projects | |
| `quotes` | `signed_at` | TIMESTAMP NULL | — | |
| `clients` | `default_pph23_rate` | DECIMAL(5,2) DEFAULT 0 | — | Per-client withholding default |
| `clients` | `is_pkp` | BOOLEAN DEFAULT FALSE | — | Marker PKP untuk auto-PPN |
| `payments` | `bank_reconcile_line_id` | BIGINT NULL | →bank_reconcile_lines | Audit trail auto-match |
| `subscriptions` | `coupon_id` | BIGINT NULL | →coupons | Track applied coupon |
| `users` | `2fa_enabled` | BOOLEAN DEFAULT FALSE | — | Quick check tanpa join |

---

## Foreign Key Cascade Strategy

| Parent | Child | On Delete |
|---|---|---|
| workspaces | semua scoped tables | CASCADE |
| users | workspace_user, audit_logs.user_id, support_tickets.user_id | SET NULL untuk audit, CASCADE untuk own data |
| invoices | invoice_items, payments, attachments, comments, reminders, late_fees, tax_details, public_views, email_logs, whatsapp_logs, qris_codes | CASCADE |
| invoices | credit_notes | CASCADE (credit note tied to invoice) |
| clients | invoices, quotes, projects | RESTRICT (lindungi history) — paksa archive dulu |
| clients | client_portal_users, time_entries.client_id, expenses.client_id | CASCADE / SET NULL |
| projects | invoices.project_id, time_entries.project_id, expenses.project_id, quotes.project_id | SET NULL (project bisa dihapus tanpa hilang invoice) |
| quotes | invoices.quote_id | SET NULL |
| ai_providers | ai_usage_logs | SET NULL |
| tenant_webhooks | webhook_deliveries | CASCADE |

---

## Indexing Strategy

Setiap tabel **wajib** index:
1. `(workspace_id)` — atau composite dengan filter umum (status, date)
2. FK columns
3. `created_at` di tabel log/event (untuk pagination)
4. Soft unique constraints (mis. `(workspace_id, invoice_no)`, `(workspace_id, slug)`)

Untuk tabel high-traffic (audit_logs, app_notifications, ai_usage_logs, webhook_deliveries):
- Partition by `created_at` per kuartal kalau >10M rows
- Archive ke tabel `*_archive` setelah 1 tahun

---

## Encryption At Rest

Field-level encrypt via Laravel Crypt:
- `ai_providers.api_key_encrypted`
- `payment_gateways.api_key_encrypted` / `secret_encrypted` (sudah ada pattern)
- `external_oauth_identities.token_encrypted`
- `user_2fa_secrets.secret_encrypted`
- `user_2fa_secrets.recovery_codes_encrypted`
- `client_portal_users.password_hash` — bcrypt (ga butuh Crypt, tapi pakai password_hash())
- `whatsapp_logs.message_body` (kalau policy ketat) — opsional

JANGAN encrypt:
- Email, name (perlu searchable)
- Amount, date (perlu sortable, indexable)
- IDs

---

## Multi-Tenant Enforcement Pattern

Setiap query Eloquent **wajib** filter via global scope `WorkspaceScope`:

```php
// app/Scopes/WorkspaceScope.php (sudah ada)
class WorkspaceScope implements Scope {
    public function apply(Builder $builder, Model $model) {
        if ($workspace = workspace()) {
            $builder->where($model->getTable() . '.workspace_id', $workspace->id);
        }
    }
}
```

Apply ke setiap model baru via trait `BelongsToWorkspace`:
```php
class CreditNote extends Model {
    use BelongsToWorkspace;
}
```

Untuk superadmin yang bisa lintas workspace, override:
```php
SomeModel::withoutGlobalScope(WorkspaceScope::class)->get()
```

---

## Test Coverage Requirement

Setiap feature WAJIB punya test:
1. **Multi-tenant isolation** — workspace A ga bisa lihat data workspace B
2. **Plan gating** — fitur premium return upgrade prompt untuk free user
3. **FK cascade** — delete parent, child ikut atau set null sesuai design
4. **Audit log generated** — mutation create entry di `audit_logs`

Letak: `tests/Feature/{FeatureName}Test.php`

---

Lihat [00-OVERVIEW.md](00-OVERVIEW.md) untuk daftar fitur, [99-PROGRESS.md](99-PROGRESS.md) untuk status live.
