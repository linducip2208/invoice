# Product Requirements Document — Invocely

## 1. Executive Summary
**Invocely** adalah aplikasi SaaS invoicing multi-tenant berbasis Laravel + Blade + MySQL. Aplikasi ini memungkinkan bisnis kecil-menengah membuat, mengelola, dan mengirim invoice profesional ke klien melalui email, melacak pembayaran, dan menghasilkan laporan keuangan — semuanya dari satu dashboard.

---

## 2. Target User
- **Freelancer** — butuh invoice cepat dengan branding sendiri
- **Small Business** — UKM yang kelola 10-500 invoice/bulan
- **Agency** — butuh multi-user per workspace
- **Client (end-customer)** — menerima & melihat invoice via link publik

---

## 3. Core Features

### 3.1 Authentication & Onboarding
| ID | Feature | Priority |
|----|---------|----------|
| A1 | Register (name, email, password, company name) | P0 |
| A2 | Login / Logout | P0 |
| A3 | Email verification | P1 |
| A4 | Password reset | P1 |
| A5 | Onboarding wizard (setup company profile) | P1 |

### 3.2 Multi-Tenant / Workspace
| ID | Feature | Priority |
|----|---------|----------|
| W1 | Auto-create workspace saat register | P0 |
| W2 | Workspace settings (company name, logo, address, tax ID) | P0 |
| W3 | Invite team member ke workspace (owner, admin, member) | P1 |
| W4 | Switch workspace (jika user punya >1) | P2 |

### 3.3 Client Management
| ID | Feature | Priority |
|----|---------|----------|
| C1 | CRUD Client (name, email, phone, address, tax ID) | P0 |
| C2 | Import client dari CSV | P2 |
| C3 | Client portal link (client bisa lihat semua invoice mereka) | P1 |

### 3.4 Product / Service Items
| ID | Feature | Priority |
|----|---------|----------|
| P1 | CRUD Product/Service (name, description, unit price, tax) | P0 |
| P2 | Kategori produk | P2 |

### 3.5 Invoice Management
| ID | Feature | Priority |
|----|---------|----------|
| I1 | Create invoice (pilih client, tambah line items, tax, discount) | P0 |
| I2 | Edit / Delete invoice (sebelum dikirim) | P0 |
| I3 | Duplicate invoice | P1 |
| I4 | Invoice numbering (custom prefix, auto-increment) | P0 |
| I5 | Status invoice: draft, sent, paid, overdue, cancelled | P0 |
| I6 | Due date & late fee | P1 |
| I7 | Multi-currency support | P2 |
| I8 | Discount per item & global discount | P0 |
| I9 | Tax per item & global tax | P0 |
| I10 | Catatan / terms & conditions | P1 |

### 3.6 Invoice Templates
| ID | Feature | Priority |
|----|---------|----------|
| T1 | 5 template bawaan (Classic, Modern, Minimal, Bold, Elegant) | P0 |
| T2 | Custom warna (primary, accent) per template | P0 |
| T3 | Upload logo perusahaan | P0 |
| T4 | Client bisa customize template tampilan invoice mereka | P1 |

### 3.7 PDF Generation
| ID | Feature | Priority |
|----|---------|----------|
| D1 | Generate PDF dari HTML template (DomPDF) | P0 |
| D2 | Download PDF | P0 |
| D3 | Preview PDF sebelum kirim | P0 |
| D4 | Attachment PDF di email | P0 |

### 3.8 Email Invoice
| ID | Feature | Priority |
|----|---------|----------|
| E1 | Kirim invoice via email ke client | P0 |
| E2 | Custom email subject & message | P1 |
| E3 | CC / BCC | P2 |
| E4 | Email log (tracking sent emails) | P2 |

### 3.9 Payment Tracking
| ID | Feature | Priority |
|----|---------|----------|
| PT1 | Record manual payment (amount, date, method, note) | P0 |
| PT2 | Payment status: paid, partial, unpaid | P0 |
| PT3 | Payment history per invoice | P0 |
| PT4 | Payment gateway integration (Midtrans, Stripe, Xendit) — via dynamic provider config | P2 |

### 3.10 Recurring Invoice
| ID | Feature | Priority |
|----|---------|----------|
| R1 | Set recurring schedule (daily, weekly, monthly, yearly) | P0 |
| R2 | Auto-generate invoice sesuai schedule (via scheduler) | P0 |
| R3 | Pause / Resume recurring | P1 |
| R4 | End date or max occurrences | P1 |

### 3.11 Dashboard & Reports
| ID | Feature | Priority |
|----|---------|----------|
| DR1 | Dashboard: total revenue, outstanding, paid, overdue | P0 |
| DR2 | Chart: revenue over time (monthly) | P0 |
| DR3 | Chart: invoice status breakdown | P0 |
| DR4 | Top clients by revenue | P1 |
| DR5 | Export report CSV | P1 |

### 3.12 Public Invoice Page
| ID | Feature | Priority |
|----|---------|----------|
| PU1 | Public URL unik per invoice (token-based) | P0 |
| PU2 | Client bisa lihat & download PDF tanpa login | P0 |
| PU3 | Client bisa bayar via link (jika payment gateway aktif) | P2 |

### 3.13 Programmatic SEO (per global preference)
| ID | Feature | Priority |
|----|---------|----------|
| SEO1 | `/best-invoicing-tools-{year}` | P1 |
| SEO2 | `/alternatives-to-{slug}` | P1 |
| SEO3 | `/compare/{a}-vs-{b}` | P1 |
| SEO4 | `/invoice-templates` — gallery template | P1 |
| SEO5 | SEO pages with JSON-LD, meta tags, 300+ words, sitemap auto-include | P1 |

---

## 4. Non-Functional Requirements

| ID | Requirement | Detail |
|----|-------------|--------|
| NFR1 | Security | CSRF, XSS, SQL injection protection (Laravel built-in), encrypted keys |
| NFR2 | Performance | Query optimization, eager loading, pagination, PDF queue |
| NFR3 | Scalability | Multi-tenant isolation via workspace_id scope |
| NFR4 | Email | Queue-based email sending (database queue) |
| NFR5 | Responsive | Mobile-friendly Blade templates (Tailwind CSS) |

---

## 5. Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Database | MySQL 8.4 |
| PDF | DomPDF (barryvdh/laravel-dompdf) |
| Auth | Laravel Breeze (Blade) |
| Queue | Database queue |
| Email | Laravel Mail + SMTP configurable |

---

## 6. User Flow

```
Register → Verify Email → Setup Company → Dashboard
  → Add Clients
  → Add Products/Services
  → Create Invoice (pilih client → tambah items → set template → preview → send)
  → Track Payments
  → View Reports
  → Manage Recurring Invoices
```
