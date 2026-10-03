# DATABASE_SCHEMA.md — Global Consultancy Education

Generated from migrations. Engine: MySQL 8 (prod) / SQLite (dev). All tables use UTC timestamps.

## academic_qualifications
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: academic_qualifications_candidate_id_index
FKs: candidate_id → candidates.id

## accommodations
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| numeric | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: accommodations_booking_status_index, accommodations_application_id_index, accommodations_candidate_id_index
FKs: application_id → applications.id, candidate_id → candidates.id

## action_notes
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: action_notes_application_id_index, action_notes_candidate_id_index
FKs: user_id → users.id, application_id → applications.id, candidate_id → candidates.id

## application_fees
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: application_fees_status_index, application_fees_application_id_index
FKs: application_id → applications.id

## application_status_history
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: application_status_history_application_id_index
FKs: changed_by → users.id, application_id → applications.id

## application_statuses
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: application_statuses_code_unique (UNIQUE)

## applications
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| numeric | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| date | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: applications_uid_unique (UNIQUE), applications_status_index, applications_course_id_index, applications_university_id_index, applications_candidate_id_index, applications_uid_index
FKs: assigned_staff_id → users.id, intake_id → intakes.id, course_id → courses.id, campus_id → campuses.id, university_id → universities.id, candidate_id → candidates.id

## appointments
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: appointments_status_index, appointments_staff_id_index, appointments_candidate_id_index
FKs: staff_id → users.id, candidate_id → candidates.id

## arrivals
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| date | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: arrivals_arrived_index, arrivals_candidate_id_index
FKs: candidate_id → candidates.id

## audit_logs
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: audit_logs_action_index, audit_logs_auditable_type_auditable_id_index, audit_logs_user_id_index
FKs: user_id → users.id

## automation_logs
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: automation_logs_trigger_event_index, automation_logs_related_type_related_id_index, automation_logs_automation_rule_id_index
FKs: automation_rule_id → automation_rules.id

## automation_rules
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: automation_rules_active_index, automation_rules_trigger_event_index

## backups
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: backups_status_index, backups_type_index
FKs: created_by → users.id

## banners
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: banners_sort_order_index, banners_active_index, banners_location_index

## blog_categories
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: blog_categories_slug_unique (UNIQUE), blog_categories_name_unique (UNIQUE), blog_categories_active_index, blog_categories_slug_index

## blog_posts
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: blog_posts_slug_unique (UNIQUE), blog_posts_published_at_index, blog_posts_status_index, blog_posts_blog_category_id_index
FKs: author_id → users.id, blog_category_id → blog_categories.id

## branches
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: branches_code_unique (UNIQUE), branches_active_index
FKs: manager_id → users.id

## cache
| Column | Type | Null | Default |
|---|---|---|---|
| varchar | 1 | NO | 1 |
| TEXT | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |

Indexes: cache_expiration_index, sqlite_autoindex_cache_1 (UNIQUE)

## cache_locks
| Column | Type | Null | Default |
|---|---|---|---|
| varchar | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |

Indexes: cache_locks_expiration_index, sqlite_autoindex_cache_locks_1 (UNIQUE)

## campuses
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: campuses_university_id_index
FKs: university_id → universities.id

## candidate_addresses
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: candidate_addresses_candidate_id_index
FKs: candidate_id → candidates.id

## candidate_documents
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| date | 0 | NO | 0 |

Indexes: candidate_documents_verification_status_index, candidate_documents_application_id_index, candidate_documents_candidate_id_index
FKs: verified_by → users.id, uploaded_by → users.id, document_type_id → document_types.id, application_id → applications.id, candidate_id → candidates.id

## candidates
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| date | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |

Indexes: candidates_user_id_unique (UNIQUE), candidates_uid_unique (UNIQUE), candidates_uid_index, candidates_status_index, candidates_phone_index, candidates_passport_no_index, candidates_email_unique (UNIQUE), candidates_email_index
FKs: branch_id → branches.id, user_id → users.id, country_id → countries.id, assigned_staff_id → users.id, assigned_manager_id → users.id

## cas_records
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: cas_records_cas_number_unique (UNIQUE), cas_records_status_index, cas_records_application_id_index
FKs: application_id → applications.id

## cities
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: cities_country_id_name_index
FKs: country_id → countries.id

## commission_rules
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| numeric | 1 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |

Indexes: commission_rules_university_id_index
FKs: university_id → universities.id

## commissions
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| date | 0 | NO | 0 |

Indexes: commissions_university_id_index, commissions_status_index, commissions_candidate_id_index, commissions_application_id_index
FKs: referral_partner_id → referral_partners.id, application_id → applications.id, candidate_id → candidates.id, university_id → universities.id

## communications
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: communications_application_id_index, communications_candidate_id_index
FKs: user_id → users.id, application_id → applications.id, candidate_id → candidates.id

## counselling_sessions
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| datetime | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: counselling_sessions_followup_date_index, counselling_sessions_status_index, counselling_sessions_session_date_index, counselling_sessions_staff_id_index, counselling_sessions_candidate_id_index
FKs: staff_id → users.id, candidate_id → candidates.id

## countries
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: countries_iso_code_unique (UNIQUE)

## course_intakes
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: course_intakes_intake_id_index, course_intakes_course_id_index, course_intakes_course_id_intake_id_unique (UNIQUE)
FKs: intake_id → intakes.id, course_id → courses.id

## course_requirements
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: course_requirements_course_id_index, course_requirements_course_id_document_type_id_unique (UNIQUE)
FKs: document_type_id → document_types.id, course_id → courses.id

## course_shortlists
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: course_shortlists_staff_id_index, course_shortlists_course_id_index, course_shortlists_candidate_id_index, course_shortlists_candidate_id_course_id_unique (UNIQUE)
FKs: staff_id → users.id, course_id → courses.id, candidate_id → candidates.id

## courses
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: courses_uid_unique (UNIQUE), courses_active_index, courses_name_index, courses_study_level_id_index, courses_subject_id_index, courses_university_id_index, courses_uid_index
FKs: study_level_id → study_levels.id, subject_id → subjects.id, campus_id → campuses.id, university_id → universities.id

## custom_field_values
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: custom_field_values_custom_field_id_index, custom_field_values_related_type_related_id_index
FKs: custom_field_id → custom_fields.id

## custom_fields
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: custom_fields_active_index, custom_fields_module_index, custom_fields_module_name_unique (UNIQUE)

## data_access_logs
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: data_access_logs_action_index, data_access_logs_candidate_id_index, data_access_logs_user_id_index
FKs: candidate_id → candidates.id, user_id → users.id

## deposits
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: deposits_status_index, deposits_application_id_index
FKs: application_id → applications.id

## document_access_logs
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: document_access_logs_action_index, document_access_logs_user_id_index, document_access_logs_candidate_document_id_index
FKs: user_id → users.id, candidate_document_id → candidate_documents.id

## document_requests
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: document_requests_due_date_index, document_requests_status_index, document_requests_document_type_id_index, document_requests_candidate_id_index
FKs: requested_by → users.id, document_type_id → document_types.id, candidate_id → candidates.id

## document_types
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: document_types_name_unique (UNIQUE), document_types_category_index

## document_versions
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: document_versions_candidate_document_id_index
FKs: uploaded_by → users.id, candidate_document_id → candidate_documents.id

## email_templates
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: email_templates_slug_unique (UNIQUE), email_templates_slug_index

## emergency_contacts
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: emergency_contacts_candidate_id_index
FKs: candidate_id → candidates.id

## english_tests
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| numeric | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: english_tests_candidate_id_index
FKs: candidate_id → candidates.id

## enrolments
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: enrolments_status_index, enrolments_candidate_id_index, enrolments_application_id_index
FKs: candidate_id → candidates.id, application_id → applications.id

## failed_jobs
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| datetime | 1 | NO | 0 |

Indexes: failed_jobs_uuid_unique (UNIQUE), failed_jobs_connection_queue_failed_at_index

## faqs
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

## form_submissions
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: form_submissions_form_id_index
FKs: form_id → forms.id

## forms
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: forms_slug_unique (UNIQUE), forms_active_index, forms_slug_index

## gdpr_consents
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: gdpr_consents_consent_type_index, gdpr_consents_candidate_id_index, gdpr_consents_candidate_id_consent_type_policy_version_unique (UNIQUE)
FKs: candidate_id → candidates.id

## gdpr_requests
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: gdpr_requests_type_index, gdpr_requests_status_index, gdpr_requests_candidate_id_index
FKs: requested_by → users.id, candidate_id → candidates.id

## intakes
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: intakes_year_month_index

## invoice_items
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: invoice_items_invoice_id_index
FKs: invoice_id → invoices.id

## invoices
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| date | 1 | NO | 0 |
| date | 0 | NO | 0 |
| numeric | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| varchar | 0 | NO | 0 |

Indexes: invoices_invoice_number_unique (UNIQUE), invoices_status_index, invoices_university_id_index, invoices_invoice_number_index
FKs: application_id → applications.id, candidate_id → candidates.id, commission_id → commissions.id, university_id → universities.id

## job_batches
| Column | Type | Null | Default |
|---|---|---|---|
| varchar | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |

Indexes: sqlite_autoindex_job_batches_1 (UNIQUE)

## jobs
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |

Indexes: jobs_queue_index

## lead_sources
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: lead_sources_name_unique (UNIQUE)

## leads
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: leads_status_index, leads_phone_index, leads_email_index
FKs: assigned_to → users.id, source_id → lead_sources.id, country_id → countries.id

## login_histories
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: login_histories_successful_index, login_histories_email_index, login_histories_user_id_index
FKs: user_id → users.id

## mail_logs
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: mail_logs_template_slug_index, mail_logs_status_index, mail_logs_to_email_index

## media_library
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: media_library_uploaded_by_index, media_library_mime_index
FKs: uploaded_by → users.id

## menu_items
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: menu_items_sort_order_index, menu_items_active_index, menu_items_parent_id_index, menu_items_menu_id_index
FKs: parent_id → menu_items.id, menu_id → menus.id

## menus
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: menus_active_index, menus_location_index

## messages
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: messages_sender_id_index, messages_candidate_id_index, messages_candidate_id_is_internal_index
FKs: sender_id → users.id, candidate_id → candidates.id

## migrations
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |

## notifications
| Column | Type | Null | Default |
|---|---|---|---|
| varchar | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: notifications_notifiable_type_notifiable_id_index, sqlite_autoindex_notifications_1 (UNIQUE)

## offer_conditions
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| date | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: offer_conditions_deadline_index, offer_conditions_is_completed_index, offer_conditions_offer_id_index
FKs: offer_id → offers.id

## offers
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: offers_status_index, offers_application_id_index
FKs: application_id → applications.id

## password_reset_tokens
| Column | Type | Null | Default |
|---|---|---|---|
| varchar | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: sqlite_autoindex_password_reset_tokens_1 (UNIQUE)

## payments
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| date | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: payments_invoice_id_index
FKs: received_by → users.id, invoice_id → invoices.id

## permissions
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

## predeparture_checklists
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: predeparture_checklists_candidate_id_index
FKs: candidate_id → candidates.id

## referral_partners
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| numeric | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: referral_partners_email_index, referral_partners_type_index, referral_partners_active_index

## referral_payments
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: referral_payments_status_index, referral_payments_referral_partner_id_index, referral_payments_commission_id_index
FKs: referral_partner_id → referral_partners.id, commission_id → commissions.id

## role_permissions
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: role_permissions_role_id_permission_id_unique (UNIQUE)
FKs: permission_id → permissions.id, role_id → roles.id

## roles
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

## saved_courses
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: saved_courses_course_id_index, saved_courses_candidate_id_index, saved_courses_candidate_id_course_id_unique (UNIQUE)
FKs: course_id → courses.id, candidate_id → candidates.id

## scholarships
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| date | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: scholarships_deadline_index, scholarships_active_index, scholarships_course_id_index, scholarships_university_id_index
FKs: course_id → courses.id, university_id → universities.id

## sessions
| Column | Type | Null | Default |
|---|---|---|---|
| varchar | 1 | NO | 1 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| TEXT | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |

Indexes: sessions_last_activity_index, sessions_user_id_index, sqlite_autoindex_sessions_1 (UNIQUE)

## settings
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: settings_key_unique (UNIQUE), settings_group_index

## sla_breaches
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| datetime | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: sla_breaches_detected_at_index, sla_breaches_related_type_related_id_index, sla_breaches_sla_policy_id_index
FKs: sla_policy_id → sla_policies.id

## sla_policies
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: sla_policies_active_index, sla_policies_event_index

## sponsorships
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| numeric | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: sponsorships_verified_index, sponsorships_candidate_id_index
FKs: candidate_id → candidates.id

## staff_availabilities
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| time | 1 | NO | 0 |
| time | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: staff_availabilities_branch_id_index, staff_availabilities_day_of_week_index, staff_availabilities_user_id_index, staff_availabilities_user_id_day_of_week_start_time_unique (UNIQUE)
FKs: branch_id → branches.id, user_id → users.id

## status_transitions
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: status_transitions_active_index, status_transitions_to_status_index, status_transitions_from_status_index, status_transitions_workflow_template_id_index
FKs: workflow_template_id → workflow_templates.id

## student_payments
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| numeric | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: student_payments_purpose_index, student_payments_status_index, student_payments_application_id_index, student_payments_candidate_id_index
FKs: application_id → applications.id, candidate_id → candidates.id

## study_levels
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: study_levels_code_unique (UNIQUE)

## subjects
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

## tasks
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| date | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: tasks_status_index, tasks_assigned_to_index, tasks_application_id_index, tasks_candidate_id_index
FKs: assigned_to → users.id, application_id → applications.id, candidate_id → candidates.id

## team_members
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

## teams
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: teams_manager_id_index
FKs: manager_id → users.id

## testimonials
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| TEXT | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |

## universities
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| numeric | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: universities_uid_unique (UNIQUE), universities_partner_status_index, universities_name_index, universities_uid_index
FKs: country_id → countries.id

## university_contacts
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: university_contacts_email_index, university_contacts_is_primary_index, university_contacts_university_id_index
FKs: university_id → universities.id

## users
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |

Indexes: users_email_unique (UNIQUE)
FKs: branch_id → branches.id, role_id → roles.id, team_id → teams.id

## visa_cases
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| INTEGER | 1 | NO | 0 |
| INTEGER | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| date | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: visa_cases_status_index, visa_cases_candidate_id_index, visa_cases_application_id_index
FKs: candidate_id → candidates.id, application_id → applications.id

## website_enquiries
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| varchar | 0 | NO | 0 |
| date | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| varchar | 1 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| INTEGER | 0 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: website_enquiries_type_index, website_enquiries_status_index, website_enquiries_email_index
FKs: converted_candidate_id → candidates.id, assigned_to → users.id

## website_pages
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| varchar | 1 | NO | 0 |
| varchar | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| TEXT | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: website_pages_slug_unique (UNIQUE), website_pages_slug_index

## workflow_templates
| Column | Type | Null | Default |
|---|---|---|---|
| INTEGER | 1 | NO | 1 |
| varchar | 1 | NO | 0 |
| TEXT | 0 | NO | 0 |
| tinyint(1) | 1 | NO | 0 |
| datetime | 0 | NO | 0 |
| datetime | 0 | NO | 0 |

Indexes: workflow_templates_active_index
