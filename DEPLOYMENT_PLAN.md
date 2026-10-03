# DEPLOYMENT PLAN - Global Consultancy Education CRM

## Hostinger Deployment Guide

### Prerequisites

1. **Create MySQL DB in hPanel**
   - Note: DB name, DB user, DB password, DB host
   - Example: `gconsultancy_crm`, `gconsultancy_crm_user`, `password123`, `localhost`

2. **Set PHP Version**
   - hPanel -> Advanced -> PHP Configuration
   - Select PHP 8.2 or 8.3
   - Ensure following settings are enabled:
     - `memory_limit` >= 256M
     - `post_max_size` >= 50M
     - `upload_max_filesize` >= 25M
     - `max_execution_time` >= 300
     - `max_input_time` >= 300
     - `extension=mysqli`
     - `extension pdo_mysql`
     - `extension dom`
     - `extension xml`
     - `extension mbstring`
     - `extension tokenizer`
     - `extension fileinfo`

3. **Upload Project**
   - Exclude `node_modules` from upload
   - `vendor` directory will be installed via Composer on server
   - Upload to `domains/gconsultancy.co.uk/public_html` or subdomain folder `crm`
   - Use FTP or File Manager in hPanel

4. **Point Domain to `public` folder**
   - In hPanel: Domains -> Manage Domain -> Document Root
   - Set Document Root to: `public_html/public`
   - Or for subdomain: Set Document Root to `public_html/crm/public`

### Environment Configuration

#### 1. Copy .env.example to .env
```bash
cp .env.example .env
```

#### 2. Configure .env Values
```
APP_NAME="Global Consultancy Education"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://crm.gconsultancy.co.uk

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gconsultancy_crm
DB_USERNAME=gconsultancy_crm_user
DB_PASSWORD=your_db_password

FILESYSTEM_DISK=local
PRIVATE_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@gconsultancy.co.uk
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@gconsultancy.co.uk
MAIL_FROM_NAME="Global Consultancy Education"

TOKEN_HARBOR_API_KEY=

CACHE_DRIVER=file
QUEUE_DRIVER=database
SESSION_DRIVER=cookie

BCRYPT_ROUNDS=12
```

#### 3. Generate Application Key
```bash
php artisan key:generate
```

#### 4. Run Migrations
```bash
php artisan migrate --force
```

#### 5. Run Seeder (Production Seeder)
```bash
php artisan db:seed --class=ProductionSeeder
```

**Note:** Create a `ProductionSeeder` that creates:
- Admin user: `admin@globalconsultancy.com`
- Default roles: admin, manager, staff, candidate
- Default permissions
- Default countries, study levels, subjects
- Default commission rules
- First admin credentials must be changed immediately

### Storage & File Handling

#### 6. Set Storage Permissions
```bash
chmod 775 storage
chmod 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 7. Private Document Storage
- **DO NOT** use `php artisan storage:link` on Hostinger shared hosting for private docs
- Private documents stored in `storage/app/private/candidates/{id}/`
- Serve private documents via `DocumentController@download` with Policy check
- Example download route:
  ```php
  Route::get('candidate/document/download/{id}', [DocumentController::class, 'download'])
      ->middleware('auth', 'permission:documents.download')
      ->name('candidate.document.download');
  ```
- The download controller checks policy and serves file with proper headers
- No predictable URLs - files cannot be accessed directly via known path

#### 8. Public Assets Symlink (Optional)
```bash
php artisan storage:link
```
- Only if using Laravel's public disk for assets
- Otherwise, commit `public/build/` from local `npm run build`

### Optimization

#### 9. Cache Production Assets
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

#### 10. npm Build (Local Only)
```bash
# Run locally before uploading
npm run build
# Commit public/ directory with build assets
```
- Do NOT run `npm install` or `npm run dev` on Hostinger
- Hostinger serves static assets from `public/build/`

### SSL Configuration

#### 11. Enable Free SSL in hPanel
- Domains -> Free SSL -> Let's Encrypt
- Wait for SSL propagation (5-15 minutes)
- Ensure `APP_URL` in .env uses `https://`

### Cron Job (Optional - for Queues)

#### 12. Set Up Cron Job
- In hPanel: Advanced -> Cron Jobs
- Add: `* * * * * /usr/bin/php /path/to/artisan schedule:run >> /dev/null 2>&1`
- Ensures scheduled tasks (emails, reports) run hourly

### Backup Strategy

#### 13. Daily Database Export
- Use hPanel phpMyAdmin
- Export DB as SQL
- Save to local/cloud storage
- Automate via cron if possible

#### 14. Weekly Files Backup
- Compress `app/`, `storage/`, `routes/`, `config/`
- Store in secure location
- Retain 4 weeks minimum

#### 15. Monthly Full System Backup
- Database + Files + `.env` (without passwords)
- Off-site backup recommended

### Security hardening

#### 16. Protect Sensitive Files
- Ensure `.env`, `storage/private`, `logs/` not accessible via web
- .htaccess protection for `/storage` directory
- `.htaccess` to block direct access to sensitive folders

#### 17. .htaccess Example (in public/.htaccess or root)
```apache
# Protect .env file
<Files .env>
    order allow,deny
    deny from all
</Files>

# Protect storage private directory
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^public/
    RewriteRule .? - [F,L]
</IfModule>

# Block access to log files
<FilesMatch "\.(log|sql|bak|backup)$">
    order allow,deny
    deny from all
</FilesMatch>
```

### Production Checklist

- [ ] MySQL DB created and configured
- [ ] PHP 8.2+ version set in hPanel
- [ ] .env configured with correct DB/MAIL settings
- [ ] APP_KEY generated
- [ ] Migrations run successfully
- [ ] Database seeded (admin user created)
- [ ] Storage permissions set (775)
- [ ] Config/routes/views cached
- [ ] SSL enabled (Free SSL in hPanel)
- [ ] Domain pointed to `public/` folder
- [ ] Admin user logged in and password changed
- [ ] Cron job set up (if using queues)
- [ ] Backup schedule established
- [ ] .env not committed to git
- [ ] No `node_modules` uploaded
- [ ] Vendor installed via `composer install --no-dev --optimize-autoloader`

### Post-Deployment Tasks

1. **Login as Admin:** `admin@globalconsultancy.com` / temporary password (change immediately)
2. **Update Admin Password:** Use forgot password or direct DB update
3. **Verify RBAC:** Test all 4 roles have correct permissions
4. **Test Document Upload:** Upload a test document as different roles
5. **Test Application Flow:** Create a full candidate journey
6. **Check Email Settings:** Send test email
7. **Verify SSL:** Ensure https:// works fully
8. **Monitor Logs:** Check `storage/logs/laravel.log` for errors