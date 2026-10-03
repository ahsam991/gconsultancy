<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Study destinations
    |--------------------------------------------------------------------------
    */
    'destinations' => [
        'uk' => ['name' => 'United Kingdom', 'slug' => 'uk', 'flag' => '🇬🇧'],
        'usa' => ['name' => 'United States', 'slug' => 'usa', 'flag' => '🇺🇸'],
        'canada' => ['name' => 'Canada', 'slug' => 'canada', 'flag' => '🇨🇦'],
        'australia' => ['name' => 'Australia', 'slug' => 'australia', 'flag' => '🇦🇺'],
        'europe' => ['name' => 'Europe', 'slug' => 'europe', 'flag' => '🇪🇺'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Candidate (lead) statuses
    |--------------------------------------------------------------------------
    */
    'candidate_statuses' => [
        'new', 'contacted', 'counselled', 'applied',
        'offer_received', 'deposit_paid', 'cas_issued',
        'visa_lodged', 'visa_granted', 'enrolled',
        'deferred', 'withdrawn', 'rejected',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application statuses + legal transitions.
    | Backend Application model should enforce these; tests assert this map.
    |--------------------------------------------------------------------------
    */
    'application_statuses' => [
        'draft', 'submitted', 'under_review', 'offer_conditional',
        'offer_unconditional', 'deposit_paid', 'cas_issued',
        'visa_lodged', 'visa_granted', 'visa_refused',
        'enrolled', 'deferred', 'withdrawn', 'rejected',
    ],

    'application_status_transitions' => [
        'draft' => ['submitted', 'withdrawn'],
        'submitted' => ['under_review', 'withdrawn'],
        'under_review' => ['offer_conditional', 'offer_unconditional', 'rejected', 'withdrawn'],
        'offer_conditional' => ['offer_unconditional', 'deferred', 'withdrawn'],
        'offer_unconditional' => ['deposit_paid', 'deferred', 'withdrawn'],
        'deposit_paid' => ['cas_issued', 'withdrawn'],
        'cas_issued' => ['visa_lodged', 'deferred', 'withdrawn'],
        'visa_lodged' => ['visa_granted', 'visa_refused'],
        'visa_refused' => ['visa_lodged', 'withdrawn'],
        'visa_granted' => ['enrolled', 'deferred'],
        'deferred' => ['submitted'],
        'enrolled' => [],
        'withdrawn' => [],
        'rejected' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Document categories (required checklist)
    |--------------------------------------------------------------------------
    */
    'document_categories' => [
        'passport', 'academic_transcript', 'academic_certificate',
        'english_test', 'sop', 'lor', 'cv', 'financial_proof',
        'cas_letter', 'visa_grant', 'other',
    ],

    'required_documents' => [
        'passport', 'academic_transcript', 'academic_certificate',
        'english_test', 'sop', 'financial_proof',
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission defaults
    |--------------------------------------------------------------------------
    */
    'commission' => [
        'default_rate_percent' => 12.5,
        'currency' => 'GBP',
    ],

    /*
    |--------------------------------------------------------------------------
    | Journey tracker steps (portal progress)
    |--------------------------------------------------------------------------
    */
    'journey_steps' => [
        'profile', 'application', 'offer', 'deposit', 'cas', 'visa', 'enrolment',
    ],
];
