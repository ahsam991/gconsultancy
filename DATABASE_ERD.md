# DATABASE ERD - Global Consultancy Education CRM

## Overview
**Database:** MySQL 8 / MariaDB  
**Principle:** One Candidate can have MANY Applications. Never duplicate candidate personal data inside applications table. Use Foreign Keys.

## ER Diagram (Mermaid)

```mermaid
erDiagram
    %% Core Users & Authentication
    USERS ||--|{ ROLES : "has many"
    USERS ||--|{ PERMISSIONS : "has many"
    ROLES ||--|{ PERMISSIONS : "has many"
    ROLES ||--|{ USERS : "assigned to"
    
    %% Countries & Locations
    COUNTRIES {
        int country_id PK
        varchar name
        varchar iso_code
        varchar dial_code
        timestamp created_at
        timestamp updated_at
    }
    CITIES {
        int city_id PK
        int country_id FK
        varchar name
        varchar area_code
        timestamp created_at
        timestamp updated_at
    }
    
    %% Study Configuration
    STUDY_LEVELS {
        int study_level_id PK
        varchar name    "e.g., Secondary, Bachelor, Master, PhD"
        varchar code    "e.g., SEC, BAC, MAS, PHD"
        timestamp created_at
        timestamp updated_at
    }
    SUBJECTS {
        int subject_id PK
        varchar name    "e.g., Computer Science, Business, Engineering"
        varchar code
        timestamp created_at
        timestamp updated_at
    }
    
    %% Universities & Courses
    UNIVERSITIES ||--|{ CAMPUSES : "has many"
    UNIVERSITIES {
        int university_id PK
        varchar uid    "Unique identifier code"
        varchar name
        int country_id FK
        varchar website
        varchar partner_status    "e.g., Partner, Affiliated, Non-Partner"
        decimal commission_rate   "Default commission %"
        varchar agreement_start
        varchar agreement_end
        text description
        string logo        "Path to logo file"
        boolean active
        timestamp created_at
        timestamp updated_at
    }
    CAMPUSES {
        int campus_id PK
        int university_id FK
        varchar name
        int city_id FK
        varchar address
        varchar website
        boolean active
        timestamp created_at
        timestamp updated_at
    }
    COURSES ||--|{ UNIVERSITIES : "belongs to"
    COURSES {
        int course_id PK
        int university_id FK
        int campus_id FK (nullable)
        int subject_id FK
        int study_level_id FK
        varchar name
        varchar code
        varchar study_mode    "e.g., Full-time, Part-time, Online"
        int duration_years
        decimal tuition_fee
        varchar currency    "e.g., GBP, USD, EUR, AUD"
        decimal deposit     "Required deposit amount"
        decimal ielts_req   "Minimum IELTS score"
        decimal toefl_req   "Minimum TOEFL score"
        decimal gpa_req     "Minimum GPA requirement"
        varchar intake      "e.g., January, May, September"
        date intake_start
        date intake_end
        varchar url        "Course URL"
        boolean active
        boolean featured
        text description
        timestamp created_at
        timestamp updated_at
    }
    
    %% Intakes
    INTAKES {
        int intake_id PK
        int course_id FK
        varchar name    "e.g., January Intake, September Intake"
        date start_date
        date application_deadline
        boolean active
        timestamp created_at
        timestamp updated_at
    }
    
    %% Lead Sources
    LEAD_SOURCES {
        int lead_source_id PK
        varchar name    "e.g., Website, Facebook, Instagram, Referral, Walk-in, Phone, Event, Fair"
        varchar code
        boolean active
        timestamp created_at
        timestamp updated_at
    }
    
    %% Candidates
    CANDIDATES {
        int candidate_id PK
        string uid      "Unique identifier (e.g., GCE-0001)"
        int user_id FK (nullable)    "Linked to auth users table"
        string first_name
        string last_name
        string email    "Unique index"
        string phone    "Unique index"
        string passport_no (nullable)
        string gender
        date dob
        string nationality
        string preferred_country    "Study destination preference"
        int preferred_study_level FK (nullable)
        string referral_source (nullable)
        string current_address (nullable)
        string permanent_address (nullable)
        string uen_code    "University Entry Number / Reference"
        string education_agent (nullable)
        string status    "NEW, CONTACTED, COUNSELLING, etc."
        date created_at
        date updated_at
        int created_by FK (nullable)
        int updated_by FK (nullable)
    }
    CANDIDATES ||--|{ ACADEMIC_QUALIFICATIONS : "has many"
    CANDIDATES ||--|{ ENGLISH_TESTS : "has many"
    CANDIDATES ||--|{ CANDIDATE_ADDRESSES : "has many"
    CANDIDATES ||--|{ EMERGENCY_CONTACTS : "has many"
    CANDIDATES ||--|{ DOCUMENTS : "has many"
    CANDIDATES ||--|{ APPLICATIONS : "has many"
    CANDIDATES ||--|{ TASKS : "has many"
    CANDIDATES ||--|{ APPOINTMENTS : "has many"
    CANDIDATES ||--|{ VISA_CASES : "has many"
    CANDIDATES ||--|{ COMMISSIONS : "has many"
    CANDIDATES ||--|{ INVOICES : "has many"
    
    ACADEMIC_QUALIFICATIONS {
        int qual_id PK
        int candidate_id FK
        varchar institution
        int country_id FK
        int study_level_id FK
        varchar qualification_type    "e.g., SSC, HSC, Diploma, Bachelor, Master"
        int passing_year
        decimal result    "e.g., Grade, Percentage"
        varchar grading_scale (nullable)
        string certificate_url (nullable)
        timestamp created_at
        timestamp updated_at
    }
    ENGLISH_TESTS {
        int test_id PK
        int candidate_id FK
        varchar test_type    "e.g., IELTS, TOEFL, PTE, Duolingo"
        decimal overall_score
        decimal listening (nullable)
        decimal reading (nullable)
        decimal writing (nullable)
        decimal speaking (nullable)
        date test_date
        date expiry_date
        string certificate_url (nullable)
        timestamp created_at
        timestamp updated_at
    }
    CANDIDATE_ADDRESSES {
        int address_id PK
        int candidate_id FK
        int country_id FK
        varchar address_line1
        varchar address_line2 (nullable)
        varchar city (nullable)
        varchar postal_code (nullable)
        varchar address_type    "e.g., Current, Permanent"
        timestamp created_at
        timestamp updated_at
    }
    EMERGENCY_CONTACTS {
        int contact_id PK
        int candidate_id FK
        varchar name
        relationship    "e.g., Mother, Father, Spouse"
        string phone
        string address (nullable)
        timestamp created_at
        timestamp updated_at
    }
    
    %% Documents
    DOCUMENT_TYPES {
        int document_type_id PK
        varchar name    "e.g., Passport, NID, Academic Transcript, English Certificate, CV, SOP, LOR, Bank Statement, CAS, Visa"
        varchar category    "e.g., Identity, Academic, English, Application, Financial, Visa"
        boolean requires_verification
        timestamp created_at
        timestamp updated_at
    }
    CANDIDATE_DOCUMENTS {
        int document_id PK
        int candidate_id FK
        int application_id FK (nullable)
        int document_type_id FK
        string original_filename
        string stored_filename
        string mime_type
        decimal size    "In bytes"
        int uploaded_by FK
        string verification_status    "REQUIRED, UPLOADED, UNDER_REVIEW, VERIFIED, REJECTED, EXPIRED"
        int verified_by FK (nullable)
        string rejection_reason (nullable)
        int version    "For versioning - never overwrite, create new version"
        text notes (nullable)
        timestamp uploaded_at
        timestamp verified_at (nullable)
        timestamp created_at
        timestamp updated_at
    }
    CANDIDATE_DOCUMENTS ||--|{ DOCUMENT_VERSIONS : "has many versions"
    
    %% Applications (MOST IMPORTANT)
    APPLICATIONS {
        int application_id PK
        string uid      "Unique application identifier"
        int candidate_id FK
        int university_id FK
        int campus_id FK (nullable)
        int course_id FK
        int intake_id FK (nullable)
        int assigned_staff_id FK    "Staff member assigned"
        int assigned_manager_id FK (nullable)
        date submission_date (nullable)
        string university_ref    "University reference number"
        string status    "DRAFT, PROFILE_CHECK, DOCUMENT_PENDING, READY_TO_APPLY, SUBMITTED, ACKNOWLEDGED, UNDER_REVIEW, INTERVIEW_REQUIRED, CONDITIONAL_OFFER, UNCONDITIONAL_OFFER, DEPOSIT_REQUIRED, DEPOSIT_PAID, CAS_REQUESTED, CAS_ISSUED, VISA_PREPARATION, VISA_APPLIED, VISA_APPROVED, VISA_REFUSED, ENROLLED, WITHDRAWN, REJECTED, CLOSED"
        decimal tuition_fee (nullable)
        decimal deposit (nullable)
        varchar currency    "e.g., GBP, USD, EUR, AUD"
        date applied_at (nullable)
        date deposit_due_date (nullable)
        date deposit_paid_at (nullable)
        string transaction_ref (nullable)
        text notes (nullable)
        timestamp created_at
        timestamp updated_at
        int created_by FK (nullable)
        int updated_by FK (nullable)
    }
    APPLICATIONS ||--|{ APPLICATION_STATUS_HISTORY : "has many status changes"
    APPLICATIONS ||--|{ DOCUMENTS : "has many (application-level docs)"
    APPLICATIONS ||--|{ OFFERS : "has many"
    APPLICATIONS ||--|{ DEPOSITS : "has many"
    APPLICATIONS ||--|{ CAS_RECORDS : "has many"
    APPLICATIONS ||--|{ VISA_CASES : "has many"
    APPLICATIONS ||--|{ ENROLMENTS : "has many"
    APPLICATIONS ||--|{ TASKS : "has many"
    APPLICATIONS ||--|{ COMMISSIONS : "has many"
    APPLICATIONS ||--|{ INVOICES : "has many"
    
    APPLICATION_STATUS_HISTORY {
        int history_id PK
        int application_id FK
        string previous_status
        string new_status
        int changed_by FK    "User ID who made the change"
        timestamp changed_at
        text reason (nullable)
        text note (nullable)
    }
    
    %% Offers
    OFFERS {
        int offer_id PK
        int application_id FK
        string offer_type    "Conditional/Unconditional"
        date offer_date
        date deadline
        decimal deposit_amount (nullable)
        decimal scholarship_amount (nullable)
        varchar scholarship_conditions (nullable)
        string document_id (nullable)
        string status    "RECEIVED, PENDING, ACCEPTED, DECLINED, EXPIRED"
        timestamp created_at
        timestamp updated_at
    }
    
    %% Deposits
    DEPOSITS {
        int deposit_id PK
        int application_id FK
        decimal required_amount
        decimal paid_amount (nullable)
        date due_date
        date paid_date (nullable)
        string payment_method    "e.g., BANK_TRANSFER, CARD, CASH"
        string transaction_ref (nullable)
        string receipt_url (nullable)
        string status    "PENDING, PARTIAL, FULL, REFUNDED, FAILED"
        timestamp created_at
        timestamp updated_at
    }
    
    %% CAS Records
    CAS_RECORDS {
        int cas_id PK
        int application_id FK
        string cas_number (nullable)
        date requested_date (nullable)
        date issued_date (nullable)
        string status    "NOT_REQUESTED, REQUESTED, UNDER_REVIEW, ISSUED, CANCELLED"
        decimal cas_fee (nullable)
        decimal paid_cas_fee (nullable)
        decimal balance_cas_fee (nullable)
        timestamp requested_at (nullable)
        timestamp issued_at (nullable)
        timestamp created_at
        timestamp updated_at
    }
    
    %% Visa Cases
    VISA_CASES {
        int visa_id PK
        int application_id FK
        int candidate_id FK
        varchar destination    "e.g., UK, USA, Canada, Australia"
        varchar visa_type    "e.g., Student Route, Tier 4"
        date application_date (nullable)
        date biometrics_date (nullable)
        date interview_date (nullable)
        date decision_date (nullable)
        string reference_number (nullable)
        string result    "e.g., Approved, Refused, Withdrawn"
        string status    "NOT_STARTED, DOCUMENTARY, BIOMETRICS, INTERVIEW, DECISION, APPROVED, REFUSED, CANCELLED"
        varchar notes (nullable)
        timestamp created_at
        timestamp updated_at
    }
    
    %% Enrolment
    ENROLMENTS {
        int enrolment_id PK
        int application_id FK
        int candidate_id FK
        int campus_id FK
        date enrolment_date
        string student_number (nullable)
        string status    "PENDING, CONFIRMED, ENROLLED, DEFERRED, WITHDRAWN, COMPLETED"
        timestamp created_at
        timestamp updated_at
    }
    
    %% Tasks
    TASKS {
        int task_id PK
        int candidate_id FK (nullable)
        int application_id FK (nullable)
        int assigned_user_id FK
        string title
        text description (nullable)
        string priority    "Low, Medium, High, Urgent"
        string status    "NEW, IN_PROGRESS, AWAITING_FEEDBACK, COMPLETED, CANCELLED"
        date due_date
        int notify_to    "Who to notify (e.g., candidate, staff, manager)"
        timestamp created_at
        timestamp updated_at
    }
    
    %% Action Notes
    ACTION_NOTES {
        int note_id PK
        int candidate_id FK (nullable)
        int application_id FK (nullable)
        int assigned_to FK    "User who created the note"
        string title
        text description
        string priority    "Low, Medium, High, Urgent"
        string status    "NEW, IN_PROGRESS, AWAITING_FEEDBACK, COMPLETED, CANCELLED"
        timestamp created_at
        timestamp updated_at
    }
    
    %% Appointments
    APPOINTMENTS {
        int appointment_id PK
        int candidate_id FK
        int staff_id FK    "Assigned staff member"
        string type    "e.g., Counselling, Visa, Pre-departure, Post-arrival"
        date appointment_date
        time start_time
        time end_time
        varchar location    "or meeting URL"
        string status    "SCHEDULED, CONFIRMED, COMPLETED, CANCELLED, NO_SHOW"
        text notes (nullable)
        timestamp created_at
        timestamp updated_at
    }
    
    %% Commission & Finance
    COMMISSION_RULES {
        int rule_id PK
        int university_id FK (nullable)
        varchar criteria    "e.g., Based on course, intake, candidate level"
        decimal rate    "Commission percentage"
        decimal min_amount (nullable)
        decimal max_amount (nullable)
        boolean active
        timestamp created_at
        timestamp updated_at
    }
    COMMISSIONS {
        int commission_id PK
        int university_id FK
        int candidate_id FK
        int application_id FK
        int intake_id FK
        decimal rate
        decimal amount
        varchar currency
        string status    "READY_TO_CLAIM, IN_REVIEW, CLAIMED, FULLY_RECEIVED, DUE, REJECTED, CLAWED_BACK"
        date claim_date (nullable)
        date expected_date (nullable)
        date received_date (nullable)
        timestamp created_at
        timestamp updated_at
    }
    INVOICES {
        int invoice_id PK
        string invoice_number    "Unique auto-generated"
        int university_id FK
        int candidate_id FK
        int application_id FK
        int commission_id FK (nullable)
        date invoice_date
        date due_date
        decimal subtotal
        decimal tax_amount
        decimal total
        string status    "PENDING, PARTIAL, PAID, REJECTED, CANCELLED"
        timestamp issued_at
        timestamp paid_at (nullable)
        timestamp created_at
        timestamp updated_at
    }
    INVOICE_ITEMS {
        int item_id PK
        int invoice_id FK
        varchar description
        decimal amount
        decimal tax (nullable)
        timestamp created_at
        timestamp updated_at
    }
    PAYMENTS {
        int payment_id PK
        int invoice_id FK
        decimal amount
        string method    "BANK_TRANSFER, CARD, CASH"
        string transaction_ref
        string receipt_url
        date payment_date
        string status    "PENDING, COMPLETED, FAILED, REFUNDED"
        timestamp created_at
        timestamp updated_at
    }
    
    %% Website CMS
    WEBSITE_PAGES {
        int page_id PK
        string slug    "e.g., home, about, services, contact"
        string title
        string meta_title (nullable)
        string meta_description (nullable)
        string meta_keywords (nullable)
        string content    "HTML content"
        string template    "e.g., default, landing, full-width"
        boolean active
        timestamp created_at
        timestamp updated_at
    }
    TESTIMONIALS {
        int testimonial_id PK
        string name
        string country
        text content
        string rating    "1-5 stars"
        boolean is_featured
        timestamp created_at
        timestamp updated_at
    }
    TEAM_MEMBERS {
        int team_member_id PK
        string name
        string role
        string photo_path (nullable)
        string linkedin_url (nullable)
        string twitter_url (nullable)
        bool is_active
        timestamp created_at
        timestamp updated_at
    }
    BLOG_POSTS {
        int blog_id PK
        string title
        string slug
        text excerpt
        text content
        string author_id FK (nullable)
        boolean published
        date published_at (nullable)
        timestamp created_at
        timestamp updated_at
    }
    
    %% Leads
    LEADS {
        int lead_id PK
        int lead_source_id FK (nullable)
        string first_name
        string last_name
        string email    "Unique index"
        string phone (nullable)
        string country_id FK (nullable)
        string preferred_country (nullable)
        string preferred_study_level (nullable)
        string source_url (nullable)
        string current_status    "NEW, CONTACTED, QUALIFIED, COUNSELLING, CONVERTED, LOST"
        string notes (nullable)
        date created_at
        date updated_at
        int created_by FK
    }
    LEADS ||--|{ APPLICATIONS : "may convert to"
    
    %% Settings
    SETTINGS {
        int setting_id PK
        varchar key    "Unique key"
        text value
        string description (nullable)
        timestamp updated_at
    }
    EMAIL_TEMPLATES {
        int template_id PK
        string name
        string slug    "e.g., welcome, status_change, invoice"
        string subject
        text content
        boolean is_html
        timestamp created_at
        timestamp updated_at
    }
    
    %% Audit Logs
    AUDIT_LOGS {
        int log_id PK
        string action
        int user_id FK (nullable)
        int candidate_id FK (nullable)
        int application_id FK (nullable)
        string ip_address (nullable)
        string user_agent (nullable)
        timestamp created_at
    }
```

## Indexes Required (Critical for Performance)

```sql
-- Unique indexes
CREATE UNIQUE INDEX idx_users_email ON users(email);
CREATE UNIQUE INDEX idx_users_phone ON users(phone);
CREATE UNIQUE INDEX idx_candidates_email ON candidates(email);
CREATE UNIQUE INDEX idx_candidates_phone ON candidates(phone);
CREATE UNIQUE INDEX idx_candidates_uid ON candidates(uid);
CREATE UNIQUE INDEX idx_applications_uid ON applications(uid);
CREATE UNIQUE INDEX idx_invoices_invoice_number ON invoices(invoice_number);

-- Foreign key indexes
CREATE INDEX idx_candidates_user_id ON users(id);
CREATE INDEX idx_candidates_country_id ON countries(id);
CREATE INDEX idx_candidates_preferred_study_level ON study_levels(id);
CREATE INDEX idx_universities_country_id ON countries(id);
CREATE INDEX idx_courses_university_id ON universities(id);
CREATE INDEX idx_courses_subject_id ON subjects(id);
CREATE INDEX idx_courses_study_level_id ON study_levels(id);
CREATE INDEX idx_campuses_university_id ON universities(id);
CREATE INDEX idx_campuses_city_id ON cities(id);
CREATE INDEX idx_intakes_course_id ON courses(id);
CREATE INDEX idx_candidate_documents_candidate_id ON candidate_documents(candidate_id);
CREATE INDEX idx_candidate_documents_application_id ON candidate_documents(application_id);
CREATE INDEX idx_candidate_documents_document_type_id ON document_types(id);
CREATE INDEX idx_applications_university_id ON universities(id);
CREATE INDEX idx_applications_course_id ON courses(id);
CREATE INDEX idx_applications_intake_id ON intakes(id);
CREATE INDEX idx_applications_assigned_staff_id ON users(id);
CREATE INDEX idx_applications_status ON applications(status);
CREATE INDEX idx_offers_application_id ON applications(id);
 CAS_RECORDS.application_id ON applications(id);
CREATE INDEX idx_visa_cases_application_id ON visa_cases(application_id);
CREATE INDEX idx_visa_cases_candidate_id ON visa_cases(candidate_id);
CREATE INDEX idx_deposits_application_id ON deposits(application_id);
CREATE INDEX idx_enrolments_application_id ON enrolments(application_id);
CREATE INDEX idx_tasks_candidate_id ON tasks(candidate_id);
CREATE INDEX idx_tasks_application_id ON tasks(application_id);
CREATE INDEX idx_tasks_assigned_user_id ON users(id);
CREATE INDEX idx_action_notes_candidate_id ON action_notes(candidate_id);
CREATE INDEX idx_action_notes_application_id ON action_notes(application_id);
CREATE INDEX idx_commissions_application_id ON commissions(application_id);
CREATE INDEX idx_commissions_candidate_id ON commissions(candidate_id);
CREATE INDEX idx_invoices_invoice_number ON invoices(invoice_number);
CREATE INDEX idx_invoice_items_invoice_id ON invoice_items(invoice_id);
CREATE INDEX idx_payments_invoice_id ON payments(invoice_id);
CREATE INDEX idx_leads_email ON leads(email);
CREATE INDEX idx_leads_status ON leads(status);
```

## Table Columns Complete List (All Core Tables)

### users
- id (PK, bigint)
- name (varchar)
- email (varchar, unique)
- password (varchar)
- phone (varchar, nullable)
- address (text, nullable)
- role_id (int, FK)
- status (enum: active, inactive, suspended)
- email_verified_at (timestamp)
- remember_token (varchar)
- created_by (int, FK)
- updated_by (int, FK)
- created_at (timestamp)
- updated_at (timestamp)

### roles
- id (PK, bigint)
- name (varchar)    "e.g., admin, manager, staff, candidate"
- guard_name (varchar)    "e.g., web, api"
- created_at (timestamp)
- updated_at (timestamp)

### permissions
- id (PK, bigint)
- name (varchar)    "e.g., candidates.view, candidates.create"
- guard_name (varchar)
- created_at (timestamp)

### role_permissions
- id (PK, bigint)
- role_id (int, FK)
- permission_id (int, FK)

### countries
- id (PK, bigint)
- name (varchar)
- iso_code (char 2)
- dial_code (varchar)
- active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### cities
- id (PK, bigint)
- country_id (int, FK)
- name (varchar)
- area_code (varchar, nullable)
- active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### study_levels
- id (PK, bigint)
- name (varchar)
- code (varchar)
- active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### subjects
- id (PK, bigint)
- name (varchar)
- code (varchar, nullable)
- description (text, nullable)
- active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### universities
- id (PK, bigint)
- uid (varchar, unique)
- name (varchar)
- country_id (int, FK)
- website (varchar, nullable)
- partner_status (varchar)
- commission_rate (decimal, default 0)
- agreement_start (date, nullable)
- agreement_end (date, nullable)
- description (text, nullable)
- logo (varchar, nullable)    "Path to stored logo"
- active (boolean)
- featured (boolean, default false)
- created_at (timestamp)
- updated_at (timestamp)

### campuses
- id (PK, bigint)
- university_id (int, FK)
- name (varchar)
- city_id (int, FK, nullable)
- address (text)
- website (varchar, nullable)
- active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### courses
- id (PK, bigint)
- university_id (int, FK)
- campus_id (int, FK, nullable)
- subject_id (int, FK)
- study_level_id (int, FK)
- name (varchar)
- code (varchar, nullable)
- study_mode (varchar)
- duration_years (int)
- tuition_fee (decimal)
- currency (varchar, default 'GBP')
- deposit (decimal)
- ielts_req (decimal, nullable)
- toefl_req (decimal, nullable)
- gpa_req (decimal, nullable)
- intake (varchar, nullable)    "e.g., January, May, September"
- intake_start (date, nullable)
- intake_end (date, nullable)
- url (varchar, nullable)
- active (boolean)
- featured (boolean, default false)
- description (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### intakes
- id (PK, bigint)
- course_id (int, FK)
- name (varchar)
- start_date (date)
- application_deadline (date)
- active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### academic_qualifications
- id (PK, bigint)
- candidate_id (int, FK)
- institution (varchar)
- country_id (int, FK)
- study_level_id (int, FK)
- qualification_type (varchar)
- passing_year (int)
- result (decimal)
- grading_scale (varchar, nullable)
- certificate_url (varchar, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### english_tests
- id (PK, bigint)
- candidate_id (int, FK)
- test_type (varchar)
- overall_score (decimal)
- listening (decimal, nullable)
- reading (decimal, nullable)
- writing (decimal, nullable)
- speaking (decimal, nullable)
- test_date (date)
- expiry_date (date)
- certificate_url (varchar, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### candidate_addresses
- id (PK, bigint)
- candidate_id (int, FK)
- country_id (int, FK)
- address_line1 (varchar)
- address_line2 (varchar, nullable)
- city (varchar, nullable)
- postal_code (varchar, nullable)
- address_type (varchar)
- created_at (timestamp)
- updated_at (timestamp)

### emergency_contacts
- id (PK, bigint)
- candidate_id (int, FK)
- name (varchar)
- relationship (varchar)
- phone (varchar)
- address (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### document_types
- id (PK, bigint)
- name (varchar)
- category (varchar)
- requires_verification (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### candidate_documents
- id (PK, bigint)
- candidate_id (int, FK)
- application_id (int, FK, nullable)
- document_type_id (int, FK)
- original_filename (varchar)
- stored_filename (varchar)
- mime_type (varchar)
- size (decimal)
- uploaded_by (int, FK)
- verification_status (enum: REQUIRED, UPLOADED, UNDER_REVIEW, VERIFIED, REJECTED, EXPIRED)
- verified_by (int, FK, nullable)
- rejection_reason (text, nullable)
- version (int, default 1)
- notes (text, nullable)
- uploaded_at (timestamp)
- verified_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### document_versions
- id (PK, bigint)
- document_id (int, FK)
- version_number (int)
- file_path (varchar)
- change_notes (text, nullable)
- created_at (timestamp)

### applications
- id (PK, bigint)
- uid (varchar, unique)
- candidate_id (int, FK)
- university_id (int, FK)
- campus_id (int, FK, nullable)
- course_id (int, FK)
- intake_id (int, FK, nullable)
- assigned_staff_id (int, FK)
- assigned_manager_id (int, FK, nullable)
- submission_date (date, nullable)
- university_ref (varchar, nullable)
- status (enum: extensive pipeline - see text)
- tuition_fee (decimal, nullable)
- deposit (decimal, nullable)
- currency (varchar, default 'GBP')
- applied_at (date, nullable)
- deposit_due_date (date, nullable)
- deposit_paid_at (date, nullable)
- transaction_ref (varchar, nullable)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### application_status_history
- id (PK, bigint)
- application_id (int, FK)
- previous_status (varchar)
- new_status (varchar)
- changed_by (int, FK)
- changed_at (timestamp)
- reason (text, nullable)
- note (text, nullable)

### offers
- id (PK, bigint)
- application_id (int, FK)
- offer_type (varchar)    "Conditional/Unconditional"
- offer_date (date)
- deadline (date)
- deposit_amount (decimal, nullable)
- scholarship_amount (decimal, nullable)
- scholarship_conditions (text, nullable)
- document_id (int, FK, nullable)
- status (enum: RECEIVED, PENDING, ACCEPTED, DECLINED, EXPIRED)
- created_at (timestamp)
- updated_at (timestamp)

### deposits
- id (PK, bigint)
- application_id (int, FK)
- required_amount (decimal)
- paid_amount (decimal, nullable)
- due_date (date)
- paid_date (date, nullable)
- payment_method (varchar)
- transaction_ref (varchar, nullable)
- receipt_url (varchar, nullable)
- status (enum: PENDING, PARTIAL, FULL, REFUNDED, FAILED)
- created_at (timestamp)
- updated_at (timestamp)

### cas_records
- id (PK, bigint)
- application_id (int, FK)
- cas_number (varchar, nullable)
- requested_date (date, nullable)
- issued_date (date, nullable)
- status (enum: NOT_REQUESTED, REQUESTED, UNDER_REVIEW, ISSUED, CANCELLED)
- cas_fee (decimal, nullable)
- paid_cas_fee (decimal, nullable)
- balance_cas_fee (decimal, nullable)
- requested_at (timestamp, nullable)
- issued_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### visa_cases
- id (PK, bigint)
- application_id (int, FK)
- candidate_id (int, FK)
- destination (varchar)    "e.g., UK, USA, Canada, Australia"
- visa_type (varchar)    "e.g., Student Route, Tier 4"
- application_date (date, nullable)
- biometrics_date (date, nullable)
- interview_date (date, nullable)
- decision_date (date, nullable)
- reference_number (varchar, nullable)
- result (varchar, nullable)    "e.g., Approved, Refused, Withdrawn"
- status (enum: NOT_STARTED, DOCUMENTARY, BIOMETRICS, INTERVIEW, DECISION, APPROVED, REFUSED, CANCELLED)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### enrolments
- id (PK, bigint)
- application_id (int, FK)
- candidate_id (int, FK)
- campus_id (int, FK)
- enrolment_date (date)
- student_number (varchar, nullable)
- status (enum: PENDING, CONFIRMED, ENROLLED, DEFERRED, WITHDRAWN, COMPLETED)
- created_at (timestamp)
- updated_at (timestamp)

### tasks
- id (PK, bigint)
- candidate_id (int, FK, nullable)
- application_id (int, FK, nullable)
- assigned_user_id (int, FK)
- title (varchar)
- description (text, nullable)
- priority (enum: Low, Medium, High, Urgent)
- status (enum: NEW, IN_PROGRESS, AWAITING_FEEDBACK, COMPLETED, CANCELLED)
- notify_to (varchar)    "e.g., candidate, staff, manager"
- due_date (date)
- created_at (timestamp)
- updated_at (timestamp)

### action_notes
- id (PK, bigint)
- candidate_id (int, FK, nullable)
- application_id (int, FK, nullable)
- assigned_to (int, FK)
- title (varchar)
- description (text)
- priority (enum: Low, Medium, High, Urgent)
- status (enum: NEW, IN_PROGRESS, AWAITING_FEEDBACK, COMPLETED, CANCELLED)
- created_at (timestamp)
- updated_at (timestamp)

### appointments
- id (PK, bigint)
- candidate_id (int, FK)
- staff_id (int, FK)
- type (varchar)    "e.g., Counselling, Visa, Pre-departure, Post-arrival"
- appointment_date (date)
- start_time (time)
- end_time (time)
- location (varchar)    "Physical address or meeting URL"
- status (enum: SCHEDULED, CONFIRMED, COMPLETED, CANCELLED, NO_SHOW)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### commission_rules
- id (PK, bigint)
- university_id (int, FK, nullable)
- criteria (text, nullable)
- rate (decimal)
- min_amount (decimal, nullable)
- max_amount (decimal, nullable)
- active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### commissions
- id (PK, bigint)
- university_id (int, FK)
- candidate_id (int, FK)
- application_id (int, FK)
- intake_id (int, FK)
- rate (decimal)
- amount (decimal)
- currency (varchar, default 'GBP')
- status (enum: READY_TO_CLAIM, IN_REVIEW, CLAIMED, FULLY_RECEIVED, DUE, REJECTED, CLAWED_BACK)
- claim_date (date, nullable)
- expected_date (date, nullable)
- received_date (date, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### invoices
- id (PK, bigint)
- invoice_number (varchar, unique, auto-generated)
- university_id (int, FK)
- candidate_id (int, FK)
- application_id (int, FK)
- commission_id (int, FK, nullable)
- invoice_date (date)
- due_date (date)
- subtotal (decimal)
- tax_amount (decimal)
- total (decimal)
- status (enum: PENDING, PARTIAL, PAID, REJECTED, CANCELLED)
- issued_at (timestamp)
- paid_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### invoice_items
- id (PK, bigint)
- invoice_id (int, FK)
- description (varchar)
- amount (decimal)
- tax (decimal, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### payments
- id (PK, bigint)
- invoice_id (int, FK)
- amount (decimal)
- method (varchar)    "BANK_TRANSFER, CARD, CASH"
- transaction_ref (varchar, nullable)
- receipt_url (varchar, nullable)
- payment_date (date)
- status (enum: PENDING, COMPLETED, FAILED, REFUNDED)
- created_at (timestamp)
- updated_at (timestamp)

### website_pages
- id (PK, bigint)
- slug (varchar, unique)
- title (varchar)
- meta_title (varchar, nullable)
- meta_description (varchar, nullable)
- meta_keywords (varchar, nullable)
- content (text)
- template (varchar, default 'default')
- active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### testimonials
- id (PK, bigint)
- name (varchar)
- country (varchar)
- content (text)
- rating (decimal, 1-5)
- is_featured (boolean, default false)
- created_at (timestamp)
- updated_at (timestamp)

### team_members
- id (PK, bigint)
- name (varchar)
- role (varchar)
- photo_path (varchar, nullable)
- linkedin_url (varchar, nullable)
- twitter_url (varchar, nullable)
- is_active (boolean)
- created_at (timestamp)
- updated_at (timestamp)

### blog_posts
- id (PK, bigint)
- title (varchar)
- slug (varchar, unique)
- excerpt (text, nullable)
- content (text)
- author_id (int, FK, nullable)
- published (boolean, default false)
- published_at (date, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### leads
- id (PK, bigint)
- lead_source_id (int, FK, nullable)
- first_name (varchar)
- last_name (varchar)
- email (varchar, unique)
- phone (varchar, nullable)
- country_id (int, FK, nullable)
- preferred_country (varchar, nullable)
- preferred_study_level (varchar, nullable)
- source_url (varchar, nullable)
- notes (text, nullable)
- current_status (varchar)
- created_at (timestamp)
- updated_at (timestamp)
- created_by (int, FK)

### settings
- id (PK, bigint)
- key (varchar, unique)
- value (text)
- description (text, nullable)
- updated_at (timestamp)

### email_templates
- id (PK, bigint)
- name (varchar)
- slug (varchar, unique)
- subject (varchar)
- content (text)
- is_html (boolean, default true)
- created_at (timestamp)
- updated_at (timestamp)

### audit_logs
- id (PK, bigint)
- action (varchar)
- user_id (int, FK, nullable)
- candidate_id (int, FK, nullable)
- application_id (int, FK, nullable)
- ip_address (varchar, nullable)
- user_agent (text, nullable)
- created_at (timestamp)