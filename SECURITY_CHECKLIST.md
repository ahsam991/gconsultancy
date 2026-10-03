# Security Checklist (OWASP-Mapped)

**Audience:** developers, admins, and reviewers hardening this Laravel 13 CRM + portal.
**Scope:** auth, RBAC/IDOR, file uploads, CSRF/XSS/SQLi, headers, audit logging, secrets, production hardening.

## Table of Contents

1. [Authentication and Sessions](#1-authentication-and-sessions)
2. [Authorization: RBAC and IDOR](#2-authorization-rbac-and-idor)
3. [File Uploads and Downloads](#3-file-uploads-and-downloads)
4. [CSRF, XSS, SQL Injection Defaults](#4-csrf-xss-sql-injection-defaults)
5. [Secure Headers Middleware](#5-secure-headers-middleware)
6. [Audit Logging Coverage](#6-audit-logging-coverage)
7. [Secrets Handling](#7-secrets-handling)
8. [Production Hardening](#8-production-hardening)

## 1. Authentication and Sessions

- [ ] Passwords hashed with bcrypt (`bcrypt()` / `Hash::make`, `BCRYPT_ROUNDS=12` in production).
  Verify: `grep -rn "Hash::make\|bcrypt(" app database/seeders | head -20`
- [ ] Login throttled at 10/min (`POST /auth/login` has `throttle:10,1`; register `6,1`; public forms `20,1`).
  Verify: `grep -n "throttle" routes/web.php`
- [ ] Session regenerated on login and registration (`$request->session()->regenerate()` in `AuthController@authenticate` and `@store`).
  Verify: `grep -n "regenerate" app/Http/Controllers/Auth/AuthController.php`
- [ ] Role-aware post-login redirect (staff/manager/admin → `route('dashboard')` at `/crm/dashboard`; candidate → `route('portal.dashboard')`).
  Verify: manual login per role + `grep -n "intended" app/Http/Controllers/Auth/AuthController.php`
- [ ] Logout requires `POST /auth/logout` behind `auth` (no state-changing GET).
  Verify: `grep -n "logout" routes/web.php`
- [ ] Password reset uses token flow (`/auth/forgot-password`, `/auth/reset-password/{token}`), min-length 8 + confirmation, email must exist.
  Verify: `grep -n "reset\|forgot\|Password::" app/Http/Controllers/Auth/AuthController.php`
- [ ] Demo/seed passwords (`password123`) changed for all four demo accounts before go-live.
  Verify: log in fails with old password; `php artisan test --filter=AuthTest`

## 2. Authorization: RBAC and IDOR

- [ ] Every CRM route requires `auth`; admin routes add `role:admin`; portal routes require `role:candidate`.
  Verify: `grep -n "middleware" routes/web.php`
- [ ] Every controller action enforces a policy/gate (`$this->authorize(...)`, `Gate::authorize(...)`), never menu-hiding alone (policies: Candidate, Application, Document; `TaskController` hard-403s candidates).
  Verify: `grep -rn "authorize\|Gate::" app/Http/Controllers | head -40`
- [ ] Staff A cannot read/update candidate B outside assignment (candidate `assigned_staff_id` scoping in `Candidate/Document/Application` queries).
  Verify test: as staff A, `GET /crm/candidates/{B-id}`, `/crm/applications/{B-app-id}`, `/crm/documents/{B-doc-id}/download` → expect 403; run `php artisan test` (RBAC/IDOR feature tests).
- [ ] Candidate cannot reach CRM: any `/crm/*` as candidate → 403; portal fenced to own `user_id` records.
  Verify test: as candidate, `GET /crm/dashboard`, `/crm/candidates`, `/crm/applications` → 403; `GET /portal/applications/{other-id}` → 403/404.
- [ ] Manager fenced to team scope; staff cannot verify documents (`verify`/`reject` authorize `verify` — manager/admin only); only permitted roles delete applications/users.
  Verify: `grep -n "verify\|delete" app/Http/Controllers/DocumentController.php app/Http/Controllers/ApplicationController.php`
- [ ] Mass assignment locked down (`$fillable` on models; user create/update validate `role_id`/`team_id` with `exists` rules).
  Verify: `grep -rn "fillable" app/Models/User.php app/Models/Candidate.php app/Models/Application.php`

## 3. File Uploads and Downloads

- [ ] Upload validated by MIME + size: `file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240` (`StoreDocumentRequest`) plus extension + byte-size re-check in `DocumentService::store` (10 MB cap).
  Verify: `cat app/Http/Requests/StoreDocumentRequest.php; grep -n "allowed\|maxKb" app/Services/DocumentService.php`
- [ ] Stored outside the public tree (`storage/app/private/candidates/{id}/` via `local` disk, randomized `time_`-prefixed filename), never under `public/`.
  Verify: `grep -n "storeAs\|private\|candidates/" app/Services/DocumentService.php; ls storage/app/private 2>/dev/null || echo "check Hostinger path"`
- [ ] Downloads go through `DocumentController@download` → `DocumentService::download` with policy check — no predictable/direct URLs, no `storage:link` for private docs on Hostinger.
  Verify: `grep -n "download" routes/web.php app/Http/Controllers/DocumentController.php`
- [ ] Re-uploads versioned (`version` increment + `DocumentVersion` row); verify/reject stamps verifier + timestamp; rejection requires `rejection_reason`.
  Verify: `grep -n "version\|verified_by\|rejection_reason" app/Http/Controllers/DocumentController.php app/Services/DocumentService.php`
- [ ] Abuse tests pass: `.php`/`.exe`/oversized files rejected; tampered `candidate_id`/`application_id` blocked by `exists` + `authorize('view', $candidate)`; cross-user download returns 403.
  Verify: `php artisan test --filter=DocumentTest` (or manual curl as staff B vs candidate A file)

## 4. CSRF, XSS, SQL Injection Defaults

- [ ] CSRF: all POST/PUT/PATCH/DELETE forms include `@csrf`; public POST routes (`/contact`, `/book-appointment`, `/apply-online`, `/enquiries`, `/course-apply`) carry tokens + throttle + honeypot.
  Verify: `grep -rn "@csrf" resources/views | wc -l; grep -n "csrf\|honeypot" routes/web.php resources/views -r | head`
- [ ] XSS: Blade `{{ }}` escaping by default; no `{!! !!}` on user content; `X-XSS-Protection: 1; mode=block` header set.
  Verify: `grep -rn "{!!" resources/views | head -20` (must be empty or intentionally safe)
- [ ] SQLi: Eloquent/query-builder bindings only — no raw string-concatenated SQL on user input (`like` search uses bindings).
  Verify: `grep -rn "DB::raw\|selectRaw\|whereRaw\|orderByRaw" app | head -20` (review each hit)
- [ ] Validation on every write path (Form Requests + `$request->validate`: wizard steps, status change, offers/deposits/CAS/visa/enrolment, invoices, payments, tasks).
  Verify: `ls app/Http/Requests; grep -rn "validate" app/Http/Controllers | wc -l`

## 5. Secure Headers Middleware

`app/Http/Middleware/SecureHeaders.php` is globally appended in `bootstrap/app.php` (`$middleware->append(...)`) and strips `X-Powered-By`. Confirm each header live:

- [ ] `X-Content-Type-Options: nosniff`
- [ ] `X-Frame-Options: SAMEORIGIN` (note: `ARCHITECTURE.md` says `DENY` — deployed code sends `SAMEORIGIN`; align or document the exception)
- [ ] `Referrer-Policy: strict-origin-when-cross-origin`
- [ ] `Permissions-Policy: camera=(), microphone=(), geolocation=()`
- [ ] `X-XSS-Protection: 1; mode=block`

Verify:

```bash
grep -n "SecureHeaders\|RoleMiddleware" bootstrap/app.php app/Http/Middleware/*.php
curl -sI https://YOUR-DOMAIN/crm/dashboard | grep -i "x-content-type-options\|x-frame-options\|referrer-policy\|permissions-policy\|x-xss-protection"
```

## 6. Audit Logging Coverage

- [ ] Status changes always write history (`ApplicationStatusService::transition` → `ApplicationStatusHistory` with previous/new/changed_by/reason/note); illegal jumps throw `InvalidArgumentException`.
  Verify: `grep -rn "StatusHistory\|transition" app/Services/ApplicationStatusService.php | head; ` open `/crm/applications/{id}/timeline`
- [ ] `AuditService::log` covers: users, settings, candidate wizard/profile, applications, documents (upload/update/verify/reject/delete), offers, deposits, commissions (calculated/claimed/received), invoices (created/PDF), payments, tasks.
  Verify: `grep -rn "AuditService::log" app/Http/Controllers app/Services | wc -l`
- [ ] Admins read logs at `/crm/audit-logs` (paginated, with user); never delete rows manually.
  Verify: open `/crm/audit-logs` and confirm recent actions appear with actor + timestamp

## 7. Secrets Handling

- [ ] `.env` never committed (gitignored); `.env.example` holds placeholders only.
  Verify: `git check-ignore .env && echo IGNORED; git log --all --full-history -- .env | head -5` (must be empty); `grep -n "PASSWORD\|SECRET\|KEY=" .env.example`
- [ ] `APP_KEY` generated server-side (`php artisan key:generate`); DB/mail credentials from hPanel, not chat/email.
  Verify: `grep -n "APP_KEY\|DB_PASSWORD\|MAIL_PASSWORD" .env` (values present, file mode `600`)
- [ ] `TOKEN_HARBOR_API_KEY` set or deliberately empty; never hardcoded in code/views/logs.
  Verify: `grep -rn "TOKEN_HARBOR" .env.example config app routes | head; grep -rni "sk-\|api[_-]\?key.*=.*['\"][A-Za-z0-9]\{16,\}" app config routes resources/views 2>/dev/null | head`
- [ ] Mail uses Hostinger SMTP over SSL (`smtp.hostinger.com:465`, `MAIL_ENCRYPTION=ssl`); test mail sent before go-live.
- [ ] Backup copies of `.env` kept offline/encrypted, excluded from file archives uploaded anywhere.

## 8. Production Hardening

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…` (no stack traces to users; custom 404/403/419/429/500/503 pages exist).
  Verify: `grep -n "APP_ENV\|APP_DEBUG\|APP_URL" .env; ls resources/views/errors`
- [ ] Caches built after each deploy: `config:cache`, `route:cache`, `view:cache`, `composer dump-autoload --optimize`.
  Verify: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- [ ] HTTPS enforced (hPanel SSL + redirect; document root → `public/`, Laravel core outside `public_html`); `.env`/`storage`/logs unreachable via web (`.htaccess` denies `.env`, `*.log|sql|bak|backup`).
  Verify: `curl -sI https://YOUR-DOMAIN/.env | head -3` (must not return 200); `curl -sI http://YOUR-DOMAIN/ | grep -i location`
- [ ] Permissions `775` on `storage` and `bootstrap/cache` (writable, not `777`); correct ownership.
  Verify: `ls -ld storage bootstrap/cache; chmod 775 storage bootstrap/cache`
- [ ] Queue/cron running (`schedule:run` + `queue:work --stop-when-empty` every minute via hPanel cron); DB queue driver, no dev-only services on server.
  Verify: `crontab -l | grep artisan; grep -n "QUEUE\|CACHE\|SESSION" .env`
- [ ] Backups proven: daily DB dump + weekly files, off-server copy, quarterly staging restore (phpMyAdmin export counts as fallback).
  Verify: `ls -lh /home/USER/backups/ | tail -5` (fresh `db-*.sql.gz` present); confirm last restore date: __________
- [ ] Post-deploy smoke: `php artisan test` green, role redirects correct, 403 matrix spot-checked, document upload/verify cycle works, one invoice PDF downloads, logs clean (`storage/logs/laravel.log`).
