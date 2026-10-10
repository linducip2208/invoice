# Architecture Document — Invocely

## 1. High-Level Architecture

```
┌────────────────────────────────────────────────────┐
│                   Client Browser                     │
│               (HTML + Tailwind + Alpine.js)          │
└──────────────────────┬─────────────────────────────┘
                       │ HTTP/HTTPS
                       ▼
┌────────────────────────────────────────────────────┐
│                  Nginx / Apache                      │
│                  (Web Server)                        │
└──────────────────────┬─────────────────────────────┘
                       │
                       ▼
┌────────────────────────────────────────────────────┐
│                 Laravel 13 (PHP 8.3)                │
│  ┌──────────────────────────────────────────────┐  │
│  │            HTTP Kernel / Middleware            │  │
│  │  ┌──────────┐ ┌───────────┐ ┌─────────────┐  │  │
│  │  │  Auth    │ │  Tenant   │ │  Rate Limit  │  │  │
│  │  └──────────┘ └───────────┘ └─────────────┘  │  │
│  └──────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────┐  │
│  │               Controllers                      │  │
│  │  Auth / Workspace / Client / Product /        │  │
│  │  Invoice / Payment / Recurring / Dashboard    │  │
│  └──────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────┐  │
│  │                Services (Business Logic)       │  │
│  │  InvoiceService / PaymentService /            │  │
│  │  RecurringService / PdfService / EmailService │  │
│  └──────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────┐  │
│  │               Models (Eloquent ORM)            │  │
│  └──────────────────────────────────────────────┘  │
└─────────────┬───────────────────┬──────────────────┘
              │                   │
              ▼                   ▼
┌──────────────────┐  ┌──────────────────────┐
│   MySQL 8.4      │  │   Storage (Local/S3)  │
│   (Database)     │  │   Logos, PDF files     │
└──────────────────┘  └──────────────────────┘
```

---

## 2. Directory Structure

```
invocely/
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Workspace.php
│   │   ├── WorkspaceUser.php
│   │   ├── Client.php
│   │   ├── Product.php
│   │   ├── Invoice.php
│   │   ├── InvoiceItem.php
│   │   ├── Payment.php
│   │   ├── RecurringInvoice.php
│   │   ├── InvoiceTemplate.php
│   │   └── EmailLog.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                (Breeze auth)
│   │   │   ├── DashboardController.php
│   │   │   ├── WorkspaceController.php
│   │   │   ├── ClientController.php
│   │   │   ├── ProductController.php
│   │   │   ├── InvoiceController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── RecurringInvoiceController.php
│   │   │   ├── InvoiceTemplateController.php
│   │   │   ├── EmailInvoiceController.php
│   │   │   ├── PublicInvoiceController.php
│   │   │   └── ProgrammaticSeoController.php
│   │   └── Middleware/
│   │       └── TenantMiddleware.php
│   ├── Services/
│   │   ├── InvoiceService.php
│   │   ├── PdfService.php
│   │   ├── EmailService.php
│   │   └── RecurringInvoiceService.php
│   └── Console/
│       └── Commands/
│           └── GenerateRecurringInvoices.php
├── resources/
│   └── views/
│       ├── layouts/
│       ├── components/
│       ├── auth/
│       ├── dashboard/
│       ├── workspace/
│       ├── clients/
│       ├── products/
│       ├── invoices/
│       │   ├── templates/          (PDF templates)
│       │   │   ├── classic.blade.php
│       │   │   ├── modern.blade.php
│       │   │   ├── minimal.blade.php
│       │   │   ├── bold.blade.php
│       │   │   └── elegant.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── show.blade.php
│       │   └── index.blade.php
│       ├── payments/
│       ├── recurring/
│       ├── templates/
│       ├── public/                  (Public invoice view)
│       ├── seo/                    (Programmatic SEO)
│       └── emails/
├── routes/
│   ├── web.php
│   └── console.php
├── database/
│   └── migrations/
└── docs/
```

---

## 3. Tenant Isolation Strategy

### Approach: Shared Database, Workspace-Scoped Queries

```
Semua tenant share database yang sama.
Setiap query di-scope dengan `where('workspace_id', $currentWorkspace->id)`.

Middleware `TenantMiddleware` akan:
1. Cek user sudah login
2. Ambil current workspace dari session
3. Inject ke global scope atau trait

Model menggunakan trait `BelongsToWorkspace`:
- Auto-scope queries ke current workspace
- Auto-set workspace_id saat create
```

### Tenant Switching
```
Session: current_workspace_id
User bisa switch workspace via dropdown di navbar
Jika user hanya punya 1 workspace → tidak ada dropdown
```

---

## 4. PDF Generation Flow

```
User klik "Download PDF" atau "Send Email"
  → InvoiceService::generatePdf($invoice)
    → Render Blade template (invoice.templates.{template_name})
    → Pass data: invoice + items + workspace + client
    → DomPDF convert HTML → PDF
    → Simpan ke storage/app/invoices/{workspace_id}/{invoice_id}.pdf
    → Return PDF path
  → Download: return response()->download($path)
  → Email: attach PDF ke Mail mailable
```

---

## 5. Queue Architecture

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  Web Request  │────▶│  Database    │────▶│  Queue Worker │
│  (Controller) │     │  Queue       │     │  (php artisan │
│               │     │  (jobs table)│     │   queue:work) │
└──────────────┘     └──────────────┘     └──────┬───────┘
                                                 │
                    ┌────────────────────────────┘
                    ▼
            ┌──────────────┐
            │  Job Classes  │
            │  - SendInvoiceEmail │
            │  - GenerateInvoicePdf│
            └──────────────┘
```

---

## 6. Recurring Invoice Flow

```
┌─────────────────────┐
│  Scheduler (Cron)    │  Setiap jam / daily
│  Laravel Scheduler   │
└──────────┬──────────┘
           │
           ▼
┌──────────────────────────────────────┐
│  GenerateRecurringInvoices Command    │
│  1. Query recurring_invoices WHERE    │
│     status = 'active'                 │
│     AND next_run_date <= now()        │
│  2. For each:                         │
│     - Create new invoice dari data    │
│     - Update next_run_date            │
│     - Increment current_occurrence    │
│     - If max_occurrences reached →    │
│       status = 'completed'            │
└──────────────────────────────────────┘
```

---

## 7. Security

| Measure | Implementation |
|---------|---------------|
| CSRF | Laravel built-in VerifyCsrfToken |
| XSS | Blade `{{ }}` auto-escape |
| SQL Injection | Eloquent parameterized queries |
| Auth | Breeze (bcrypt hashed passwords) |
| File Upload | Validate mime type (image only for logo) |
| Rate Limit | Throttle login & public view endpoints |
| Tenant Isolation | Global scope on workspace_id |
| Public Token | UUID v4 untuk invoice public link (un-guessable) |
