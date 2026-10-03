# ARCHITECTURE - Global Consultancy Education CRM

## System Overview

**Project Name:** Global Consultancy Education  
**Business:** International Education Consultancy (UK, USA, Canada, Australia, Europe)  
**Architecture Model:** Laravel 11 MVC (Model-View-Controller)  
**Deployment Target:** Hostinger Web Hosting (Shared hosting)  
**Stack:** PHP 8.2+ / Laravel 11 / MySQL 8 / Blade / Bootstrap 5.3 / Alpine.js

## Core Principles

1. **Hostinger Compatibility** - No Docker, Kubernetes, Redis (optional), Node.js server, Supervisor, VPS root, Python required in production
2. **Production-Ready** - No TODO, Coming Soon, Lorem Ipsum, or hard-coded stats
3. **Security-First** - OWASP compliant, IDOR protection, policy-driven access
4. **Scalable** - Built to handle 5000+ candidates, 10000+ applications
5. **Maintainable** - Clean code, proper separation of concerns, configurable business rules

## Directory Structure (Laravel 11 Standard)

```
global-consultancy/
├── app/
│   ├── Console/
│   ├── Events/Listeners/
│   ├── Http/
│   │   ├── Controllers/          ← All controllers (Admin, Manager, Staff, Candidate, Public)
│   │   ├── Middleware/
│   │   ├── Requests/             ← Form Requests validation
│   │   └── Resources/            ← Blade components, API Resources
│   ├── Models/                   ← Eloquent Models
│   ├── Notifications/
│   ├── Policies/                 ← Authorization Policies
│   └── Services/                 ← Business logic services
├── database/
│   ├── migrations/               ← All table migrations
│   ├── seeders/                  ← Database seeders
│   └── factories/                ← Model factories
├── resources/
│   ├── views/
│   │   ├── layouts/              ← AppLayout, auth, admin, manager, staff, candidate layouts
│   │   ├── components/           ← StatCard, DataTable, FilterPanel, Modal, StatusBadge, etc.
│   │   ├── public/               ← Public website pages
│   │   ├── admin/                ← Admin dashboard
│   │   ├── manager/              ← Manager dashboard
│   │   ├── staff/                ← Staff dashboard
│   │   └── candidate/            ← Candidate portal
│   ├── css/                      ← Custom CSS (compiled from Bootstrap)
│   └── js/                       ← Alpine.js init, custom JS
├── routes/
│   ├── web.php                   ← Web routes (auth, CRM, public)
│   ├── auth.php                  ← Auth routes (Breeze)
│   └── api.php                   ← Future API routes (auth:sanctum)
├── storage/app/private/candidates/ ← Private document storage (NOT public)
├── tests/                        ← Unit & Feature tests
├── .env.example                  ← Environment variables template
└── README.md
```

## MVC Implementation

### Model (Eloquent)
- All database tables have corresponding Eloquent models
- Uses `$fillable` for mass assignment protection
- Implements `SoftDeletes` where applicable
- Has ` casts` for date/morph attributes
- Relationships defined (hasMany, belongsTo, morphMany, etc.)

### View (Blade Templates)
- Server-side rendered (no Node.js runtime needed in production)
- Blade components for reusable UI parts
- Extends `app.layout` for consistent structure
- Alpine.js for lightweight interactivity
- Bootstrap 5.3 for styling
- Server-side dataTables for lists
- Chart.js for dashboard charts

### Controller (Laravel Controllers)
- Resource controllers for CRUD operations
- Uses `auth` + `role/permission` middleware
- Leverages `Policies` & `Gates` for authorization
- Form Requests for validation
- Services for business logic
- Returns JSON for API, Blade views for web

## Hostinger-Specific Considerations

### PHP Configuration
- PHP 8.2+ required (set in hPanel -> Advanced -> PHP Configuration)
- `memory_limit` >= 256M
- `post_max_size` >= 50M
- `upload_max_filesize` >= 25M
- `max_execution_time` >= 300
- `max_input_time` >= 300

### Asset Handling
- `npm run build` locally, commit `public/build/`
- Hostinger serves static assets from `public/` folder
- No `npm install` or `npm run dev` on server

### Storage
- Private documents stored in `storage/app/private/candidates/{id}/`
- Served via `DocumentController@download` with Policy check
- Symlink `storage/public` -> `storage/app/public` for public assets
- **DO NOT** use `php artisan storage:link` for private docs on Hostinger shared hosting
- Instead, serve private files through a controller endpoint with auth check

### Database
- MySQL 8 / MariaDB
- All migrations include UTC timestamps, `created_by`, `updated_by`
- Indexes on: `candidates.email`, `candidates.phone`, `candidates.uid`, `applications.uid`, `applications.status`, `candidate_documents.candidate_id`, `universities.country_id`, `courses.university_id`, `invoices.invoice_number` (unique)

### Environment (.env)
```
APP_NAME="Global Consultancy Education"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://crm.gconsultancy.co.uk

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gconsultancy_crm
DB_USERNAME=
DB_PASSWORD=

FILESYSTEM_DISK=local
PRIVATE_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@gconsultancy.co.uk
MAIL_PASSWORD=
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@gconsultancy.co.uk
MAIL_FROM_NAME="Global Consultancy Education"

TOKEN_HARBOR_API_KEY=

CACHE_DRIVER=file
QUEUE_DRIVER=database
SESSION_DRIVER=cookie

BCRYPT_ROUNDS=12
```

### Routing Strategy
- Public website routes: `/`, `/about`, `/services`, `/study-uk`, etc.
- Auth routes: `/auth/login`, `/auth/register`, `/auth/forgot-password`
- CRM routes: `/admin/*`, `/manager/*`, `/staff/*`, `/candidate/*`
- All CRM routes grouped with middleware: `auth`, `role`, `permission`
- API routes: `/api/v1/*` (future, auth:sanctum)

### Caching (Production)
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`
- `composer dump-autoload --optimize`

### Queue System
- Database driver for queues (no Redis needed on Hostinger)
- Heavy tasks (email sending, report generation) run asynchronously
- Cron: `* * * * * /usr/bin/php /path/to/artisan schedule:run >> /dev/null 2>&1`

### Security Headers
- Implement via middleware or .htaccess
- X-Content-Type-Options: nosniff
- X-Frame-Options: DENY
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin

## RBAC Architecture

### Role-Based Access Control
- **4 roles**: Admin, Manager, Staff, Candidate/Student
- Middleware: `auth`, `role:<role>`, `permission:<permission>`
- Every route has middleware chain: `auth` -> `role` -> `permission`
- Every controller action uses `CandidatePolicy`, `ApplicationPolicy`, etc.

### Permission System
- Table structure: `permissions` <-> `role_permissions` <-> `users`
- Permissions are granular: `candidates.view`, `candidates.edit`, `candidates.delete`, etc.
- Policies check: `auth->user->can('view', $candidate)` or `$candidate->policy->view($user)`

## State Machine & Workflows

### Candidate Lifecycle
```
Lead -> Candidate -> Counselling -> Profile Collection -> Academic Assessment -> 
English Assessment -> Course Shortlist -> Application -> Document Collection -> 
University Submission -> Offer (Conditional/Unconditional) -> Deposit -> CAS -> 
Visa -> Enrolment -> Pre-Departure -> Arrival -> Post-Arrival -> Completion -> 
Alumni -> Commission -> Invoice -> Payment
```

### Application Status Pipeline
```
DRAFT -> PROFILE_CHECK -> DOCUMENT_PENDING -> READY_TO_APPLY -> SUBMITTED -> 
ACKNOWLEDGED -> UNDER_REVIEW -> INTERVIEW_REQUIRED -> 
CONDITIONAL_OFFER -> UNCONDITIONAL_OFFER -> DEPOSIT_REQUIRED -> 
DEPOSIT_PAID -> CAS_REQUESTED -> CAS_ISSUED -> VISA_PREPARATION -> 
VISA_APPLIED -> VISA_APPROVED -> VISA_REFUSED -> ENROLLED -> 
WITHDRAWN -> REJECTED -> CLOSED
```

### Status History
- Every status change logs: previous_status, new_status, changed_by, timestamp, reason, note
- Never change status without creating history entry

## Testing Strategy

### Unit Tests
- Services (Commission calc, Status transitions)
- Model relationships, accessors, mutators

### Feature Tests
- Auth, RBAC (Staff A cannot access Candidate B outside scope)
- Candidate CRUD, Search/Filter/Import/Export
- Document upload & auth
- Application creation & status change
- Offer, CAS, Visa, Enrolment
- Commission, Invoice, Payment
- Tasks, Notifications, Audit logs

### Security Tests
- IDOR tests
- Broken Access Control
- File upload abuse

## Performance Optimization

- Eager loading with `with()`
- Pagination (never load 1000s rows)
- Database indexes on foreign keys and frequently searched columns
- Cache: config, routes, views in production
- Queue heavy operations (emails, reports)
- Horizontal consideration: database connection pooling not needed on Hostinger

## SEO (Public Website)

- Semantic HTML5 structure
- Title & Meta Description per page
- OpenGraph tags for social sharing
- Canonical URLs
- XML Sitemap (generated via package or custom)
- robots.txt
- Clean URLs (Laravel default)
- Optimized images (WebP format)
- Fast loading (Bootstrap CDN, minimal custom CSS)

## Backup & Recovery

- Daily DB export via hPanel phpMyAdmin
- Weekly files backup (compressed)
- `.env` never committed to git
- `storage/` and `database/` directories backed up
- `.htaccess` protects sensitive directories