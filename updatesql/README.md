# Invocely — SQL Dump

File: `invocely.sql` (atau `invocely.zip` versi terkompresi)
Berisi: 72 tabel (schema + data) — siap import ke server lain.

## Penting: cara import di cPanel / shared hosting

Error `#1044 - Access denied` muncul kalau user DB **tidak punya hak `CREATE DATABASE`** (umum di shared hosting). Solusinya:

### 1. Bikin database **manual** via cPanel dulu

1. Login ke cPanel.
2. Buka **MySQL Databases**.
3. **Create New Database** → contoh nama: `cpses_xxx_invocely` (cPanel biasanya prefix otomatis).
4. **Add User to Database** → kasih user dengan **All Privileges**.
5. Catat: nama DB final, username, password.

### 2. Import file `invocely.sql`

**Via phpMyAdmin:**

1. Buka phpMyAdmin → pilih database yang baru dibuat (jangan klik "Server", harus level database).
2. Tab **Import** → pilih `invocely.sql` (atau `invocely.zip` — phpMyAdmin auto-extract).
3. Klik **Go**. Tunggu sampai selesai.

**Via SSH:**

```bash
mysql -u USERNAME -p NAMA_DATABASE < invocely.sql
```

### 3. Update `.env` di server

```env
DB_HOST=localhost
DB_DATABASE=cpses_xxx_invocely
DB_USERNAME=cpses_xxx_invocely_user
DB_PASSWORD=password_yang_kamu_set
```

Lalu:

```bash
php artisan config:clear
php artisan cache:clear
```

## Login default setelah restore

| Role | Email | Password |
|---|---|---|
| Superadmin | `superadmin@invocely.test` | `superadmin123` |
| Demo Workspace Admin | `demo@invocely.test` | `password` |

> ⚠️ **Wajib ganti password** setelah deploy production. Demo data juga sebaiknya dihapus via:
> ```bash
> php artisan migrate:fresh --seed
> ```
> (untuk dev/staging only — di production cukup hapus demo workspace via /admin)

## Detail dump

- **Source:** MySQL 8.4 (Laragon) · database `invocely`
- **Charset:** `utf8mb4` / `utf8mb4_unicode_ci`
- **Mode:** table-level statements (no `CREATE DATABASE`, no `USE`) — aman untuk shared hosting
- **Includes:** schema, data, routines, triggers, events
- **Generated:** 2026-05-09
