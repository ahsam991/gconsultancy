# Deployment Checklist

Verify each item before pointing DNS / going live.

## Code & config

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://globalconsultancy.com`
- [ ] `APP_KEY` set (never commit real `.env`)
- [ ] DB_* credentials point at Hostinger MySQL and connect
- [ ] `MAIL_*` Hostinger SMTP tested (send a test enquiry)
- [ ] `QUEUE_CONNECTION=database`, `CACHE_STORE=file`, `SESSION_DRIVER=cookie`
- [ ] `TOKEN_HARBOR_API_KEY` set or intentionally left empty
- [ ] `config/consultancy.php` destinations/statuses reviewed
- [ ] `layouts/app.blade.php` verified untouched (CRM layout as shipped)

## Views & routes

- [ ] `php artisan view:clear` passes
- [ ] Public pages render: /, /about, /services, /study/uk, /universities, /courses, /course-finder, /contact, /appointment, /apply
- [ ] Enquiry/appointment/apply forms POST with @csrf + honeypot
- [ ] Error pages exist: 404, 403, 419, 429, 500, 503
- [ ] `public/sitemap.xml` + `public/robots.txt` served (Disallow /admin /portal /dashboard)
- [ ] Public layout has NO noindex; CRM pages noindex handled at layout level

## Auth & RBAC

- [ ] `php artisan test` green (at minimum `--filter=AuthTest`)
- [ ] Login redirects by role (admin/manager/staff/candidate)
- [ ] Staff gets 403 on admin pages; candidate gets 403 on staff pages
- [ ] Default passwords changed (`admin@globalconsultancy.com` etc.)

## Server

- [ ] Document root → `laravel/public`, Laravel core outside `public_html`
- [ ] `public/.htaccess` active; `.env`/storage protected; HTTPS redirect enabled
- [ ] `storage/` + `bootstrap/cache/` writable; `storage:link` created
- [ ] `config:cache && route:cache && view:cache` run
- [ ] Cron: `schedule:run` + `queue:work --stop-when-empty` every minute
- [ ] Backup tested once (see BACKUP_RESTORE.md)
