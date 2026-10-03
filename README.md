# Global Consultancy — Education CRM + Public Website

Study-abroad website, candidate portal and CRM for counselling, admissions, visas and enrolment.

## Features

- **Public website** (`resources/views/public/`): home, about, services, study destinations (UK/USA/Canada/Australia/Europe), universities, courses, course finder, contact, appointment booking, apply-online. Blade + Bootstrap 5.3 CDN, no build step. SEO meta + OpenGraph + canonical on every page.
- **Candidate portal** (`resources/views/portal/`): journey tracker (Profile → Application → Offer → Deposit → CAS → Visa → Enrolment), profile editor, applications + status timeline, required-documents checklist + uploads, appointment requests. Mobile-first.
- **CRM**: role-based dashboards (admin/manager/staff/candidate), candidates, applications with legal status transitions (`config/consultancy.php`), documents, appointments, tasks, finance invoice PDF (`resources/views/finance/invoice-pdf.blade.php`).
- **Tests** (`tests/Feature/`): AuthTest, RbacTest, CandidateTest, ApplicationTest, DocumentTest — all independent of seeders.

## Stack

- Laravel 13 (PHP ^8.3), Blade, Bootstrap 5.3 via CDN
- MySQL (production) / SQLite (local + tests)
- Queue: database · Cache: file · Session: cookie · Mail: Hostinger SMTP

## Local setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed --force   # roles + master data + demo (local only)
php artisan test
php artisan serve
```

## URL map

- Public site: `/`, `/about`, `/services`, `/study/{UK,USA,Canada,Australia,Europe}`, `/universities`, `/courses`, `/course-finder`, `/contact`, `/book-appointment`, `/apply-online`
- CRM (staff roles): `/crm/dashboard`, `/crm/candidates`, `/crm/applications`, `/crm/documents`, `/crm/universities`, `/crm/courses`, `/crm/journey/*`, `/crm/tasks`, `/crm/appointments`, `/crm/leads`, `/crm/commissions`, `/crm/invoices`, `/crm/reports/*`, `/crm/users`, `/crm/settings`, `/crm/cms/*`
- Candidate portal: `/portal/dashboard`, `/portal/profile`, `/portal/applications`, `/portal/documents`, `/portal/appointments`

## Hostinger deploy (13 steps)

1. Create hosting plan + domain (`globalconsultancy.com`) with SSL enabled.
2. Create MySQL database + user in hPanel; note host, db name, user, password.
3. Upload project via File Manager/Git, or `git clone` over SSH, into `domains/globalconsultancy.com/laravel` (keep Laravel **outside** `public_html`).
4. Point the domain's document root to `laravel/public` (hPanel → Websites → Document root).
5. Copy `.env.example` to `.env`; set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://globalconsultancy.com`, DB_*, Hostinger SMTP, `TOKEN_HARBOR_API_KEY`.
6. Run `php artisan key:generate`.
7. `composer install --no-dev --optimize-autoloader`.
8. `php artisan migrate --force`.
9. Seed roles/permissions + create admin: `php artisan db:seed --class=RolePermissionSeeder` (then set admin password).
10. Fix permissions: `chmod -R 775 storage bootstrap/cache`; ensure `storage/app/public` symlink via `php artisan storage:link`.
11. Verify `public/.htaccess` is active (HTTPS redirect uncommented).
12. Optimize: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
13. Smoke-test: home, course finder, apply form, login, portal dashboard; check `storage/logs/laravel.log`.

## Cron (scheduler)

Add in hPanel → Cron Jobs (every minute):

```
* * * * * /usr/bin/php /home/USER/domains/globalconsultancy.com/laravel/artisan schedule:run >> /dev/null 2>&1
```

## Queue worker

Hostinger shared hosting has no supervisor — use cron to process jobs:

```
* * * * * /usr/bin/php /home/USER/domains/globalconsultancy.com/laravel/artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

## Backup

See `BACKUP_RESTORE.md`.

## Default logins (change immediately)

- Admin: `admin@globalconsultancy.com` / `password123`
- Manager: `manager@globalconsultancy.com` / `password123`
- Staff: `staff@globalconsultancy.com` / `password123`
- Candidate: `candidate@globalconsultancy.com` / `password123`

Change all passwords right after first login (and rotate `APP_KEY`-dependent tokens if the key was ever shared).
