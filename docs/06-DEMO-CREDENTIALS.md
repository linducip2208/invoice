# Demo Credentials & Seeded Data — Invocely

> **Last Updated:** 2026-05-07
> **Purpose:** Login credentials dan data dummy yang ter-seed otomatis untuk demo / development.

---

## 1. Login Credentials

Ada **2 jenis user** sekarang: **Superadmin** (kelola seluruh aplikasi) dan **Workspace Admin** (kelola workspace mereka sendiri).

### 🔴 Superadmin (Owner Aplikasi)

Akses semua plan, user, workspace, subscription. Login otomatis redirect ke `/admin`.

| Field | Value |
|-------|-------|
| **URL Login** | `http://localhost:8000/login` |
| **Email** | `superadmin@invocely.test` |
| **Password** | `superadmin123` |
| **Role** | `is_superadmin = true` |
| **Default Page** | `/admin` (dashboard MRR, user, plans, dll.) |

### 🟢 Demo Workspace Admin

User demo dengan workspace, klien, invoice, dll.

| Field | Value |
|-------|-------|
| **URL Login** | `http://localhost:8000/login` |
| **Email** | `demo@invocely.test` |
| **Password** | `password` |
| **Role** | `admin` di workspace `Demo Company` |
| **Default Page** | `/dashboard` |
| **Plan** | Auto-assigned ke Free saat akses pertama (bisa upgrade di `/billing`) |

> ⚠️ Credential ini hanya untuk **demo / development**. JANGAN dipakai di production. Ganti via `php artisan tinker` atau register user baru sebelum deploy.

### Cara generate ulang demo data

```bash
php artisan migrate:fresh --seed
```

Atau hanya seeder demo:

```bash
php artisan db:seed --class=DemoDataSeeder
```

Source: `database/seeders/DemoDataSeeder.php`

---

## 2. Workspace Default — Demo Company

Detail workspace yang dibuat otomatis saat seed:

| Field | Value |
|-------|-------|
| Name | Demo Company |
| Slug | `demo-company` |
| Email | `hello@demo-company.test` |
| Phone | `+1-555-0199` |
| Address | 123 Business Ave, Suite 100 |
| City / State | San Francisco, CA |
| Country | United States |
| Postal Code | 94105 |
| Tax ID | `TAX-12345-DEMO` |
| Invoice Prefix | `INV-` |
| Default Template | `classic` |
| Currency | `USD` |
| Primary Color | `#2563EB` |
| Accent Color | `#1E40AF` |

---

## 3. Clients (10 records)

| # | Name | Company | Email | Phone |
|---|------|---------|-------|-------|
| 1 | Acme Corporation | Acme Corp | billing@acme.test | +1-555-1000 |
| 2 | Jane Smith | Smith Consulting | jane@smithconsulting.test | +1-555-1001 |
| 3 | Bob Johnson | Johnson LLC | bob@johnson.test | +1-555-1002 |
| 4 | Global Tech Solutions | Global Tech Solutions | accounts@globaltech.test | +1-555-1003 |
| 5 | Creative Studios | Creative Studios Inc | hello@creativestudios.test | +1-555-1004 |
| 6 | DataFlow Analytics | DataFlow Analytics | finance@dataflow.test | +1-555-1005 |
| 7 | GreenLeaf Organics | GreenLeaf Organics | orders@greenleaf.test | +1-555-1006 |
| 8 | CloudNine Hosting | CloudNine Hosting LLC | billing@cloudnine.test | +1-555-1007 |
| 9 | PixelPerfect Design | PixelPerfect Design | info@pixelperfect.test | +1-555-1008 |
| 10 | BuildRight Construction | BuildRight Construction Co | accounts@buildright.test | +1-555-1009 |

Semua client berdomisili di **San Francisco, CA** (alamat berurut: 100, 200, 300, … Market St) dan berstatus `is_active = true`.

---

## 4. Products / Services (15 records)

| # | Name | Category | Unit Price (USD) |
|---|------|----------|------------------|
| 1 | Web Development - Hourly | Development | 85.00 |
| 2 | UI/UX Design - Hourly | Design | 75.00 |
| 3 | SEO Consultation | Marketing | 150.00 |
| 4 | Content Writing - per 1000 words | Content | 50.00 |
| 5 | Server Maintenance - Monthly | Hosting | 200.00 |
| 6 | Logo Design | Design | 350.00 |
| 7 | Email Marketing Setup | Marketing | 500.00 |
| 8 | Mobile App Development - Hourly | Development | 95.00 |
| 9 | Photo Editing - per image | Design | 15.00 |
| 10 | Social Media Management - Monthly | Marketing | 600.00 |
| 11 | Database Optimization | Development | 250.00 |
| 12 | Video Editing - Hourly | Content | 65.00 |
| 13 | WordPress Plugin Development | Development | 1200.00 |
| 14 | Technical Support - Hourly | Support | 55.00 |
| 15 | Cloud Migration Service | Hosting | 1500.00 |

Semua produk: `tax_rate = 0`, `is_active = true`.

---

## 5. Invoices (20 records, randomized)

20 invoice di-generate dengan distribusi status realistis:

| Index Range | Status Pool |
|-------------|-------------|
| 0–2 | `draft` |
| 3–7 | `sent` |
| 8–12 | `paid` |
| 13–15 | `partial`, `sent`, `overdue` (random) |
| 16–19 | `overdue`, `cancelled`, `draft` (random) |

**Karakteristik tiap invoice:**

- **Invoice No:** `INV-{YYYYMMDD}-{0001..0020}`
- **Items per invoice:** 1–4 (random pick dari 15 produk)
- **Quantity per item:** 1–5
- **Tax rate:** 10%
- **Discount:** ~25% chance kena diskon (5–15% atau $10–$50 fixed)
- **Issue date:** 1–60 hari ke belakang dari hari ini
- **Due date:** issue_date + 30 hari
- **Currency:** USD
- **Template:** classic
- **Notes:** "Thank you for your business!"
- **Terms:** "Payment is due within 30 days. Late payments may incur a 2% monthly fee."

**Amount paid logic:**
- `paid` → `amount_paid = total`, `balance_due = 0`
- `partial` → `amount_paid = 30–70% dari total`
- `draft / sent / overdue / cancelled` → `amount_paid = 0`

---

## 6. Payments

Setiap invoice ber-status `paid` atau `partial` otomatis dapat record di tabel `payments`:

| Field | Value |
|-------|-------|
| amount | sama dengan `invoice.amount_paid` |
| payment_date | random 1–30 hari ke belakang |
| payment_method | random: `bank_transfer` / `credit_card` / `cash` |
| reference_no | `PAY-0001`, `PAY-0002`, … |
| notes | "Payment received" |

---

## 7. Recurring Invoices (2 records)

### Recurring #1 — Active

| Field | Value |
|-------|-------|
| Client | Acme Corporation |
| Template | classic |
| Frequency | monthly (interval 1) |
| Next Run | +7 hari dari hari ini |
| Max Occurrences | 12 |
| Status | `active` |
| Items | Monthly Website Maintenance ($200) + Monthly SEO Report ($150) |
| Subtotal | $350 |
| Tax (10%) | $35 |
| **Total** | **$385** |
| Terms | Net 15 |

### Recurring #2 — Paused

| Field | Value |
|-------|-------|
| Client | Global Tech Solutions |
| Template | modern |
| Frequency | monthly (interval 1) |
| Next Run | +30 hari dari hari ini |
| Max Occurrences | unlimited (`null`) |
| Status | `paused` |
| Items | Cloud Infrastructure Support ($500) |
| **Total** | **$500** |
| Terms | Net 30 |

---

## 8. Invoice Templates (Seeded)

Di-seed via `InvoiceTemplateSeeder`. Cek file `database/seeders/InvoiceTemplateSeeder.php` untuk daftar lengkap. Template default workspace: `classic`.

---

## 9. Quick Smoke Test

Setelah seed berjalan, login dengan credential di atas, lalu cek halaman-halaman berikut:

- [ ] `/dashboard` → ada metric (total invoiced, paid, outstanding)
- [ ] `/clients` → 10 client list
- [ ] `/products` → 15 produk
- [ ] `/invoices` → 20 invoice dengan status berwarna-warni
- [ ] `/payments` → list payment
- [ ] `/recurring` → 2 recurring (1 active, 1 paused)
- [ ] `/reports` → grafik populated
- [ ] `/workspace/settings` → form pre-filled dengan data Demo Company

---

## 10. Reset Tanpa Hilangkan Schema

Kalau hanya mau refresh data tanpa drop tabel:

```bash
php artisan db:seed --class=DemoDataSeeder
```

`DemoDataSeeder` pakai `firstOrCreate` untuk user & workspace, jadi aman dijalankan berulang. Tapi `Client`, `Product`, `Invoice`, `Payment`, `RecurringInvoice` **selalu di-create** (bukan firstOrCreate) — jadi data akan duplikat. Untuk fresh state gunakan `migrate:fresh --seed`.
