# Invocely Roadmap v2 — Feature Expansion

> **Tujuan dokumen:** spec lengkap untuk fitur ekspansi Invocely di luar 41 task awal.
> **Last updated:** 2026-05-08
> **Total fitur:** 50 feature spec dalam 10 kategori
> **Method:** Setiap fitur punya DB schema (relasi ke tabel existing), routes, controller, view, edge case, & status.

---

## Legend Status

| Icon | Arti |
|---|---|
| ❌ | **Not Started** — belum ada code, belum ada DB |
| 🟦 | **DB Ready** — tabel sudah ada (di `mega_features_batch.php`), tinggal controller/view/logic |
| 🟡 | **In Progress** — sedang dikerjakan |
| ✅ | **Done** — sudah complete (DB + controller + view + tested) |
| 🔒 | **Blocked** — ada dependency yang harus selesai dulu |

## Legend Priority

| Icon | Arti |
|---|---|
| 🔥 | **Critical** — directly impact revenue/cash flow |
| ⭐ | **High** — kompetitor lain punya, kita harus punya |
| 💎 | **Medium** — nice differentiator |
| 🌱 | **Low** — future enhancement |

---

## Daftar File

| File | Fitur | Status Ringkas |
|---|---|---|
| [01-DATABASE-RELATIONS.md](01-DATABASE-RELATIONS.md) | Full ERD + relasi semua tabel | 📚 Reference |
| [02-quick-wins.md](02-quick-wins.md) | Estimate, Late Fee, Reminder, Credit Note, Discount, Customer Portal | 1🟦 5❌ |
| [03-indonesia.md](03-indonesia.md) | e-Faktur, PPh23, NPWP, Bank Reconcile, WhatsApp, QRIS | 6❌ |
| [04-ai-features.md](04-ai-features.md) | OCR, Smart Desc, Predict, Risk, Translate | 5❌ |
| [05-time-project.md](05-time-project.md) | Time Tracker, Project, Expense, Budget | 2🟦 2❌ |
| [06-collaboration.md](06-collaboration.md) | Multi-User, Approval, Comment, Assign, Activity Feed | 2🟦 3❌ |
| [07-reporting.md](07-reporting.md) | Aging, Cashflow, P&L, Tax Summary, Custom Dashboard | 5❌ |
| [08-integration.md](08-integration.md) | API, Webhook, Zapier, Accounting Export, Email Forward | 2🟦 3❌ |
| [09-mobile.md](09-mobile.md) | PWA, Quick Capture, Mobile Audit | 3❌ |
| [10-compliance.md](10-compliance.md) | Audit Log, GDPR, 2FA, SSO, Data Residency | 1🟦 4❌ |
| [11-differentiator.md](11-differentiator.md) | E-Sign, Pay-by-Link, Comment Thread, NPS, White-Label | 5❌ |
| [99-PROGRESS.md](99-PROGRESS.md) | Live progress tracker | 📊 Track |

**Total:** 50 fitur · 8 sudah punya tabel DB · 42 brand new

---

## Prioritas Eksekusi (Recommended Order)

Kalau eksekusi 1-orang, ikuti urutan ini supaya impact terbesar duluan dan setiap fitur build di atas yang sebelumnya.

### Sprint 1 — Cash Flow Booster (2 minggu)
1. **Estimate / Quotation** (DB ready, tinggal logic) — gerbang sales
2. **Reminder Sequence** — directly improve cash flow user
3. **Late Fee Otomatis** — pelengkap reminder
4. **Credit Note** — handle return/refund proper
5. **Aging Report** — visibilitas piutang

### Sprint 2 — Indonesia Lokal (1.5 minggu)
1. **PPh 23 (Withholding)** — wajib untuk B2B Indonesia
2. **NPWP Validator** — quick win
3. **WhatsApp Send** — distribusi yang paling sering dipakai
4. **QRIS Embed** — universal payment Indonesia
5. **e-Faktur Export** — moat untuk PKP

### Sprint 3 — Customer Experience (1 minggu)
1. **Customer Portal Login** — klien punya akun
2. **Comment Thread di Invoice** — komunikasi 2-arah
3. **Pay-by-Link Embed** — friction-less payment
4. **Discount Code** (DB ready)

### Sprint 4 — Automation & Scale (2 minggu)
1. **Multi-User + Roles** (DB ready) — buka enterprise market
2. **Approval Workflow** — buat klien enterprise
3. **API Public + Webhook** (DB ready) — ekspansi ekosistem
4. **Zapier Connector** — distribusi
5. **Audit Log full** (DB ready) — compliance

### Sprint 5 — Smart Features (1.5 minggu)
1. **AI Description Generator** — friction reducer
2. **OCR Receipt** — power user feature
3. **Predict Payment Date** — insight unik
4. **Late Risk Score** — actionable warning

### Sprint 6 — Differentiator & Compliance (1 minggu)
1. **E-Signature pada Estimate**
2. **2FA / TOTP**
3. **NPS Post-Payment**
4. **Mobile Audit**
5. **PWA Setup**

### Sprint 7 — Premium / Enterprise (1.5 minggu)
1. **White-Label Domain (CNAME)**
2. **SSO**
3. **Custom Dashboard Widget**
4. **Bank Auto-Reconcile**

**Total estimated:** ~10-11 minggu untuk satu developer fokus, ~6 minggu untuk tim 2 orang paralel.

---

## Prinsip Desain (di-enforce semua spec)

### 1. Multi-Tenant Strict
**Setiap tabel baru WAJIB punya `workspace_id` foreign key + index.**
Cascade delete saat workspace dihapus. Filter scope di every controller via `workspace()` helper.

### 2. Dynamic Provider (No Hardcode)
**Untuk integrasi pihak ke-3** (AI, payment, email, SMS, WhatsApp, OCR), ikuti pattern di `D:\project laravel\foodscan\docs\10-AI-PROVIDERS.md`:
- Tabel `providers` generik (atau extend `payment_gateways` pattern)
- API key di-encrypt at rest, never logged, never returned in response
- User input semua sendiri di admin UI
- Format-based adapter (bukan vendor-based)
- Optional preset templates di `storage/app/{type}-presets/*.json` untuk autofill

### 3. pSEO Auto-Include
Halaman public yang baru (mis. customer portal, public estimate, comparison) **wajib** di-sitemap, ada JSON-LD, meta tag lengkap.

### 4. Audit Trail Everywhere
**Semua mutasi penting** (create/update/delete invoice, payment, quote, dll) **wajib** insert ke `audit_logs` via observer atau service decorator.

### 5. Soft Decline > Hard Block
Plan limit (free vs pro vs business) tampil sebagai upgrade prompt, bukan 403 error. Pakai middleware `plan:feature.name`.

### 6. Idempotency
Webhook receive, payment confirmation, recurring billing — pakai idempotency key untuk hindari double-process.

### 7. Job Queue Default
Semua I/O eksternal (email, webhook outbound, OCR call, AI call) lewat Laravel queue. Block synchronous request HARUS dihindari.

---

## Existing Foundation (Sudah Ada)

| Asset | Lokasi |
|---|---|
| 30 tabel database | `database/migrations/` |
| 178 routes | `routes/web.php` |
| 19 models | `app/Models/` |
| 26 controllers | `app/Http/Controllers/` |
| 5 PDF templates | `resources/views/invoices/templates/` |
| Drag-drop layout builder | `resources/views/invoice-layouts/builder.blade.php` |
| License v3 client | `app/Services/LicenseClient.php` |
| Plan gating middleware | `app/Http/Middleware/EnforcePlanLimits.php` |
| Workspace scoping helper | `app/Helpers/workspace.php` |

Semua spec di roadmap v2 ini extend foundation di atas — bukan replace.

---

## How to Use This Roadmap

1. **Baca [01-DATABASE-RELATIONS.md](01-DATABASE-RELATIONS.md)** dulu — paham keseluruhan ERD
2. **Pilih fitur** dari Sprint 1 (atau pilih sendiri sesuai prioritas)
3. **Baca spec lengkap** di file kategori (`02-` sampai `11-`)
4. **Update [99-PROGRESS.md](99-PROGRESS.md)** saat mulai dan saat selesai
5. **Setiap fitur done** → update status emoji + isi tanggal completion

Setiap spec follow template:
```
## Feature: <Name>
Status · Priority · Effort

### Why
### Database Schema (new tables / column additions / relations)
### Routes
### Controller(s)
### View(s) / UI
### Edge Cases & Notes
### Acceptance Criteria
```
