# Collaboration & Team — Sprint 4

> Multi-user workspace, role permissions, approval flow, comment thread, activity log. Buka enterprise market.

---

## 6.1 Multi-User Workspace + Roles 🟦

**Status:** 🟦 (tabel `workspace_invites` + pivot `workspace_user.role` ada) | **Priority:** ⭐ High | **Effort:** 3 hari

### Why
Banyak workspace = punya tim (owner + accountant + sales). Tanpa multi-user, mereka share login = audit trail rusak.

### Database Schema
**Tabel `workspace_user` SUDAH ADA** dengan `role` (owner/admin/member).
**Tabel `workspace_invites` SUDAH ADA**.

**Tambahan: granular permission**
Tabel BARU `workspace_permissions`:
```sql
id BIGINT PK
workspace_id BIGINT FK ON DELETE CASCADE
user_id BIGINT FK ON DELETE CASCADE
ability VARCHAR(80)  -- 'invoice.create', 'invoice.delete', 'client.delete', 'reports.view', dll
allowed BOOLEAN DEFAULT TRUE
UNIQUE(workspace_id, user_id, ability)
INDEX(workspace_id, user_id)
```

Atau lebih simple: pakai role-based fixed set + per-user override.

**Role default abilities (seed):**
- **owner** — semua
- **admin** — semua kecuali `workspace.delete`, `billing.manage`
- **manager** — invoice.*, client.*, payment.*, reports.view, NO settings.*, NO user.*
- **member** — invoice.create, invoice.update (own), client.view, NO delete
- **accountant** — reports.view, payment.*, expense.*, NO create/update invoice
- **viewer** — only `*.view`

### Routes
```
GET    /workspace/team                  team.index
POST   /workspace/team/invite           team.invite
GET    /workspace/team/invites          team.invites.index
DELETE /workspace/team/invites/{invite} team.invites.cancel
POST   /workspace/team/invites/{token}/accept   team.invites.accept   (public: dari email)
DELETE /workspace/team/{user}           team.remove
PUT    /workspace/team/{user}/role      team.update-role
GET    /workspace/team/{user}/permissions  team.permissions.show
PUT    /workspace/team/{user}/permissions  team.permissions.update
```

### Controllers
- `App\Http\Controllers\TeamController`
- `App\Http\Controllers\TeamInviteController`
- `App\Http\Controllers\TeamPermissionController`

### Service
- `App\Services\Team\InviteService::send($workspace, $email, $role)`
  - Generate token, store invite
  - Send email "[Workspace name] invited you to join Invocely"
  - Token unique, expires 7 hari
- `App\Services\Team\PermissionResolver::userCan(User, $workspace, $ability)`
  - Cek role → ability map
  - Override individual via `workspace_permissions`

### Middleware / Authorization
- `Gate::define('invoice.delete', fn($user) => PermissionResolver::userCan($user, workspace(), 'invoice.delete'))`
- Apply di setiap controller method via `$this->authorize('invoice.delete', $invoice)`

### View
- `resources/views/team/index.blade.php` — daftar member + role + remove button
- `resources/views/team/invite-modal.blade.php` — form invite (email + role)
- `resources/views/team/invites/index.blade.php` — pending invites (resend / cancel)
- `resources/views/team/permissions.blade.php` — checkboxes ability per user

### Edge Cases
- Owner tidak bisa di-remove dari workspace
- Owner bisa di-transfer ke admin (1x sequence)
- Member yang dihapus → invoice yang dia create tetap ada (created_by SET NULL atau keep + flag "by removed user")
- Invite sudah expired → 410 Gone, suggest resend
- Email klien sama dengan email user lain → reject (1 email = 1 user)

### Acceptance Criteria
- [ ] Invite via email
- [ ] Accept invite via link → join workspace
- [ ] Switch workspace di top nav
- [ ] Role update real-time effects permission
- [ ] Granular per-user override
- [ ] Audit log: setiap invite/remove/role-change

---

## 6.2 Approval Workflow ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 2 hari

### Why
Enterprise client — invoice >X juta perlu approval manager. Tanpa workflow, control rusak.

### Database Schema
**Tabel BARU `approval_rules` + `approval_requests`** (lihat 01-DATABASE-RELATIONS Section 10).

### Routes
```
GET    /workspace/approval-rules                approval-rules.index
POST   /workspace/approval-rules                approval-rules.store
PUT    /workspace/approval-rules/{rule}         approval-rules.update
DELETE /workspace/approval-rules/{rule}         approval-rules.destroy

GET    /approvals                               approvals.index    (pending untuk current user)
POST   /approvals/{request}/approve             approvals.approve
POST   /approvals/{request}/reject              approvals.reject
```

### Controllers
- `App\Http\Controllers\ApprovalRuleController`
- `App\Http\Controllers\ApprovalController`

### Service
- `App\Services\Approval\ApprovalEngine`
  - `evaluate($subject)` — dipanggil saat user submit invoice/quote/expense/credit_note
    - Cek rules matching `applies_to` + condition
    - Kalau match → create `approval_request`, set subject status='pending_approval', notify approvers
  - `recordResponse($request, $user, $decision, $comment)`
    - Append ke `approver_responses`
    - Kalau semua approve (require_all) atau salah satu (require_all=false) → mark final
    - Final approve → resume submit flow (subject status berubah ke normal)
    - Final reject → subject status='rejected', notify creator

### Modifikasi `invoices`, `quotes`, `expenses`, `credit_notes`:
- Tambah enum 'pending_approval' ke status
- Field `approval_request_id BIGINT NULL FK`

### View
- `resources/views/approval-rules/index.blade.php` — table rules
- `resources/views/approval-rules/form.blade.php` — wizard: condition + approver picker
- `resources/views/approvals/index.blade.php` — inbox approver
- `resources/views/approvals/show.blade.php` — detail item + tombol approve/reject + komen

### Edge Cases
- Approver dihapus dari workspace → cari pengganti dari role 'admin'
- Subject diedit setelah pending_approval → reset approval (atau mark stale)
- Approver = creator → auto-skip approver itu

### Acceptance Criteria
- [ ] Bikin rule conditional
- [ ] Submit invoice >threshold → masuk pending
- [ ] Approver dapat notif + email
- [ ] Approve/Reject dengan komen
- [ ] Audit log lengkap

---

## 6.3 Comment Thread di Invoice ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 1.5 hari

### Why
Internal note antar tim ("ini invoice perlu hold sampai NPWP klien valid"), atau public reply dari klien ("kenapa item ke-3 beda dari quotation?"). Tanpa thread, komunikasi via email/WA hilang context.

### Database Schema
**Tabel BARU `invoice_comments`** (lihat 01-DATABASE-RELATIONS Section 4).

### Routes
```
GET    /invoices/{invoice}/comments              invoices.comments.index
POST   /invoices/{invoice}/comments              invoices.comments.store
DELETE /invoices/{invoice}/comments/{comment}    invoices.comments.destroy
POST   /i/{token}/comments                       public.invoice.comments.store    (public: dari klien)
```

### Controllers
- `App\Http\Controllers\InvoiceCommentController`
- `App\Http\Controllers\PublicInvoiceCommentController` (untuk public thread)

### View
- Tab "Komen" di `resources/views/invoices/show.blade.php`
- Public view: section diskusi di `public.blade.php` (toggle visible per workspace setting `enable_client_comments`)

### Edge Cases
- Spam dari public → rate limit per IP (3 komen / jam)
- Email notification ke owner saat ada komen public
- Internal comment ga boleh leak ke public view (filter `is_internal=false`)
- Reply (`parent_id`) — render threaded; max depth 2

### Acceptance Criteria
- [ ] Comment box di invoice show
- [ ] Toggle internal vs public
- [ ] Klien bisa reply via public view
- [ ] Notification ke pihak terkait
- [ ] Audit log

---

## 6.4 Assign Invoice ❌

**Status:** ❌ | **Priority:** 🌱 Low | **Effort:** 1 hari

### Why
Sales A handle klien X, sales B handle klien Y. Auto-assign + notif kalau jatuh tempo.

### Database Schema
Modifikasi `invoices`:
- `assigned_to BIGINT NULL FK→users ON DELETE SET NULL`

Modifikasi `clients`:
- `default_assignee_id BIGINT NULL FK→users` — auto-fill saat create invoice ke client ini

### Routes
```
PUT /invoices/{invoice}/assign        invoices.assign
GET /my-invoices                      invoices.mine
```

### View
- Dropdown "Assigned to" di invoice form
- Filter "Yang saya tangani" di invoice list

### Acceptance Criteria
- [ ] Assign invoice ke user
- [ ] Notification ke assignee saat invoice jatuh tempo / paid / partial
- [ ] Filter "My invoices"

---

## 6.5 Activity Feed (Audit Log Surface) 🟦

**Status:** 🟦 (tabel `audit_logs` ada, perlu UI) | **Priority:** ⭐ High | **Effort:** 1.5 hari

### Why
"Siapa edit invoice ini kemarin?" — tanpa visibility, ga bisa investigate. Audit log udah ada, tapi belum ada page untuk view.

### Database Schema
Pakai existing `audit_logs`. Pastikan **semua mutation** insert ke audit_logs via observer:

**Wajib hook** ke models:
- Invoice (create, update, delete)
- Quote (create, update, delete, accept, convert)
- Payment (create, delete)
- CreditNote (issue, apply, cancel)
- Client (create, update, delete)
- Workspace settings (update)
- Team (invite, remove, role change)

Pattern:
```php
// app/Observers/AuditableObserver.php
class AuditableObserver {
    public function created(Model $m) { $this->log('created', $m); }
    public function updated(Model $m) { $this->log('updated', $m, $m->getDirty()); }
    public function deleted(Model $m) { $this->log('deleted', $m); }

    private function log($action, $model, $changes = null) {
        AuditLog::create([
            'workspace_id' => $model->workspace_id ?? workspace()?->id,
            'user_id'      => auth()->id(),
            'action'       => "{$model->getTable()}.{$action}",
            'subject_type' => get_class($model),
            'subject_id'   => $model->id,
            'description'  => "{Action} {Model} #{$model->id}",
            'changes'      => $changes,
            'ip'           => request()->ip(),
        ]);
    }
}
```

Boot di `AppServiceProvider`:
```php
Invoice::observe(AuditableObserver::class);
Quote::observe(AuditableObserver::class);
// ...
```

### Routes
```
GET /activity                          activity.index
GET /activity/{audit}                  activity.show     (detail diff)
GET /invoices/{invoice}/activity       activity.invoice  (per-subject filter)
GET /clients/{client}/activity         activity.client
```

### Controller
- `App\Http\Controllers\ActivityController`

### View
- `resources/views/activity/index.blade.php` — feed timeline
  - Filter: user, action type, date range, subject type
  - Group by hari (relative: "Today", "Yesterday", "2 hari lalu")
- `resources/views/activity/show.blade.php` — diff view (before/after JSON)

### Acceptance Criteria
- [ ] Semua mutation di-log otomatis
- [ ] Page activity feed dengan filter
- [ ] Per-subject filter (invoice X timeline)
- [ ] Diff view untuk update events
- [ ] Export CSV untuk audit external

---

## 6.6 Notification Center 🟦

**Status:** 🟦 (tabel `app_notifications` ada) | **Priority:** ⭐ High | **Effort:** 1.5 hari

### Why
In-app notifications: "Klien X bayar invoice Y", "Approval Z menunggu", "Invoice jatuh tempo besok". Tanpa notif center, user miss event penting.

### Database Schema
Pakai existing `app_notifications`. OK as-is.

### Routes
```
GET    /notifications                  notifications.index
POST   /notifications/{n}/read         notifications.read
POST   /notifications/read-all         notifications.read-all
DELETE /notifications/{n}              notifications.destroy
GET    /api/notifications/unread       api.notifications.unread       (poll)
```

### Controller
- `App\Http\Controllers\NotificationController`

### Service
- `App\Services\Notification\NotificationDispatcher::send(User $u, $type, $title, $message, ?$url, ?$icon)`
  - Insert ke app_notifications
  - Trigger broadcast via Pusher / Laravel Echo (kalau real-time setup)
  - Optional: parallel email kalau `user.email_notifications=true`

### Events Triggering Notifications
- Invoice paid (full or partial) → workspace owner
- Invoice overdue → assigned user + owner
- Quote accepted → creator
- Approval pending → approvers
- Approval decided → creator
- Comment public → owner
- Reminder failed → owner
- Subscription expiring → owner (via system cron)

### View
- Bell icon di top navbar dengan badge count unread (poll `/api/notifications/unread` tiap 30 detik)
- Dropdown menu 5 latest, link "View all"
- `resources/views/notifications/index.blade.php` — full list

### Acceptance Criteria
- [ ] Bell badge real-time (atau dekat-dekat — 30 detik OK)
- [ ] Click notification → mark read + redirect to action_url
- [ ] Filter by type
- [ ] Mark all read button
