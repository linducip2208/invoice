# Time Tracking & Project Management — Sprint 5

> Saat user freelance/agency, project tracking jadi pondasi: time → expense → invoice. Sebagian tabel sudah ada, tinggal UI + logic.

---

## 5A. Time Tracker UI 🟦

**Status:** 🟦 (tabel `time_entries` sudah ada) | **Priority:** ⭐ High | **Effort:** 3 hari

### Why
Freelancer charge per jam butuh stopwatch + log harian. Tanpa ini, mereka pakai Toggl/Clockify lalu manual entry ke Invocely. Friction.

### Database Schema
**Tabel `time_entries` SUDAH ADA**. Kolom existing:
- workspace_id, client_id, user_id, description, hours, hourly_rate
- entry_date, billable, invoiced, invoice_id

**Modifikasi:**
- `started_at TIMESTAMP NULL` — untuk live timer
- `ended_at TIMESTAMP NULL`
- `is_running BOOLEAN DEFAULT FALSE`
- `project_id BIGINT NULL FK→projects` (lihat 5C)
- `tags JSON NULL` — array of strings

### Routes
```
GET    /time                          time.index
GET    /time/timer                    time.timer        (live timer page)
POST   /time/start                    time.start        (start stopwatch)
POST   /time/{entry}/stop             time.stop
POST   /time                          time.store        (manual log)
PUT    /time/{entry}                  time.update
DELETE /time/{entry}                  time.destroy
GET    /time/calendar                 time.calendar     (month view)
GET    /time/report                   time.report       (per client/project)
POST   /time/bulk-invoice             time.bulk-invoice (select entries → create invoice)
```

### Controller
- `App\Http\Controllers\TimeEntryController`

### Service
- `App\Services\Time\TimerService`
  - `start(User, ClientId, ?ProjectId, $description, $rate)` — close any existing running timer
  - `stop(TimeEntry)` — set ended_at, hours = diff
- `App\Services\Time\InvoiceFromTimeEntries`
  - Input: array of entry IDs
  - Output: Invoice draft dengan line items per entry (atau grouped per project/client)

### View
- `resources/views/time/index.blade.php` — list dengan filter
- `resources/views/time/timer.blade.php` — Alpine.js stopwatch dengan client/project picker
- `resources/views/time/calendar.blade.php` — FullCalendar.js per hari
- `resources/views/time/bulk-invoice.blade.php` — checklist entries → invoice

### UI Highlights
- Floating timer widget (top bar) — visible di semua halaman saat user lagi tracking
- Quick-add: shortcut `T` keyboard untuk buka timer
- Drag entries di calendar untuk reschedule

### Edge Cases
- Multiple running timers per user — auto-stop yang lama saat start baru
- User tutup browser saat timer running → auto-recover saat login (running entry tetap valid)
- Entry sudah invoiced → readonly, tampil link ke invoice
- Bulk invoice 50+ entries — group line items biar PDF ga overflow

### Acceptance Criteria
- [ ] Timer start/stop dari mana saja
- [ ] Manual entry form
- [ ] Calendar view
- [ ] Filter by client/project/billable
- [ ] Bulk → invoice dengan grouping option
- [ ] Mobile-friendly timer

---

## 5B. Expense Tracking UI 🟦

**Status:** 🟦 (tabel `expenses` sudah ada) | **Priority:** ⭐ High | **Effort:** 2 hari

### Why
Catat pengeluaran per project + bisa di-billable ke klien. Mirror dari time entry tapi untuk pengeluaran.

### Database Schema
**Tabel `expenses` SUDAH ADA**. Kolom existing:
- workspace_id, client_id, category, description, amount, currency
- expense_date, receipt_path, billable, invoiced, invoice_id, notes

**Modifikasi:**
- `project_id BIGINT NULL FK→projects`
- `markup_percent DECIMAL(5,2) DEFAULT 0` — kalau billable, charge markup ke klien
- `vendor_name VARCHAR(120) NULL`

### Routes
```
GET    /expenses                       expenses.index
GET    /expenses/create                expenses.create
POST   /expenses                       expenses.store
GET    /expenses/{expense}             expenses.show
PUT    /expenses/{expense}             expenses.update
DELETE /expenses/{expense}             expenses.destroy
POST   /expenses/bulk-invoice          expenses.bulk-invoice
GET    /expense-categories             expense-categories.index
POST   /expense-categories             expense-categories.store
```

### Tabel BARU `expense_categories`
```sql
id BIGINT PK
workspace_id BIGINT FK ON DELETE CASCADE
name VARCHAR(60)
icon VARCHAR(10) NULL
color VARCHAR(7) NULL
is_default BOOLEAN DEFAULT FALSE
created_at, updated_at
UNIQUE(workspace_id, name)
```

Default categories di-seed saat workspace create: Office, Travel, Meals, Software, Marketing, Equipment, Other.

### Controller
- `App\Http\Controllers\ExpenseController`
- `App\Http\Controllers\ExpenseCategoryController`

### View
- `resources/views/expenses/index.blade.php` — list dengan thumbnail receipt
- `resources/views/expenses/create.blade.php` — drag-drop receipt upload (lihat 5.2 OCR)
- `resources/views/expenses/show.blade.php` — detail dengan receipt viewer

### Edge Cases
- Receipt 10MB max
- Foreign currency expense → store original + IDR equivalent at expense_date rate (perlu FX rates feature, simpan di workspace settings dulu)
- Expense dihapus → file receipt juga hapus

### Acceptance Criteria
- [ ] CRUD expense
- [ ] Upload + thumbnail receipt
- [ ] Filter by date, category, billable
- [ ] Bulk to invoice (mirror 5A)

---

## 5C. Project Management ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 4 hari

### Why
Group entitas (invoice, time entry, expense, quote) per project. Tanpa ini, agency yang handle multi-project per klien rusak — semua tercampur.

### Database Schema
**Tabel BARU `projects`** (lihat 01-DATABASE-RELATIONS Section 8).

**Modifikasi:**
- `invoices.project_id BIGINT NULL FK→projects`
- `time_entries.project_id`
- `expenses.project_id`
- `quotes.project_id`

### Routes
```
GET    /projects                       projects.index
GET    /projects/create                projects.create
POST   /projects                       projects.store
GET    /projects/{project}             projects.show          (overview tab)
GET    /projects/{project}/invoices    projects.invoices
GET    /projects/{project}/time        projects.time
GET    /projects/{project}/expenses    projects.expenses
GET    /projects/{project}/budget      projects.budget        (vs actual)
PUT    /projects/{project}             projects.update
PUT    /projects/{project}/status      projects.update-status
DELETE /projects/{project}             projects.destroy
```

### Controller
- `App\Http\Controllers\ProjectController`

### Service
- `App\Services\Project\ProjectStats`
  - `summary(Project): array` — total invoiced, total time, total expense, profit margin
  - `budgetUtilization(Project): array` — % used per type

### View
- `resources/views/projects/index.blade.php` — kanban or table view, filter by status & client
- `resources/views/projects/show.blade.php` — tab overview, stats card
  - Total Invoiced (sum invoices.total)
  - Total Hours (sum time_entries.hours)
  - Total Expense (sum expenses.amount)
  - Profit (Invoiced − Expense, jika billable)
- `resources/views/projects/budget.blade.php` — chart progress vs budget

### Edge Cases
- Project completed → tutup edit, tapi data history tetap accessible
- Cross-currency project (jarang) — convert via workspace base currency
- Klien hapus → project tetap, ganti client ke "Archived Client"

### Acceptance Criteria
- [ ] CRUD project
- [ ] Assign invoice/time/expense ke project
- [ ] Dashboard project: revenue, time, expense, margin
- [ ] Budget vs Actual chart
- [ ] Filter project by status/client

---

## 5D. Budget vs Actual ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 1.5 hari

### Why
Project punya budget (waktu / uang). Tanpa tracking real-time, project yg over-budget baru ketauan saat akhir. Visualisasi progress = early warning.

### Database Schema
Tidak butuh tabel baru. Pakai field di `projects.budget_amount` & `budget_hours`. Aggregate via query.

### View
Page `/projects/{project}/budget`:

```
Budget Amount: Rp 50.000.000
├─ Invoiced: Rp 32.000.000 (64%) ████████████░░░░░░░░
├─ Expense: Rp 8.500.000 (17%)   ███░░░░░░░░░░░░░░░░░
└─ Net: Rp 23.500.000 (47% margin)

Budget Hours: 200 jam
└─ Logged: 142 jam (71%)        ██████████████░░░░░░
```

### Service
- `App\Services\Project\BudgetTracker`
  - `getMetrics(Project): array`

### Acceptance Criteria
- [ ] Real-time progress bar
- [ ] Alert kalau hours/amount lewat 80%
- [ ] Trend chart (per minggu)
- [ ] Export ringkasan ke PDF
