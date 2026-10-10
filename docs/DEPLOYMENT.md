# DEPLOYMENT

## Syarat server

- PHP ^8.3 + extensi: mbstring, pdo_mysql, sqlite3 (uji), gd, bcmath, intl, zip
- Composer 2, Node 20+, MySQL 8 / MariaDB
- Web root → `public/`, HTTPS wajib (PWA + cookie aman)

## Deploy pertama

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate
# .env: APP_ENV=production APP_DEBUG=false APP_URL=https://domain
#       DB_* , MAIL_* (smtp), SESSION_SECURE_COOKIE=true
#       LICENSE_DEV_BYPASS=false
php artisan migrate --force
php artisan storage:link
npm ci --ignore-scripts && npm run build
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Operasional

- Cron: `* * * * * php /path/artisan schedule:run >> /dev/null 2>&1`
- Queue worker (database driver): Supervisor/systemd menjalankan
  `php artisan queue:work --tries=3 --backoff=30`
- Backup: `invoices:db:backup` 03:30 → simpan off-server; uji restore berkala
- Jangan seed demo di prod (DatabaseSeeder otomatis skip; paksa via `SEED_DEMO=true` bila perlu)
- Health: `GET /up` (tanpa rahasia)
