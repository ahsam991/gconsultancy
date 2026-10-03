# Risks & Issues - Global Consultancy Education CRM

## Critical Risks

### 1. Hostinger Shared Hosting Limitations
- **Risk:** No root access, limited PHP extensions, no Redis, no Docker
- **Impact:** Queue system limited to database driver, no cache Redis, limited memory
- **Mitigation:** Use database-driven queue, file caching, configure PHP extensions in hPanel, keep memory_limit at 256M minimum

### 2. Document Storage Security
- **Risk:** Private documents stored in predictable paths
- **Impact:** IDOR, unauthorized file access
- **Mitigation:** Serve all private documents via authorized controller endpoint only, never expose `storage/app/private/` paths, use policy checks on every download

### 3. RBAC Misconfiguration
- **Risk:** Permissions not enforced server-side, relying on menu hiding
- **Impact:** Users accessing routes outside their scope
- **Mitigation:** Every route has `auth` + `role` + `permission` middleware, every controller uses Policies/Gates, thorough testing of IDOR scenarios

### 4. Data Integrity - Duplicate Candidates
- **Risk:** Same candidate created multiple times
- **Impact:** Duplicate applications, commission errors, confused workflow
- **Mitigation:** Duplicate detection on create (email/phone/passport check), Import CSV with validation, Unique indexes on email and phone

### 5. Status Pipeline Errors
- **Risk:** Status changed without history entry
- **Impact:** Audit trail broken, compliance issues
- **Mitigation:** Status change always creates history entry via service method, never directly in controller, database trigger or model observer

### 6. File Upload Abuse
- **Risk:** Malicious file uploads, size limits exceeded
- **Impact:** Server compromise, storage full, performance issues
- **Mitigation:** MIME + Extension + Size validation via Form Requests, store in private directory, serve via controlled endpoint, scan files with ClamAV if possible

### 7. Commission Calculation Errors
- **Risk:** Incorrect commission calculations, missed payments
- **Impact:** Financial loss, staff disputes, compliance issues
- **Mitigation:** Commission calculation in Services (not controllers), configurable rules, audit every commission change, double-check before paying out

### 8. Invoice Number Collision
- **Risk:** Duplicate invoice numbers
- **Impact:** Accounting errors, payment tracking failures
- **Mitigation:** Auto-generate unique invoice numbers with prefix + sequence, database unique index, validation before generation

### 9. SEO/Performance on Public Website
- **Risk:** Slow loading, missing meta tags, poor SEO
- **Impact:** Reduced visibility, poor user experience
- **Mitigation:** Semantic HTML5, Title/Meta per page, OpenGraph, Canonical URLs, XML Sitemap, Optimized Images, Bootstrap CDN, Minimal custom CSS

### 10. Multi-Tenancy Issues
- **Risk:** Manager/staff accessing candidates from other teams/divisions
- **Impact:** Data leakage, privacy violations
- **Mitigation:** Strict team-based filtering in all queries, `assigned_staff_id`/`assigned_manager_id` checks, never rely on URL parameters alone

## Issues to Address

### 1. Laravel 11 Compatibility
- Issue: Some packages may not be fully compatible with Laravel 11
- Resolution: Check package compatibility, update to Laravel 11 versions, test thoroughly

### 2. Queue Driver on Hostinger
- Issue: Redis not available on Hostinger shared hosting
- Resolution: Use database driver for queues, ensure `database` table created and migrations run

### 3. Storage Link on Shared Hosting
- Issue: `php artisan storage:link` may not work properly on shared Hostinger
- Resolution: Serve private docs via controller, use separate folder structure, avoid symlinks

### 4. Email Deliverability
- Issue: SMTP settings on Hostinger, spam filters
- Resolution: Use Hostinger SMTP (`smtp.hostinger.com`), configure SPF/DKIM, consider SendGrid or Mailgun for transactional

### 5. PHP Version Compatibility
- Issue: Some code may not work with PHP 8.2/8.3
- Resolution: Test on PHP 8.2+, fix deprecated features, use type declarations carefully

### 6. Database Migration Order
- Issue: Complex relationships may cause migration failures
- Resolution: Run migrations in correct order, use `--force` flag, ensure foreign keys are indexed

### 7. Form Validation Coverage
- Issue: All forms need server-side validation (Form Requests)
- Resolution: Create Form Requests for every action, never trust client input, include custom business rules

### 8. Test Data vs Production Data
- Issue: No fake data in production, but development needs testing data
- Resolution: Seeder classes for development, separate ProductionSeeder, never commit real data

### 9. Multi-Language Support
- Issue: Future requirement for multiple languages
- Resolution: Use Laravel localization from start, language files in `resources/lang/`, design DB to support i18n

### 10. Audit Log Volume
- Issue: Large audit log tables impacting performance
- Resolution: Partition audit logs, regular cleanup (retain 12 months minimum), index frequently queried columns

## Mitigation Priority

**High Priority:**
1. RBAC enforcement (server-side, not just menu hiding)
2. Document storage security (private + controller-served)
3. Status history protocol (never change without logging)
4. Duplicate candidate detection
5. Commission calculation accuracy

**Medium Priority:**
6. File upload validation
7. Email deliverability configuration
8. SEO on public website
9. Queue driver selection
10. Storage symlink alternative

**Low Priority:**
11. PHP 8.3 compatibility tweaks
12. Multi-language initial setup
13. Advanced reporting features
14. WhatsAPI integration (future)