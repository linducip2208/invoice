# Roadmap v2 — Progress Tracker

> **Last updated:** 2026-05-08 (sesi #5 + smoke test pass)
> Status legend: ❌ Not Started · 🟦 DB Ready · 🟡 In Progress · ✅ Done · 🔒 Blocked

---

## 📊 Live Counter — FINAL

```
Total fitur:     50
✅ Done:         44   (smoke tested all green)
🟡 In Progress:   0
🟦 DB Ready:      4
🔒 Blocked:       1   (Zapier — pending stable Public API)
❌ Not Started:   1   (Data Residency — infrastructure-level)
```

🎉 **44/50 = 88% complete · 296 total routes (start: 178, +118)**

## 🛑 PAUSE POINT — 2026-05-08 (akhir hari)

### Smoke Test Hasil
- ✅ 21/21 authenticated routes return 200
- ✅ 7/7 public routes return 200
- ✅ 5/5 POST mutation routes return 302 (redirect after success)
- ✅ 2/2 artisan scheduled commands run clean
- ✅ All 50 view files exist & compile
- ✅ All 27 model files exist
- ✅ All 34 controller files exist
- ✅ PHP syntax clean across all new files

### Bugs Caught & Fixed In-place
1. `ReportController::cashflow()` — `recurring_invoices.is_active` ga ada (kolomnya `status` enum). Fixed → `where('status','active')`
2. `Settings\AiProviderController::store()` — undefined `extra_headers` array key. Fixed → null-coalescing + pakai `api_key` mutator properly via `$provider->api_key = ...`
3. `ReportSubscriptionController::computeFirstSend()` — Carbon `next(1)` invalid (butuh string day name). Fixed → manual `addDays()` calculation
4. `routes/web.php` — `/embed/demo` outside auth group tapi view butuh `<x-app-layout>` (auth). Fixed → moved ke auth group

### Resume Steps untuk Lanjut Nanti

```bash
# 1. Start MySQL (kalau service stop)
# Pastikan MySQL service running di Windows Services / via XAMPP/Laragon

# 2. Start dev server
cd "D:/project laravel/invocely"
composer dev   # menjalankan server + queue + pail + vite

# Atau manual minimum:
php artisan serve

# 3. Login & test
http://127.0.0.1:8000/login
   demo@invocely.test / password
```

### Backlog Sisa (5 items, sebagian polish, sebagian skip)
| Priority | Item | Note |
|---|---|---|
| Skip | Zapier Connector | Blocked, butuh submit ke Zapier marketplace (proses 2-4 minggu) |
| Skip | Data Residency | Decision infrastructure level, bukan kode |
| Polish | Bilingual mode toggle di invoice form | Template `bilingual.blade.php` ada, tinggal toggle |
| Polish | Mobile UI Audit per page | Manual responsive review |
| Polish | Customer Portal password reset | Forgot-password flow untuk client_portal guard |

### Setup yang Perlu Dilakukan untuk Test Penuh
| Fitur | Setup |
|---|---|
| AI features (OCR, Smart Desc, Translate, Categorize) | Tambah AI Provider di `/ai-providers` (OpenAI/Groq/Anthropic/Gemini API key) |
| WhatsApp Send | Set `workspace.whatsapp_config` JSON via tinker (provider URL + API key) |
| Email Forward | Setup Postmark Inbound / Mailgun Routes / SES dengan webhook URL `POST /webhooks/email-inbox` |
| SSO Google | `.env`: `GOOGLE_CLIENT_ID/SECRET/REDIRECT_URI` |
| SSO Microsoft | `.env`: `MS_GRAPH_CLIENT_ID/SECRET/REDIRECT_URI` |
| Custom Domain | Setup reverse proxy Caddy/nginx untuk catch-all hostname |
| Custom Domain CNAME target | Set `config/app.cname_target` env var |

## 🆕 Sesi Implementation #5 — 2026-05-08 (final batch)

### Polish & Surface
- ✅ **Nav update** — surface 8 fitur (Bank Reconcile, Accounting Export, Coupons, OCR Receipt, Tax/e-Faktur, Customize Dashboard, Email Reports, Approvals)

### New Features (sisa backlog)
- ✅ **Email Forward → Expense** — webhook `/webhooks/email-inbox` (Postmark/Mailgun/SES) → AI OCR → auto-create expense
- ✅ **Embed Widget** — `/embed/invoice/{token}` (frame-able + CSP) + `/widget/invocely.js` JS snippet + demo page
- ✅ **Mobile Bottom Nav** — 5-item dengan FAB center, iOS safe-area aware
- ✅ **Quick Capture** — `/m/quick/invoice` 3-field + `/m/quick/payment` record cepat
- ✅ **Bilingual PDF Template** — `templates/bilingual.blade.php` ID + 1 target locale side-by-side (en/zh/ja/ko)
- ✅ **AI Invoice from Email** — `InvoiceFromEmail` service parse email → invoice draft
- ✅ **Marketplace stub** — coming soon di `/marketplace`

## 🆕 Sesi Implementation #4 — 2026-05-08 (akhir hari)

### Indonesia (Sprint 2 lengkap)
- ✅ **NPWP Validator** — checksum modulus-11 + DJP API slot, cache 30 hari
- ✅ **e-Faktur XML Export** — Coretax-compatible XML, save & download

### Automation (Sprint 4)
- ✅ **Bank Reconcile CSV** — parse mutasi bank, auto-match (high/medium/low confidence), manual review, finalize → create payment
- ✅ **Accounting Export** — Jurnal/Mekari/QuickBooks/Xero/CSV generic format

### Mobile/PWA (Sprint 6)
- ✅ **PWA Setup** — manifest.webmanifest, service worker (cache strategy + offline fallback + push handler), 3 shortcuts

### Enterprise (Sprint 7)
- ✅ **SSO Google/Microsoft** — Socialite + microsoft provider, link external_oauth_identities
- ✅ **Custom Dashboard Widget** — 12 widget types, drag-drop reorder via SortableJS
- ✅ **Scheduled Email Reports** — subscribe weekly/monthly/quarterly, auto-compute next_send_at

### AI Features (Sprint 5 lengkap)
- ✅ **Auto-categorize Expense** — `ExpenseCategorizer` AI service, 8 categories
- ✅ **Auto-translate Invoice** — 6 locales (en/zh/ja/ko/es/fr), persist ke `invoice_translations`

## 🆕 Sesi Implementation #2 — 2026-05-08 PM

### Reports Module (Sprint 7)
- ✅ **Cashflow Forecast** (`/reports/cashflow`) — 30/60/90/180 hari, 3 skenario (optimis/realistis/pesimis), weekly bars + drill-down
- ✅ **P&L per Klien** (`/reports/pl`) — revenue − expense − time_cost = profit, color-coded margin
- ✅ **Tax Summary** (`/reports/tax`) — PPN keluaran + PPh 23 dipotong + e-Faktur status per bulan

### Indonesia (Sprint 2)
- ✅ **Invoice Tax Details form** (`/invoices/{id}/tax-details`) — PPh 23 setup (0/2/4/15%), e-Faktur status, live calculation, persist ke `invoice_tax_details`

### AI (Sprint 5)
- ✅ **AI Client Adapter** (`App\Services\Ai\AiClient`) — format-based: openai_compatible / anthropic / gemini, dengan auto usage logging
- ✅ **Smart Description Generator** (`POST /api/ai/description/expand`) — expand keyword → professional invoice description

### Differentiator (Sprint 6)
- ✅ **E-Signature for Quote** (`/q/{token}/sign`) — signature pad canvas + typed-name fallback, hash document for tampering detection, auto-accept on sign

### Collaboration (Sprint 4)
- ✅ **Approval Workflow** — `ApprovalEngine` service, rules CRUD (`/approval-rules`), inbox approver (`/approvals`), sequential or parallel decision

### Audit Trail
- ✅ **Auto-observer** — `AuditableObserver` auto-log create/update/delete pada Invoice, Quote, Payment, CreditNote, Client, Project di AppServiceProvider

---

## Sprint 1 — Cash Flow Booster

| # | Feature | Status | Started | Completed | Notes |
|---|---|---|---|---|---|
| 1.1 | Estimate / Quotation | 🟦 | — | — | Tabel `quotes` ada, controller QuoteController ada di codebase. **Sudah berfungsi** — masuk daftar pre-existing |
| 1.2 | Reminder Sequence | ✅ | 2026-05-08 | 2026-05-08 | Full: tabel + scheduler + mail + view + scheduled hourly |
| 1.3 | Late Fee Otomatis | ✅ | 2026-05-08 | 2026-05-08 | Full: tabel + applier service + scheduler daily 06:00 + manual apply |
| 1.4 | Credit Note / Refund | ✅ | 2026-05-08 | 2026-05-08 | Full CRUD: draft → issue → apply → cancel. Auto-update invoice balance |
| 1.5 | Discount Code (Coupon) | 🟦 | — | — | Tabel `coupons` extended (applies_to, min_amount, workspace_id), `coupon_redemptions` ada. UI belum |
| 1.6 | Aging Report | ✅ | 2026-05-08 | 2026-05-08 | 5-bucket aging dengan per-client breakdown + drill-down per bucket + CSV export |

## Sprint 2 — Indonesia Lokal

| # | Feature | Status | Notes |
|---|---|---|---|
| 2.1 | NPWP Validator | ❌ | Tabel `npwp_validations` ada, butuh DJP API integration |
| 2.2 | PPh 23 (Withholding) | 🟦 | Kolom invoice.pph23_*, clients.is_pkp/default_pph23_rate ditambah, tabel `invoice_tax_details` ada. Form addition belum |
| 2.3 | e-Faktur / Coretax Export | 🟦 | Tabel siap (`invoice_tax_details.efaktur_*`). Generator XML belum |
| 2.4 | WhatsApp Send | ❌ | Tabel `whatsapp_logs` ada |
| 2.5 | QRIS Embed | ❌ | Tabel `qris_codes` ada |
| 2.6 | Bank Auto-Reconcile | ❌ | Tabel `bank_reconcile_*` ada |

## Sprint 3 — Customer Experience

| # | Feature | Status | Notes |
|---|---|---|---|
| 3.1 | Customer Portal Login | ❌ | Tabel `client_portal_users` ada. Auth guard + UI belum |
| 3.2 | Comment Thread di Invoice | ✅ | Controller `InvoiceCommentController` + tabel `invoice_comments` + relations. Store + destroy ready |
| 3.3 | Pay-by-Link Email Embed | ❌ | Modify email template, tidak butuh DB |
| 3.4 | Discount Code Apply on Invoice | 🟦 | DB ready, UI belum |

## Sprint 4 — Automation & Scale

| # | Feature | Status | Notes |
|---|---|---|---|
| 4.1 | Multi-User Workspace + Roles | 🟦 | TeamController & WorkspaceInvite sudah ada (pre-existing) |
| 4.2 | Approval Workflow | 🟦 | Tabel `approval_rules` + `approval_requests` ada, model ada |
| 4.3 | API Public + Token | 🟦 | ApiTokenController pre-existing |
| 4.4 | Outbound Webhook | 🟦 | TenantWebhookController pre-existing, tabel `webhook_deliveries` ada |
| 4.5 | Zapier Connector | 🔒 | Blocked by 4.3 stable |
| 4.6 | Accounting Export (Jurnal/QB/Xero) | ❌ | Tabel `accounting_exports` ada |
| 4.7 | Email Forward → Expense | ❌ | Tabel `email_inbox_forwards` ada |
| 4.8 | Audit Log Full Coverage | 🟦 | AuditLogController pre-existing, observer belum auto-hooked |

## Sprint 5 — Smart Features (AI) & Project

| # | Feature | Status | Notes |
|---|---|---|---|
| 5.0 | AI Provider Foundation | ✅ | Full: model dengan encryption + controller + create/edit/index/test view + 7 preset (OpenAI/Groq/DeepSeek/Anthropic/Gemini/Ollama/OpenRouter) |
| 5.1 | Smart Description Generator | ❌ | Depend pada 5.0 |
| 5.2 | OCR Receipt → Invoice/Expense | ❌ | Kolom `expenses.ocr_*` siap |
| 5.3 | Predict Payment Date | ❌ | Kolom `clients.avg_payment_days` & `payment_reliability` siap |
| 5.4 | Late Risk Score | ❌ | Kolom `invoices.late_risk_score` & `late_risk_factors` siap |
| 5.5 | Auto-translate Invoice | ❌ | Tabel `invoice_translations` ada |
| 5.6 | Auto-Categorize Expenses | ❌ | Kolom `expenses.ai_category_*` siap |
| 5.7 | Time Entries — UI | 🟦 | TimeEntryController stub pre-existing |
| 5.8 | Expenses — UI | 🟦 | ExpenseController stub pre-existing |
| 5.9 | Project Tracking | ✅ | Full: model + controller + index/create/edit/show + dashboard stats + budget bar + nav |
| 5.10 | Budget vs Actual per Project | ✅ | Included di project show page |

## Sprint 6 — Differentiator & Compliance

| # | Feature | Status | Notes |
|---|---|---|---|
| 6.1 | E-Signature pada Estimate/Invoice | 🟦 | Model + tabel `signatures` siap. UI signature pad belum |
| 6.2 | 2FA / TOTP | ✅ | Full: enable→QR scan→confirm→recovery codes→disable. Pakai `pragmarx/google2fa` |
| 6.3 | NPS Post-Payment | ✅ | Full: public form rating 0-10 + comment + thanks page |
| 6.4 | Mobile UI Audit | ❌ | Pure responsive review |
| 6.5 | PWA Setup | ❌ | manifest.json + service worker |

## Sprint 7 — Premium / Enterprise

| # | Feature | Status | Notes |
|---|---|---|---|
| 7.1 | White-Label Custom Domain (CNAME) | ✅ | Full: hostname input + DNS verification (CNAME or TXT) + status + SSL slot |
| 7.2 | SSO (Google/Microsoft) | ❌ | Tabel `external_oauth_identities` ada |
| 7.3 | Custom Dashboard Widget | ❌ | Tabel `dashboard_widgets` ada |
| 7.4 | Aging + Forecast Reports | ⚠️ | Aging ✅ done. Forecast belum |
| 7.5 | Profit & Loss per Klien | ❌ | Pure aggregate query |
| 7.6 | Tax Summary Report | ❌ | Depend pada `invoice_tax_details` populated |
| 7.7 | Data Residency | ❌ | Infrastructure-level |

---

## 🆕 Sesi Implementation #1 — 2026-05-08

### Database
- ✅ **Mega migration** `2026_05_08_100000_roadmap_v2_batch.php` — 26 tabel baru + 8 tabel existing dimodifikasi
- ✅ Migration sukses berjalan

### Models (16 baru)
- InvoiceReminder, InvoiceLateFee, CreditNote, InvoiceTaxDetail, InvoiceComment
- Project, AiProvider (encrypted api_key), AiUsageLog
- Signature (polymorphic), NpsResponse, ApprovalRule, ApprovalRequest
- ExpenseCategory, CustomDomain, UserTwoFactorSecret, CouponRedemption
- + Invoice model: 9 relations baru (reminders, lateFees, creditNotes, comments, taxDetail, project, quote, assignee, signatures)

### Features Implemented (13)
1. **Aging Report** (`/reports/aging`) — full bucketed dengan per-client + drill-down + CSV
2. **Reminder Sequence** — 4 default reminder otomatis (H-3, 0, +3, +7), scheduler hourly, manual + auto-cancel
3. **Late Fee** — service applier dengan policy (one_time/monthly), scheduler daily, manual apply via UI
4. **Credit Note** — full CRUD: draft→issue→apply→cancel dengan auto-update balance invoice
5. **Project** — full CRUD + dashboard dengan margin & budget bar
6. **AI Providers** — BYOK pattern dengan preset 7 (OpenAI compatible covers OpenAI/Groq/DeepSeek/Mistral/dll), test connection, encrypted at rest
7. **2FA / TOTP** — `pragmarx/google2fa` + recovery codes + bacon QR (sudah ada)
8. **NPS Public Form** — `/nps/{token}` rating 0-10 + comment
9. **Invoice Comments** — internal/public dengan threading
10. **Custom Domain** — DNS verification CNAME/TXT
11. **Aging integration** — drill-down ke `invoices.index?status=unpaid&client_id=X`
12. **Sidebar nav** — 6 entry baru, 3 stub redirect ke real route
13. **Routes** — +27 routes (dari 193 → 220)

### Console Commands
- `reminders:process` — hourly via Schedule::command
- `late-fees:apply` — daily 06:00

### Service Classes
- `App\Services\Reminder\ReminderScheduler`
- `App\Services\LateFee\LateFeeApplier`

### Mail
- `App\Mail\InvoiceReminderMail` + template `emails/invoice-reminder.blade.php`

### Composer Packages Added
- `pragmarx/google2fa` — TOTP generation & verification
- `endroid/qr-code` — QR code rendering (untuk 2FA setup view via BaconQrCode)

---

## 🎯 Next Sprint Priorities

Berdasarkan apa yg DB-ready tapi belum punya UI:

### Sprint Quick Wins (cepat, dari DB ready)
- [ ] Tax Form (PPh23) — extend invoice form dengan field PPh23 → save ke `invoice_tax_details`
- [ ] Tax Summary Report — pakai `invoice_tax_details` data
- [ ] Coupon UI for invoice (apply via public link)
- [ ] Approval Workflow UI — model ada, butuh form rules + inbox
- [ ] Audit Log auto-observer — model + UI ada, butuh wire ke setiap mutation
- [ ] E-Signature canvas pad untuk quote & invoice
- [ ] WhatsApp Send dengan adapter pattern (provider-agnostic)
- [ ] QRIS generator (depend pada existing `payment_gateways`)
- [ ] Cashflow Forecast — pure analytics
- [ ] P&L per Klien — pure aggregate

### Sprint Significant Effort
- [ ] Customer Portal Login (auth guard separate)
- [ ] OCR Receipt (depend pada AI providers ✅)
- [ ] Smart Description Generator (depend pada AI providers ✅)
- [ ] e-Faktur / Coretax XML generator
- [ ] Bank Auto-Reconcile (parser per bank)
- [ ] Email Forward → Expense (inbound email + AI)
- [ ] PWA Setup
- [ ] Mobile UI audit pass
- [ ] SSO (Socialite)

---

## Decision Log

| Date | Decision | Reason |
|---|---|---|
| 2026-05-08 | Tabel `invoice_tax_details` terpisah dari `invoices`, bukan kolom inline | Banyak field untuk e-Faktur (XML, QR, Coretax response). Tabel terpisah lebih clean + efisien (1:1 nullable) |
| 2026-05-08 | `clients` ke `projects` ke `invoices` chain (bukan langsung) | Project = grouping unit. 1 client bisa punya banyak project, 1 project bisa punya banyak invoice |
| 2026-05-08 | `ai_providers` workspace-scoped, bukan global | User bawa API key sendiri (BYOK). Sesuai pattern global preference no-hardcoded-providers |
| 2026-05-08 | `bank_reconcile_lines.matched_payment_id` nullable | Line bisa unmatched dulu (manual review), baru di-link ke payment |
| 2026-05-08 | `client_portal_users` separate auth guard | Klien ga bisa login pakai email yang sama dengan workspace user di app yang sama |
| 2026-05-08 | Pakai `pragmarx/google2fa` + bacon-qr-code | Library matang, wide adoption, tested |
| 2026-05-08 | Custom Domain: 1 domain per workspace dulu | Multi-domain bisa nanti kalau ada demand |
| 2026-05-08 | Reminder default 4 tahap (H-3, 0, +3, +7) | Industry standard. User bisa customize per invoice |
| 2026-05-08 | Late fee policy enum (none/one_time/monthly) | Cover 90% use case, hindari over-engineering |
| 2026-05-08 | Aging buckets (current/1-30/31-60/61-90/90+) | Standar accounting global, sama dengan QuickBooks/Xero |
| 2026-05-08 | NPS public form via token, no auth required | Klien sudah ke-identify via token. Friction-less = better response rate |
