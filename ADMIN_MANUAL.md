# Admin Manual

**Audience:** system administrators (role `admin`). Covers user/team management, permissions, settings, templates, CMS, finance, audit, backup, queues, seeding, and passwords.
**Routes:** admin area is `/crm/*` guarded by `role:admin` (see `routes/web.php`). Public site `/`, portal `/portal/*`.
**Demo logins (rotate immediately in production):** admin@/manager@/staff@/candidate@globalconsultancy.com, password `password123`.

## Table of Contents

1. [Users and Teams](#1-users-and-teams)
2. [Roles and Permissions Matrix](#2-roles-and-permissions-matrix)
3. [Settings](#3-settings)
4. [Email Templates](#4-email-templates)
5. [CMS: Pages, Testimonials, Team, FAQs](#5-cms-pages-testimonials-team-faqs)
6. [Commission Rules, Claiming, Receiving](#6-commission-rules-claiming-receiving)
7. [Invoices and DomPDF Download](#7-invoices-and-dompdf-download)
8. [Audit Log](#8-audit-log)
9. [Backup and Restore](#9-backup-and-restore)
10. [Queue and Cron on Hostinger](#10-queue-and-cron-on-hostinger)
11. [Production Seeder](#11-production-seeder)
12. [Changing Default Passwords](#12-changing-default-passwords)
13. [Go-Live Checks](#13-go-live-checks)

## 1. Users and Teams

- **List users:** `/crm/users` (name, role, team, paginated).
- **Create user:** `/crm/users/create` → submit `POST /crm/users` with name, unique email, password (min 8, confirmed), `role_id`, optional `team_id`, phone.
- **View / edit / delete:** `/crm/users/{id}`, `/crm/users/{id}/edit` (`PUT/PATCH /crm/users/{id}`), `DELETE /crm/users/{id}`. Leaving the password blank on edit keeps the existing password.
- **Teams:** full resource under `/crm/teams` (`index/create/store/show/edit/update/destroy`, `TeamController`). Assign users via their `team_id`; managers scope to their team's candidates/applications.
- **Rule:** managers and staff cannot reach these routes (403). Every user action is audit-logged (`user.created`, `settings.updated`, etc.).

## 2. Roles and Permissions Matrix

Four roles: `admin`, `manager`, `staff`, `candidate`. Enforcement is server-side: `auth` + `role:<role>` middleware (`RoleMiddleware`, admin bypasses role checks) plus per-action policies/gates in controllers. Hiding menu items is cosmetic only.

| Module | Admin | Manager | Staff | Candidate |
|---|---|---|---|---|
| User/team management | CRUD (`/crm/users`, `/crm/teams`) | View own team only | No | No |
| Candidates | Full | Team scope | Assigned only | Own only |
| Applications | Full + delete | Team (no delete) | Assigned only | Own, view only |
| Universities/courses | CRUD | View (+ limited edits) | View | View |
| Documents upload | All | Team | Assigned | Own |
| Documents verify/reject | Yes | Yes | No | No |
| Offers/CAS/visa/enrolment | CRUD | Team | Assigned, edit | View only |
| Commissions/invoices/payments | CRUD | View team | No | No |
| CMS (`/crm/cms/*`) | CRUD | No | No | No |
| Settings/audit (`/crm/settings`, `/crm/audit-logs`) | Full | No | No | No |

Key behaviors: staff document/commission/application queries are auto-scoped (`assigned_staff_id`); candidates are fenced to `user_id` and `role:candidate` portal routes. `DELETE /crm/applications/{id}` and verify actions (`POST /crm/documents/{id}/verify|reject`) must stay restricted to the matrix above.

## 3. Settings

1. Open `/crm/settings` — key/value list (`Setting` model, ordered by key).
2. Submit `PUT/PATCH /crm/settings` with a `settings[key] = value` map; keys are created or updated in one save.
3. Production values to confirm (see `.env.example` / deployment docs): `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`, DB credentials, `MAIL_*` (Hostinger SMTP `smtp.hostinger.com:465/ssl`), `QUEUE_CONNECTION=database`, `CACHE_STORE=file`, `SESSION_DRIVER=cookie`, `BCRYPT_ROUNDS=12`, `TOKEN_HARBOR_API_KEY`.
4. After changing cached config on Hostinger: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.

## 4. Email Templates

- **Manage:** `/crm/email-templates` (resource: index/create/store/show/edit/update/destroy).
- **Seeded defaults (`MasterDataSeeder`):**

| Slug | Subject | Variables |
|---|---|---|
| `welcome` | `Welcome to Global Consultancy, {{name}}` | `name, uid` |
| `offer-received` | `Your offer from {{university}}` | `name, university, course` |
| `cas-issued` | `Your CAS has been issued` | `name, cas_number` |
| `visa-approved` | `Visa approved` | `name` |

- **Variables format:** comma-separated names in the `variables` column; placeholders in subject/body use `{{variable}}` (e.g. `{{name}}`, `{{uid}}`, `{{university}}`, `{{course}}`, `{{cas_number}}`). Keep both in sync when editing.
- **Practice:** keep `active` on for in-use templates; test-render with a real candidate before mass use; never store secrets in template bodies.

## 5. CMS: Pages, Testimonials, Team, FAQs

All under `/crm/cms/*`, admin-only, full CRUD each:

| Content | List | Create (POST) | View / Edit / Delete |
|---|---|---|---|
| Pages | `/crm/cms/pages` | `/crm/cms/pages/create` | `/crm/cms/pages/{id}`, `…/edit`, `DELETE` |
| Testimonials | `/crm/cms/testimonials` | `/crm/cms/testimonials/create` | `/crm/cms/testimonials/{id}`, `…/edit`, `DELETE` |
| Team members | `/crm/cms/team` | `/crm/cms/team/create` | `/crm/cms/team/{id}`, `…/edit`, `DELETE` |
| FAQs | `/crm/cms/faqs` | `/crm/cms/faqs/create` | `/crm/cms/faqs/{id}`, `…/edit`, `DELETE` |

Seeded pages: `home`, `about`, `services`, `contact`, `privacy` (published). Publish flags (`is_published`/`active`), sort orders, and featured ratings map directly to the public site (`/`, `/about`, `/services`, `/contact`, `/universities`, `/courses`).

## 6. Commission Rules, Claiming, Receiving

- **How amounts are computed (`CommissionService`):** latest active `CommissionRule` for the application's university wins (`rate`); else the university's `commission_rate`; else 10%. Amount = tuition × rate/100, stored per application (`updateOrCreate` by `application_id`), auto-recalculated on application create and on `ENROLLED`/`DEPOSIT_PAID` transitions.
- **List/filter:** `/crm/commissions` (`?status=`). Claimable = `PENDING`/`READY_TO_CLAIM`/`DUE` on `ENROLLED` applications.
- **Claim:** `POST /crm/commissions/{id}/claim` → status `CLAIMED` + `claimed_date`.
- **Receive:** `POST /crm/commissions/{id}/receive` → status `FULLY_RECEIVED` + `received_date`.
- Both actions are audit-logged against the application. Managers see team scope; staff have no access.

## 7. Invoices and DomPDF Download

1. **List:** `/crm/invoices`.
2. **Create:** `/crm/invoices/create` → `POST /crm/invoices` with `university_id`, `candidate_id`, `application_id`, optional `commission_id`, `invoice_date`, `due_date`, `subtotal`, `tax_amount`, line `items[]` (description + amount). Number is auto-generated (`UIDService::invoiceNumber`, e.g. `INV-000001`); total = subtotal + tax; status starts `PENDING`.
3. **View:** `/crm/invoices/{id}` shows items, payments, linked university/candidate/application.
4. **PDF (DomPDF, A4 portrait):** `GET /crm/invoices/{id}/pdf` downloads `{invoice_number}.pdf`; add `?preview=1` to render the HTML view in-browser first. PDF generation is audit-logged.
5. **Payments:** `POST /crm/payments` with `invoice_id`, `amount`, `method`, `transaction_ref`, `payment_date`. Invoice flips to `PARTIAL` on first completed payment and `PAID` (+ `paid_at`) when the sum covers the total.

## 8. Audit Log

- **Read:** `/crm/audit-logs` — newest first, 15 per page, with acting user. Covers user/settings, candidate wizard/profile, application create/update/status, documents upload/verify/reject/delete, offers/deposits/CAS, commissions, invoices/PDF/payments, tasks.
- **Notifications:** `/crm/notifications`, mark read via `POST /crm/notifications/{id}/read` or all via `POST /crm/notifications-read-all`. Portal equivalents exist under `/portal/notifications`.
- **Practice:** investigate 403 spikes (possible probing), verify every production status change has a reason, and never delete audit rows manually — retention is a code/policy decision, not a cleanup task.

## 9. Backup and Restore Routine

Back up three things: MySQL database (daily), `storage/app/` uploads + `public/uploads` (weekly or with DB), and a secure offline copy of `.env` (never in git).

```bash
# Database
mysqldump -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" \
  | gzip > backup-$(date +%F).sql.gz

# Uploaded files (adjust base path to the Hostinger layout)
tar -czf files-$(date +%F).tar.gz -C /home/USER/domains/globalconsultancy.com/laravel storage/app public/uploads 2>/dev/null
```

- **Cron-friendly daily DB backup (keep 7 days):**

```cron
0 2 * * * /usr/bin/mysqldump -h localhost -u DBUSER -p'DBPASS' DBNAME | gzip > /home/USER/backups/db-$(date +\%F).sql.gz && find /home/USER/backups -name 'db-*.sql.gz' -mtime +7 -delete
```

- **Restore:**

```bash
gunzip < backup-YYYY-MM-DD.sql.gz | mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE"
tar -xzf files-YYYY-MM-DD.tar.gz -C /home/USER/domains/globalconsultancy.com/laravel
php artisan config:clear
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

- Keep one off-server copy; re-check `775` on `storage` and `bootstrap/cache` after restore; test a restore on a staging subdomain quarterly. Hostinger alternative: daily export via hPanel phpMyAdmin.

## 10. Queue and Cron on Hostinger

- **Config:** database queue driver (`QUEUE_CONNECTION=database`), file cache, cookie sessions — no Redis/Supervisor on shared hosting.
- **Cron (hPanel → Advanced → Cron Jobs), every minute:**

```cron
* * * * * /usr/bin/php /home/USER/domains/globalconsultancy.com/laravel/artisan schedule:run >> /dev/null 2>&1
```

- Add a second per-minute entry running `queue:work --stop-when-empty` if heavy mail/report jobs need draining (see `DEPLOYMENT_CHECKLIST.md`).
- **Deploy sequence:** `composer install --no-dev --optimize-autoloader` → `php artisan migrate --force` → `php artisan config:cache && php artisan route:cache && php artisan view:cache` → verify `storage/` + `bootstrap/cache/` writable. Build assets locally (`npm run build`) and upload `public/build/`; never run npm on the server.

## 11. Production Seeder

```bash
php artisan db:seed --class=ProductionSeeder
```

This runs `RolePermissionSeeder` (roles admin/manager/staff/candidate + permissions) and `MasterDataSeeder` (countries, levels, subjects, intakes, document types, commission rules, email templates, CMS pages), then creates `admin@globalconsultancy.com` / `password123` if missing. Use `DemoSeeder` only for non-production demo data (demo users, universities, courses, candidates, applications, finance samples). Re-running is safe (`firstOrCreate`).

## 12. Changing Default Passwords

1. Log in as the demo account, then reset via `/crm/users/{id}/edit` (admin edits anyone; users should set their own via `/auth/forgot-password` → `/auth/reset-password/{token}`).
2. Password rule: min 8 characters, confirmed. On edit forms a blank password preserves the current one.
3. Do this first after seeding: `admin@globalconsultancy.com`, plus any `manager@`/`staff@`/`candidate@` demo accounts. Confirm the old `password123` no longer works and `php artisan test` (at least `--filter=AuthTest`) is green.

## 13. Go-Live Checks

`APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL` (https), real DB + SMTP credentials tested, caches built, document root on `public/`, HTTPS redirect + SSL active, `.env`/storage protected, cron running, one backup + restore tested, error pages (404/403/419/429/500/503) and `sitemap.xml`/`robots.txt` verified.
