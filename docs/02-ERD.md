# Entity Relationship Diagram — Invocely

## Tables & Relationships

```
┌─────────────────┐       ┌──────────────────────┐
│     users       │       │     workspaces        │
├─────────────────┤       ├──────────────────────┤
│ id (PK)         │       │ id (PK)              │
│ name            │       │ name                  │
│ email           │       │ slug (unique)         │
│ password        │       │ logo_path             │
│ email_verified  │       │ address               │
│ created_at      │       │ city                  │
│ updated_at      │       │ state                 │
└────────┬────────┘       │ country               │
         │                │ postal_code           │
         │                │ phone                 │
         │                │ email                 │
         │                │ tax_id                │
         │                │ invoice_prefix        │
         │                │ next_invoice_no       │
         │                │ default_template      │
         │                │ primary_color         │
         │                │ accent_color           │
         │                │ created_at            │
         │                │ updated_at            │
         │                └───────────┬───────────┘
         │                            │
         │          ┌─────────────────┘
         │          │
┌────────┴──────────┴────────┐
│     workspace_user         │ (pivot)
├────────────────────────────┤
│ id (PK)                    │
│ workspace_id (FK)          │
│ user_id (FK)               │
│ role (owner/admin/member)  │
│ created_at                 │
└────────────────────────────┘
```

```
┌──────────────────────┐
│     clients           │  (scoped to workspace)
├──────────────────────┤
│ id (PK)              │
│ workspace_id (FK)    │
│ name                 │
│ company_name         │
│ email                │
│ phone                │
│ address              │
│ city                 │
│ state                │
│ country              │
│ postal_code          │
│ tax_id               │
│ notes                │
│ view_token (unique)  │ ◄── untuk public client portal
│ created_at           │
│ updated_at           │
└──────────┬───────────┘
           │
           │ 1:N
           ▼
┌──────────────────────┐       ┌──────────────────────────┐
│     invoices          │       │     invoice_items         │
├──────────────────────┤       ├──────────────────────────┤
│ id (PK)              │       │ id (PK)                  │
│ workspace_id (FK)    │       │ invoice_id (FK)          │
│ client_id (FK)       │───────│ product_id (FK, nullable) │
│ invoice_no           │       │ description              │
│ status               │       │ quantity                 │
│ (draft/sent/paid/    │       │ unit_price               │
│  overdue/cancelled)  │       │ tax_rate (%)             │
│ template             │       │ tax_amount               │
│ primary_color        │       │ discount_type            │
│ accent_color         │       │ (fixed/percent)          │
│ issue_date           │       │ discount_value           │
│ due_date             │       │ subtotal                 │
│ subtotal             │       │ total                    │
│ tax_rate             │       │ sort_order               │
│ tax_amount           │       │ created_at               │
│ discount_type        │       │ updated_at               │
│ discount_value       │       └──────────────────────────┘
│ discount_amount      │
│ total                │       ┌──────────────────────────┐
│ amount_paid          │       │     payments              │
│ balance_due          │       ├──────────────────────────┤
│ currency             │       │ id (PK)                  │
│ notes                │       │ invoice_id (FK)          │
│ terms                │       │ workspace_id (FK)        │
│ public_token(unique) │───┐   │ amount                   │
│ last_sent_at         │   │   │ payment_date             │
│ created_at           │   │   │ payment_method           │
│ updated_at           │   │   │ (cash/transfer/credit/   │
└──────────┬───────────┘   │   │  other)                  │
           │               │   │ reference_no             │
           │ 1:N           │   │ notes                    │
           ▼               │   │ created_at               │
┌──────────────────────┐   │   │ updated_at               │
│   recurring_invoices  │   │   └──────────────────────────┘
├──────────────────────┤   │
│ id (PK)              │   │
│ workspace_id (FK)    │   │
│ client_id (FK)       │   │
│ template             │   │
│ frequency            │   │   ┌──────────────────────────┐
│ (daily/weekly/       │   │   │   email_logs             │
│  monthly/yearly)     │   │   ├──────────────────────────┤
│ interval (1,2,3...)  │   │   │ id (PK)                  │
│ next_run_date        │   │   │ workspace_id (FK)        │
│ end_date (nullable)  │   │   │ invoice_id (FK)          │
│ max_occurrences      │   │   │ to_email                │
│ current_occurrence   │   │   │ subject                  │
│ status (active/      │   │   │ body                     │
│  paused/completed)   │   │   │ sent_at                 │
│ last_invoice_id (FK) │   │   │ status (sent/failed)    │
│ invoice_data (JSON)  │   │   │ error_message            │
│ created_at           │   │   │ created_at               │
│ updated_at           │   │   └──────────────────────────┘
└──────────────────────┘   │
                           │   ┌──────────────────────────┐
                           │   │   invoice_public_views    │
                           │   ├──────────────────────────┤
                           │   │ id (PK)                  │
                           │   │ invoice_id (FK)          │
                           │───│ ip_address               │
                           │   │ user_agent               │
                           │   │ viewed_at                │
                           │   └──────────────────────────┘
                           │
┌──────────────────────┐   │
│   products            │   │
├──────────────────────┤   │
│ id (PK)              │   │
│ workspace_id (FK)    │   │
│ name                 │   │
│ description          │   │
│ unit_price           │   │
│ tax_rate (%)         │   │
│ category             │   │
│ is_active            │   │
│ created_at           │   │
│ updated_at           │   │
└──────────────────────┘   │
                           │
┌──────────────────────┐   │
│  invoice_templates    │   │
├──────────────────────┤   │
│ id (PK)              │   │
│ workspace_id (FK)    │───┘ (nullable = global template)
│ name                 │
│ slug                 │
│ is_default           │
│ preview_image        │
│ config (JSON)        │  ◄── layout config, color defaults
│ created_at           │
│ updated_at           │
└──────────────────────┘
```

---

## Key Relationships

| Relationship | Type | Notes |
|-------------|------|-------|
| User → Workspace | M:N via `workspace_user` | Role: owner, admin, member |
| Workspace → Client | 1:N | Semua resource di-scope workspace |
| Workspace → Invoice | 1:N | |
| Client → Invoice | 1:N | |
| Invoice → InvoiceItem | 1:N | |
| Invoice → Payment | 1:N | Multiple partial payments |
| Workspace → Product | 1:N | |
| Workspace → RecurringInvoice | 1:N | |
| Workspace → InvoiceTemplate | 1:N | |
| Invoice → EmailLog | 1:N | Tracking email dikirim |

---

## Index Strategy

| Table | Index | Reason |
|-------|-------|--------|
| invoices | `workspace_id + status` | Dashboard query |
| invoices | `client_id` | Get client invoices |
| invoices | `public_token` | Public view lookup |
| invoices | `due_date + status` | Overdue detection |
| clients | `workspace_id` | Tenant isolation |
| clients | `view_token` | Client portal |
| payments | `invoice_id` | Payment history |
| payments | `workspace_id` | Tenant isolation |
| recurring_invoices | `next_run_date + status` | Scheduler pickup |
| workspace_user | `user_id` | Get user workspaces |
