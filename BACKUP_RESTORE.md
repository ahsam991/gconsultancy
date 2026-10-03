# Backup & Restore

## What to back up

1. MySQL database (daily)
2. `storage/app/` uploads (documents, invoices)
3. `.env` (secure offline copy — never in git)
4. Full project checkout (weekly, or rely on git + `composer install`)

## Backup

```bash
# 1. Database (Hostinger values from .env)
mysqldump -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" \
  | gzip > backup-$(date +%F).sql.gz

# 2. Uploaded files
tar -czf files-$(date +%F).tar.gz -C /home/USER/domains/globalconsultancy.com/laravel storage/app public/uploads 2>/dev/null

# 3. Download both archives off-server (hPanel File Manager or scp/sftp).
```

Cron-friendly variant (runs daily, keeps 7 days):

```cron
0 2 * * * /usr/bin/mysqldump -h localhost -u DBUSER -p'DBPASS' DBNAME | gzip > /home/USER/backups/db-$(date +\%F).sql.gz && find /home/USER/backups -name 'db-*.sql.gz' -mtime +7 -delete
```

## Restore

```bash
# 1. Database
gunzip < backup-YYYY-MM-DD.sql.gz | mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE"

# 2. Files
tar -xzf files-YYYY-MM-DD.tar.gz -C /home/USER/domains/globalconsultancy.com/laravel

# 3. Rebuild caches
php artisan config:clear
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Notes

- Test a restore on a staging subdomain at least once per quarter.
- Keep at least one off-server copy (local drive or cloud storage).
- After restore, re-check file ownership/permissions (`775` on `storage`, `bootstrap/cache`).
