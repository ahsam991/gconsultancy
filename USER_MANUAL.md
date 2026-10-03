# User Manual — Counsellors (Staff / Manager)

**Audience:** counsellors and team managers using the CRM daily (roles: `staff`, `manager`). Admin-only tasks are covered in `ADMIN_MANUAL.md`.
**System:** Laravel CRM for a study-abroad consultancy. CRM routes live under `/crm/*`, candidate portal under `/portal/*`, public site at `/`.
**Demo logins (change after first login):**

| Role | Email | Password |
|---|---|---|
| Admin | admin@globalconsultancy.com | password123 |
| Manager | manager@globalconsultancy.com | password123 |
| Staff | staff@globalconsultancy.com | password123 |
| Candidate | candidate@globalconsultancy.com | password123 |

## Table of Contents

1. [Logging In](#1-logging-in)
2. [Reading the Dashboard](#2-reading-the-dashboard)
3. [Candidate Lifecycle: Lead to Candidate](#3-candidate-lifecycle-lead-to-candidate)
4. [Candidate Profile Wizard (6 Steps)](#4-candidate-profile-wizard-6-steps)
5. [Applications and the Legal Status Pipeline](#5-applications-and-the-legal-status-pipeline)
6. [Documents: Upload and Verify](#6-documents-upload-and-verify)
7. [Offers, Deposits, CAS, Visa, Enrolment](#7-offers-deposits-cas-visa-enrolment)
8. [Tasks, Appointments, Notes](#8-tasks-appointments-notes)
9. [Course Finder](#9-course-finder)
10. [Reports and CSV Export](#10-reports-and-csv-export)
11. [Candidate Portal Overview](#11-candidate-portal-overview)
12. [Daily Checklist](#12-daily-checklist)

## 1. Logging In

1. Go to `/auth/login`.
2. Enter your staff/manager email and password. Failed attempts are throttled (10 attempts per minute per IP/email); wait 60 seconds if you see a 429 page.
3. On success you land on `/crm/dashboard` (staff/manager/admin) — candidates land on `/portal/dashboard` instead.
4. To log out, submit the logout form (`POST /auth/logout`). Closing the tab alone does not end the session.
5. Forgot password: `/auth/forgot-password` → enter email → follow the reset link (`/auth/reset-password/{token}`).

> Visibility rule: staff see only candidates/applications assigned to them; managers see their team's. If a record is missing, check assignment, not search spelling.

## 2. Reading the Dashboard

1. Open `/crm/dashboard`.
2. Read top-to-bottom: KPI stat cards (leads, active candidates, applications by stage, visa outcomes) → charts → your upcoming tasks and appointments.
3. Click any card/row to drill into the filtered list (e.g. applications awaiting documents).
4. Use global search at `/crm/search?q=...` for UID, name, email, or university reference lookups.

## 3. Candidate Lifecycle: Lead to Candidate

Stages: **Lead → Candidate → counselling → profile collection → application(s) → journey to enrolment.**

1. **Work the lead list:** open `/crm/leads`. Filter by status/source, open a lead.
2. **Qualify:** call/email the lead, record outcome via `POST /crm/communications` (from the candidate/lead page) and add a note (`POST /crm/notes`).
3. **Convert:** on the lead page, submit `POST /crm/leads/{lead}/convert`. This creates the candidate record (UID like `GC-000001`).
4. **Assign:** set `assigned_staff_id` (and manager) on the candidate so the right counsellor sees it.
5. **Build the profile:** run the 6-step wizard (next section) until completion is high.
6. **Create application(s):** `/crm/applications/create` → pick candidate, university, course, intake → save as `DRAFT`.
7. **Bulk tools (manager):** `/crm/candidates-import` + `/crm/candidates-import-confirm` for CSV import; `/crm/candidates-bulk-assign` and `/crm/candidates-bulk-status` for team assignment; `/crm/candidates-archive` and `/crm/candidates-archived` for archiving/restoring.

## 4. Candidate Profile Wizard (6 Steps)

Open `/crm/candidates/{id}/wizard/{step}` where step is 1–6. Save each step; use "Save draft" to pause without advancing.

| Step | URL | What to enter |
|---|---|---|
| 1. Personal Info | `/crm/candidates/{id}/wizard/1` | First/last name, DOB, gender, nationality, passport no. + expiry, address, city, preferred destination/level/subject, referral source, assigned staff |
| 2. Academic | `/crm/candidates/{id}/wizard/2` | One entry per qualification: level, institution, country, passing year, result, grading scale (repeat for HSC, Bachelor, etc.) |
| 3. English Test | `/crm/candidates/{id}/wizard/3` | Test type (IELTS/PTE/TOEFL), overall + band scores, test date, expiry date |
| 4. Emergency Contact | `/crm/candidates/{id}/wizard/4` | Name, relationship, phone (required), email, address |
| 5. Documents | via step 5 / `/crm/documents/create` | Upload per checklist (see Section 6) |
| 6. Review & Submit | `/crm/candidates/{id}/wizard/6` | Review everything, submit. Sets `profile_completion` % and moves status `NEW` → `PROFILE_PENDING` |

Practical notes:

1. Completion % = share of these 5 checks: personal filled, ≥1 qualification, ≥1 English test, ≥1 emergency contact, ≥1 document.
2. Edit a submitted profile via `/crm/candidates/{id}/edit`; each wizard save is audit-logged.
3. Candidate list CSV: `GET /crm/candidates-export` (manager: team scope only).

## 5. Applications and the Legal Status Pipeline

Pipeline (in order): `DRAFT → PROFILE_CHECK → DOCUMENT_PENDING → READY_TO_APPLY → SUBMITTED → ACKNOWLEDGED → UNDER_REVIEW → INTERVIEW_REQUIRED → CONDITIONAL_OFFER → UNCONDITIONAL_OFFER → DEPOSIT_REQUIRED → DEPOSIT_PAID → CAS_REQUESTED → CAS_ISSUED → VISA_PREPARATION → VISA_APPLIED → VISA_APPROVED → ENROLLED`, with terminal branches `VISA_REFUSED`, `WITHDRAWN`, `REJECTED`, `CLOSED`.

1. **Create:** `/crm/applications/create` → candidate + university + campus + course + intake → starts at `DRAFT` (UID like `APP-000001`).
2. **Work it:** open `/crm/applications/{id}`. The page shows allowed next statuses (computed by `ApplicationStatusService`), the document checklist, and linked offer/deposit/CAS/visa/enrolment/commission records.
3. **Change status:** submit `POST /crm/applications/{id}/status` with fields `status` (uppercase), `reason`, `note`.
   - **Every change requires a reason** — the field may be technically nullable, but policy is: no reason, no transition. It writes an `ApplicationStatusHistory` row (`previous_status`, `new_status`, `changed_by`, timestamp, reason, note).
   - Invalid jumps are rejected with an error message; follow the pipeline order.
4. **Review history:** open `/crm/applications/{id}/timeline` to see the full who/when/why trail.
5. **Delete:** only admin/manager paths allow it and it is audit-logged; prefer `WITHDRAWN`/`CLOSED` over deleting.

## 6. Documents: Upload and Verify

Storage: private `storage/app/private/candidates/{id}/`, served only through the controller — never a direct public URL.

1. **Upload (staff/manager/candidate):** `/crm/documents/create` → select candidate (+ optional application), document type, file, notes → submit.
   - Allowed: `pdf, jpg, jpeg, png, doc, docx`, max **10 MB**. Larger or other types are rejected.
   - Re-uploading the same type for the same candidate/application bumps `version` and keeps a `DocumentVersion` row.
2. **Track:** `/crm/documents` (filter `?candidate_id=`), detail at `/crm/documents/{id}`.
3. **Download:** `GET /crm/documents/{id}/download` — policy-checked; staff get 403 outside their assignments.
4. **Verify (manager/admin only — staff cannot verify):**
   - Approve: `POST /crm/documents/{id}/verify` → status `VERIFIED` with verifier + timestamp.
   - Reject: `POST /crm/documents/{id}/reject` with required `rejection_reason` → status `REJECTED`; tell the candidate what to re-upload.
5. **Rename/notes:** `GET /crm/documents/{id}/edit` (notes only). Delete requires delete permission and is audit-logged.

## 7. Offers, Deposits, CAS, Visa, Enrolment

All are recorded per application from `/crm/applications/{id}` or the journey pages. Post to the nested routes below.

1. **Offer:** `POST /crm/applications/{id}/offers` — fields: `offer_type` (Conditional/Unconditional), `offer_date`, `deadline`, `deposit_amount`, `scholarship_amount`, conditions, status. List: `/crm/journey/offers`.
2. **Deposit:** `POST /crm/applications/{id}/deposits` — `required_amount`, `paid_amount`, `due_date`, `paid_date`, `payment_method`, `transaction_ref`, status. List: `/crm/journey/deposits`.
3. **CAS:** `POST /crm/applications/{id}/cas` — `cas_number`, `requested_date`, `issued_date`, status. List: `/crm/journey/cas`. Record the CAS number the day it arrives.
4. **Visa:** `POST /crm/applications/{id}/visas` — outcome, reference no., decision date. List: `/crm/journey/visas`. A refusal must record reasons and next action (appeal/reapply).
5. **Enrolment:** `POST /crm/applications/{id}/enrolments` — student ID, campus, enrolment date, status. List: `/crm/journey/enrolments`. Enrolment also triggers commission calculation.
6. **Overview board:** `/crm/journey` shows all applications mid-journey.

## 8. Tasks, Appointments, Notes

1. **Tasks:** list `/crm/tasks` (staff see own), create `/crm/tasks/create` (title, candidate/application, assignee, priority, status, due date) → detail `/crm/tasks/{id}`.
2. **Appointments:** list `/crm/appointments`, create `/crm/appointments/create`, view `/crm/appointments/{id}`, edit `/crm/appointments/{id}/edit`. Public booking form is at `/book-appointment` and lands here for confirmation.
3. **Notes:** from a candidate/application page, submit `POST /crm/notes`; delete via `DELETE /crm/notes/{id}`.
4. **Communications log:** `POST /crm/communications` (channel: call/email/SMS, direction inbound/outbound, subject, body) — log every meaningful contact.

## 9. Course Finder

1. Open `/crm/courses-finder` (public mirror: `/course-finder`, `/courses`).
2. Filter by subject keyword, study level, country, max tuition fee, and IELTS score. Results sort by tuition fee, 15 per page.
3. Click a course → university, campus, fee, deposit, IELTS requirement, intakes.
4. To propose it: create an application (`/crm/applications/create`) with that course, or send the public course page link to the candidate.

## 10. Reports and CSV Export

1. Start at `/crm/reports` and pick a report: candidates `/crm/reports/candidates`, applications `/crm/reports/applications`, visa `/crm/reports/visa`, finance `/crm/reports/finance`, enrolments `/crm/reports/enrolments`, staff workload `/crm/reports/staff`.
2. Filter by date range (`from`/`to`), status, university as offered on each page.
3. To download, add `?format=csv` to the report URL (e.g. `/crm/reports/applications?status=SUBMITTED&format=csv`), or use `/crm/reports/export?type=applications&format=csv` (types: candidates, applications, visa, finance; default candidates).
4. Candidate export: `/crm/candidates-export` (CSV columns: uid, names, email, phone, destination, status).

## 11. Candidate Portal Overview

What the candidate sees at `/portal/*` (role `candidate` only), so you can guide them:

| Page | URL | Candidate can |
|---|---|---|
| Dashboard | `/portal/dashboard` | Progress %, applications, documents, appointments, tasks |
| Profile | `/portal/profile` | View + update own profile (`PUT /portal/profile`) |
| Applications | `/portal/applications`, `/portal/applications/{id}` | View own applications and status (read-only) |
| Documents | `/portal/documents` | View + upload own documents (`POST /portal/documents`) |
| Appointments | `/portal/appointments` | View + request appointments (`POST /portal/appointments`) |
| Tasks | `/portal/tasks` | View tasks assigned to them |
| Notifications | `/portal/notifications` | View + mark read (`POST /portal/notifications/{id}/read`) |

If a candidate reports "page not found / 403", confirm they log in with the candidate account — CRM URLs (`/crm/*`) always return 403 for candidates.

## 12. Daily Checklist

1. `/crm/dashboard` — today's appointments and overdue tasks.
2. `/crm/tasks?status=NEW` and `/crm/appointments` — confirm, reschedule, or complete.
3. `/crm/applications?status=DOCUMENT_PENDING` — chase missing documents.
4. `/crm/documents` — upload new arrivals; managers clear the verify queue.
5. `/crm/journey/offers` + `/crm/journey/deposits` — follow up on deadlines and due dates.
6. Log every call/email (`POST /crm/communications`) and keep status reasons complete.
