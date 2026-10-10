# Reporting & Business Intelligence — Sprint 7

> Insight bisnis untuk owner. Aging, cashflow forecast, P&L, tax summary, custom dashboard.

---

## 7.1 Aging Report ❌

(Sudah dispek di [02-quick-wins.md § 1.6](02-quick-wins.md#16-aging-report). Salah satu Sprint 1 priority — duplicate listing di sini sebagai bagian dari Reports module.)

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 1 hari

---

## 7.2 Cashflow Forecast ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 2 hari

### Why
Owner perlu lihat 30/60/90 hari ke depan: berapa expected income, berapa expense scheduled. Decision-making (boleh hire? boleh expand?).

### Database Schema
Tidak butuh tabel baru. Aggregate query.

### Routes
```
GET /reports/cashflow                  reports.cashflow
GET /reports/cashflow.csv              reports.cashflow.csv
GET /api/reports/cashflow              api.reports.cashflow.json   (untuk widget dashboard)
```

### Service
- `App\Services\Reports\CashflowForecaster::forecast($workspace, $days = 90): array`
  - **Inflow** sources:
    - Open invoices (status=sent/partial) — group by due_date bucket
    - Recurring invoices yang bakal generate
    - Quotes accepted yang belum jadi invoice
  - **Outflow** sources:
    - Expenses billable yang belum di-invoice (negatif untuk owner kalau pay vendor first)
    - Subscription bills (kalau ada modul vendor)
  - Result: array per minggu/bulan dengan inflow, outflow, net

### View
- `resources/views/reports/cashflow.blade.php` — line chart Chart.js
  - 3 lines: Inflow proyeksi, Outflow, Net
  - Toggle scenario: optimis (semua bayar on-time), realistic (apply avg client late days), pessimist (asumsi 30% delay)

### Edge Cases
- Klien tidak realiable → factor in `client.payment_reliability` untuk realistic scenario
- Recurring invoice dengan end_date → respect, jangan project beyond

### Acceptance Criteria
- [ ] Page chart 30/60/90 hari
- [ ] 3 scenario toggle
- [ ] Drill-down per minggu → list invoice expected
- [ ] Export CSV

---

## 7.3 Profit & Loss per Klien ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 2 hari

### Why
"Klien yang paling profit?" "Klien yang banyak revenue tapi expense-nya lebih banyak — net loss." Tanpa P&L, ga ketauan klien red flag.

### Database Schema
Tidak butuh tabel baru. Pure aggregate.

### Routes
```
GET /reports/p-l                            reports.pl
GET /reports/p-l/{client}                   reports.pl.client
GET /reports/p-l.csv                        reports.pl.csv
```

### Service
- `App\Services\Reports\PnlReportService`
  - Per client:
    - Revenue: SUM(payments.amount) untuk invoices.client_id = X
    - Expense: SUM(expenses.amount) untuk billable=true AND client_id = X
    - Time cost: SUM(time_entries.hours × hourly_rate) untuk client_id = X
    - Net: revenue - expense - time_cost
    - Margin %: net / revenue

### View
- `resources/views/reports/pl.blade.php`
  - Sorted by margin descending
  - Color: hijau (>30%), kuning (10-30%), merah (<10%)
  - Click client → drill-down per project / invoice / expense
- `resources/views/reports/pl-client.blade.php` — detail per klien

### Acceptance Criteria
- [ ] Table top 20 klien by margin
- [ ] Drill-down ke level project & invoice
- [ ] Filter by date range
- [ ] Highlight unprofitable clients

---

## 7.4 Tax Summary Report ❌

**Status:** ❌ | **Priority:** ⭐ High (untuk PKP) | **Effort:** 1 hari

### Why
SPT Masa PPN — bulanan, accountant butuh rekap semua faktur output. Tanpa report, mereka manual export Excel.

### Database Schema
Pakai `invoice_tax_details` table (Section 5 dari roadmap-v2/01).

### Routes
```
GET /reports/tax/ppn                   reports.tax.ppn         (PPN keluaran)
GET /reports/tax/pph23                 reports.tax.pph23       (PPh 23 dipotong)
GET /reports/tax/efaktur-status        reports.tax.efaktur     (status submission)
GET /reports/tax/{type}.csv            reports.tax.csv
GET /reports/tax/{type}.xlsx           reports.tax.xlsx        (format Excel SPT)
```

### Service
- `App\Services\Reports\TaxReportService`
  - PPN keluaran: list semua invoice issued yang ada PPN
    - Columns: tanggal, no_faktur, NPWP buyer, nama buyer, DPP, PPN, status efaktur
  - PPh 23: list invoice yang ada pemotongan
    - Columns: tanggal, no_invoice, NPWP klien, nama klien, jumlah dipotong, object code

### View
- `resources/views/reports/tax/ppn.blade.php`
- `resources/views/reports/tax/pph23.blade.php`
- `resources/views/reports/tax/efaktur-status.blade.php`

### Acceptance Criteria
- [ ] Filter per bulan
- [ ] Export Excel format yang siap import ke aplikasi DJP / Coretax
- [ ] Total per bulan jelas
- [ ] Status faktur (approved / draft / rejected)

---

## 7.5 Revenue Trends & Charts ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 1 hari

### Why
Owner buka dashboard tiap pagi, lihat trend. Sudah ada di dashboard simple, perlu di-enhance jadi modul Reports.

### Routes
```
GET /reports/revenue              reports.revenue
GET /reports/revenue/by-client    reports.revenue.by-client
GET /reports/revenue/by-product   reports.revenue.by-product
GET /reports/revenue/by-month     reports.revenue.by-month
```

### Service
- `App\Services\Reports\RevenueAnalyzer`

### View
- `resources/views/reports/revenue.blade.php`
  - Multi-chart: trend bulanan (12 bulan), top 10 klien pie, top 10 produk bar
  - Comparison year-over-year
  - Forecast next quarter (linear regression simple)

### Acceptance Criteria
- [ ] Chart trend bulanan dengan YoY comparison
- [ ] Top klien & top produk
- [ ] Drill-down per chart
- [ ] Export PNG / PDF

---

## 7.6 Custom Dashboard Widget ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 3 hari

### Why
Setiap user punya prioritas beda — accountant fokus tax, sales fokus pipeline, owner fokus revenue. Drag-drop widget biar mereka bikin layout sendiri.

### Database Schema
**Tabel BARU `dashboard_widgets`** (lihat 01-DATABASE-RELATIONS Section 17).

### Available Widget Types
| Type | Description | Size |
|---|---|---|
| `revenue_trend` | Line chart 12 bulan | md/lg |
| `aging_summary` | Bucket bars | sm/md |
| `top_clients` | Top 5 by revenue | sm/md |
| `top_unpaid` | Top 5 unpaid invoice | md |
| `cashflow_30d` | Mini chart 30 hari | sm |
| `recent_activity` | Last 10 activity log | md |
| `pending_approvals` | Approver inbox snippet | sm |
| `time_today` | Hours logged today | sm |
| `tax_due_this_month` | PPN+PPh23 yang harus disetor | sm |
| `pipeline` | Quotes status funnel | md |
| `recent_invoices` | Last 10 invoices | md/lg |
| `notification_count` | Badge jumlah notif | sm |

### Routes
```
GET    /dashboard                       dashboard
GET    /dashboard/customize             dashboard.customize
POST   /dashboard/widgets               dashboard.widgets.store
PUT    /dashboard/widgets/{widget}      dashboard.widgets.update
PUT    /dashboard/widgets/reorder       dashboard.widgets.reorder
DELETE /dashboard/widgets/{widget}      dashboard.widgets.destroy
GET    /api/dashboard/widget/{type}     api.dashboard.widget        (data per widget)
```

### Controller
- `App\Http\Controllers\DashboardWidgetController`

### Service
- `App\Services\Dashboard\WidgetRegistry` — defines available types + data resolvers
- `App\Services\Dashboard\WidgetDataProvider` — fetch data per type

### UI
- Customize mode: drag-drop widget reposition (SortableJS)
- Add widget: modal dengan grid available types
- Setiap widget render via Blade component `<x-widget type="..." config="..." />`

### Acceptance Criteria
- [ ] Default widgets seeded saat user pertama buka dashboard
- [ ] Drag-drop reorder
- [ ] Add/remove widget
- [ ] Per-user layout (bukan workspace-shared)
- [ ] Mobile responsive (stack vertically)

---

## 7.7 Scheduled Email Reports ❌

**Status:** ❌ | **Priority:** 🌱 Low | **Effort:** 1.5 hari

### Why
Owner mau report mingguan/bulanan otomatis ke email tanpa harus login. Set & forget.

### Database Schema
Tabel BARU `report_subscriptions`:
```sql
id BIGINT PK
workspace_id BIGINT FK
user_id BIGINT FK
report_type VARCHAR(50)  -- 'weekly_summary','monthly_pl','quarterly_tax', dll
frequency ENUM('weekly','monthly','quarterly')
day_of_week TINYINT NULL  -- 1-7 (Mon-Sun)
day_of_month TINYINT NULL  -- 1-31
hour TINYINT NOT NULL DEFAULT 8
last_sent_at TIMESTAMP NULL
next_send_at TIMESTAMP NOT NULL
is_active BOOLEAN DEFAULT TRUE
config JSON NULL  -- per-report customization
created_at, updated_at
INDEX(workspace_id, is_active, next_send_at)
```

### Routes
```
GET    /reports/subscriptions          report-subscriptions.index
POST   /reports/subscriptions          report-subscriptions.store
PUT    /reports/subscriptions/{sub}    report-subscriptions.update
DELETE /reports/subscriptions/{sub}    report-subscriptions.destroy
```

### Scheduler
```php
$schedule->command('reports:send-subscribed')->hourly();
```

Job select WHERE next_send_at <= now() AND is_active → render report → email PDF/HTML → update next_send_at.

### Acceptance Criteria
- [ ] User bisa subscribe ke report
- [ ] Auto-send via cron
- [ ] Unsubscribe link di email
- [ ] Preview report sebelum subscribe
