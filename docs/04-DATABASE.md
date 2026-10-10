# Relational Database Schema — Invocely

## Migration: users

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Migration: workspaces

```sql
CREATE TABLE workspaces (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    logo_path VARCHAR(255) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) NULL DEFAULT 'Indonesia',
    postal_code VARCHAR(20) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    tax_id VARCHAR(100) NULL,
    invoice_prefix VARCHAR(20) NOT NULL DEFAULT 'INV-',
    next_invoice_no INT UNSIGNED NOT NULL DEFAULT 1,
    default_template VARCHAR(50) NOT NULL DEFAULT 'classic',
    primary_color VARCHAR(7) NOT NULL DEFAULT '#2563EB',
    accent_color VARCHAR(7) NOT NULL DEFAULT '#1E40AF',
    currency VARCHAR(10) NOT NULL DEFAULT 'IDR',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Migration: workspace_user

```sql
CREATE TABLE workspace_user (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workspace_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role ENUM('owner', 'admin', 'member') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (workspace_id, user_id)
);
```

## Migration: clients

```sql
CREATE TABLE clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workspace_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) NULL DEFAULT 'Indonesia',
    postal_code VARCHAR(20) NULL,
    tax_id VARCHAR(100) NULL,
    notes TEXT NULL,
    view_token CHAR(36) NOT NULL UNIQUE,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    INDEX idx_clients_workspace (workspace_id),
    INDEX idx_clients_token (view_token)
);
```

## Migration: products

```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workspace_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    unit_price DECIMAL(15, 2) NOT NULL DEFAULT 0,
    tax_rate DECIMAL(5, 2) NOT NULL DEFAULT 0,
    category VARCHAR(100) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    INDEX idx_products_workspace (workspace_id)
);
```

## Migration: invoices

```sql
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workspace_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    invoice_no VARCHAR(50) NOT NULL,
    status ENUM('draft','sent','paid','partial','overdue','cancelled') NOT NULL DEFAULT 'draft',
    recurring_invoice_id BIGINT UNSIGNED NULL,
    template VARCHAR(50) NOT NULL DEFAULT 'classic',
    primary_color VARCHAR(7) NULL,
    accent_color VARCHAR(7) NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal DECIMAL(15, 2) NOT NULL DEFAULT 0,
    tax_rate DECIMAL(5, 2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
    discount_type ENUM('fixed', 'percent', 'none') NOT NULL DEFAULT 'none',
    discount_value DECIMAL(15, 2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
    total DECIMAL(15, 2) NOT NULL DEFAULT 0,
    amount_paid DECIMAL(15, 2) NOT NULL DEFAULT 0,
    balance_due DECIMAL(15, 2) NOT NULL DEFAULT 0,
    currency VARCHAR(10) NOT NULL DEFAULT 'IDR',
    notes TEXT NULL,
    terms TEXT NULL,
    public_token CHAR(36) NOT NULL UNIQUE,
    last_sent_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (recurring_invoice_id) REFERENCES recurring_invoices(id) ON DELETE SET NULL,
    INDEX idx_invoices_workspace_status (workspace_id, status),
    INDEX idx_invoices_client (client_id),
    INDEX idx_invoices_token (public_token),
    INDEX idx_invoices_due_status (due_date, status),
    INDEX idx_invoices_no (workspace_id, invoice_no)
);
```

## Migration: invoice_items

```sql
CREATE TABLE invoice_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    description TEXT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL DEFAULT 1,
    unit_price DECIMAL(15, 2) NOT NULL DEFAULT 0,
    tax_rate DECIMAL(5, 2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
    discount_type ENUM('fixed', 'percent', 'none') NOT NULL DEFAULT 'none',
    discount_value DECIMAL(15, 2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(15, 2) NOT NULL DEFAULT 0,
    total DECIMAL(15, 2) NOT NULL DEFAULT 0,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_items_invoice (invoice_id)
);
```

## Migration: payments

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    workspace_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash','bank_transfer','credit_card','other') NOT NULL DEFAULT 'bank_transfer',
    reference_no VARCHAR(100) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    INDEX idx_payments_invoice (invoice_id),
    INDEX idx_payments_workspace (workspace_id)
);
```

## Migration: recurring_invoices

```sql
CREATE TABLE recurring_invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workspace_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    template VARCHAR(50) NOT NULL DEFAULT 'classic',
    primary_color VARCHAR(7) NULL,
    accent_color VARCHAR(7) NULL,
    frequency ENUM('daily','weekly','monthly','yearly') NOT NULL DEFAULT 'monthly',
    interval_value INT UNSIGNED NOT NULL DEFAULT 1,
    next_run_date DATE NOT NULL,
    end_date DATE NULL,
    max_occurrences INT UNSIGNED NULL,
    current_occurrence INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('active','paused','completed') NOT NULL DEFAULT 'active',
    last_invoice_id BIGINT UNSIGNED NULL,
    invoice_data JSON NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (last_invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    INDEX idx_recurring_next (next_run_date, status),
    INDEX idx_recurring_workspace (workspace_id)
);
```

## Migration: invoice_templates

```sql
CREATE TABLE invoice_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workspace_id BIGINT UNSIGNED NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    preview_image VARCHAR(255) NULL,
    config JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    UNIQUE INDEX idx_template_slug_workspace (slug, workspace_id)
);
```

## Migration: email_logs

```sql
CREATE TABLE email_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workspace_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED NOT NULL,
    to_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NULL,
    status ENUM('sent','failed') NOT NULL DEFAULT 'sent',
    error_message TEXT NULL,
    sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_email_logs_invoice (invoice_id)
);
```

## Migration: invoice_public_views

```sql
CREATE TABLE invoice_public_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    viewed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_views_invoice (invoice_id)
);
```

---

## jobs table (Laravel queue)

```sql
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX idx_jobs_queue (queue)
);
```

---

## Total Tables: 13

| # | Table | Purpose |
|---|-------|---------|
| 1 | users | Authentication |
| 2 | workspaces | Multi-tenant container |
| 3 | workspace_user | Pivot user-workspace with role |
| 4 | clients | Customer/client data |
| 5 | products | Product/service catalog |
| 6 | invoices | Invoice header |
| 7 | invoice_items | Invoice line items |
| 8 | payments | Payment records |
| 9 | recurring_invoices | Recurring schedule & template |
| 10 | invoice_templates | Template definitions |
| 11 | email_logs | Email delivery tracking |
| 12 | invoice_public_views | Public view analytics |
| 13 | jobs | Laravel queue jobs |
