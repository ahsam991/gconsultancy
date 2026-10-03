<?php

namespace Database\Seeders;

use App\Models\ApplicationStatus;
use App\Models\City;
use App\Models\CommissionRule;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\Intake;
use App\Models\LeadSource;
use App\Models\Setting;
use App\Models\StudyLevel;
use App\Models\Subject;
use App\Models\WebsitePage;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'United Kingdom', 'iso_code' => 'GB', 'dial_code' => '+44'],
            ['name' => 'United States', 'iso_code' => 'US', 'dial_code' => '+1'],
            ['name' => 'Canada', 'iso_code' => 'CA', 'dial_code' => '+1'],
            ['name' => 'Australia', 'iso_code' => 'AU', 'dial_code' => '+61'],
            ['name' => 'Germany', 'iso_code' => 'DE', 'dial_code' => '+49'],
            ['name' => 'Bangladesh', 'iso_code' => 'BD', 'dial_code' => '+880'],
            ['name' => 'India', 'iso_code' => 'IN', 'dial_code' => '+91'],
            ['name' => 'Nigeria', 'iso_code' => 'NG', 'dial_code' => '+234'],
            ['name' => 'Pakistan', 'iso_code' => 'PK', 'dial_code' => '+92'],
            ['name' => 'Nepal', 'iso_code' => 'NP', 'dial_code' => '+977'],
        ];
        foreach ($countries as $c) {
            Country::firstOrCreate(['iso_code' => $c['iso_code']], $c + ['active' => true]);
        }

        $uk = Country::where('iso_code', 'GB')->first();
        $cities = [
            ['country_iso' => 'GB', 'name' => 'London'],
            ['country_iso' => 'GB', 'name' => 'Manchester'],
            ['country_iso' => 'GB', 'name' => 'Birmingham'],
            ['country_iso' => 'BD', 'name' => 'Dhaka'],
            ['country_iso' => 'BD', 'name' => 'Chittagong'],
            ['country_iso' => 'IN', 'name' => 'Delhi'],
            ['country_iso' => 'NG', 'name' => 'Lagos'],
        ];
        foreach ($cities as $city) {
            $country = Country::where('iso_code', $city['country_iso'])->first();
            if ($country) {
                City::firstOrCreate(['country_id' => $country->id, 'name' => $city['name']], ['active' => true]);
            }
        }

        foreach ([
            ['name' => 'Foundation', 'code' => 'FDN'],
            ['name' => 'Diploma', 'code' => 'DIP'],
            ['name' => 'Bachelor', 'code' => 'BCH'],
            ['name' => 'Master', 'code' => 'MST'],
            ['name' => 'PhD', 'code' => 'PHD'],
        ] as $level) {
            StudyLevel::firstOrCreate(['code' => $level['code']], $level + ['active' => true]);
        }

        foreach ([
            ['name' => 'Business', 'code' => 'BUS'],
            ['name' => 'Computer Science', 'code' => 'CS'],
            ['name' => 'Engineering', 'code' => 'ENG'],
            ['name' => 'Medicine', 'code' => 'MED'],
            ['name' => 'Law', 'code' => 'LAW'],
            ['name' => 'Arts & Humanities', 'code' => 'ART'],
            ['name' => 'Social Science', 'code' => 'SOC'],
        ] as $subject) {
            Subject::firstOrCreate(['name' => $subject['name']], $subject + ['active' => true]);
        }

        foreach (['Website', 'Facebook', 'Instagram', 'Referral', 'Walk-in', 'Phone', 'Event'] as $source) {
            LeadSource::firstOrCreate(['name' => $source], ['active' => true]);
        }

        foreach ([
            ['name' => 'Passport', 'category' => 'Identity', 'required_default' => true],
            ['name' => 'National ID', 'category' => 'Identity', 'required_default' => false],
            ['name' => 'SSC Certificate', 'category' => 'Academic', 'required_default' => true],
            ['name' => 'HSC Certificate', 'category' => 'Academic', 'required_default' => true],
            ['name' => 'Bachelor Transcript', 'category' => 'Academic', 'required_default' => false],
            ['name' => 'IELTS Certificate', 'category' => 'English', 'required_default' => false],
            ['name' => 'SOP', 'category' => 'Application', 'required_default' => true],
            ['name' => 'Bank Statement', 'category' => 'Financial', 'required_default' => false],
            ['name' => 'CAS Letter', 'category' => 'Visa', 'required_default' => false],
            ['name' => 'Other', 'category' => 'Other', 'required_default' => false],
        ] as $dt) {
            DocumentType::firstOrCreate(['name' => $dt['name']], $dt + ['active' => true]);
        }

        foreach ([
            ['name' => 'September 2026', 'year' => 2026, 'month' => 9, 'start_date' => '2026-09-01', 'deadline' => '2026-07-31'],
            ['name' => 'January 2027', 'year' => 2027, 'month' => 1, 'start_date' => '2027-01-11', 'deadline' => '2026-11-30'],
            ['name' => 'May 2027', 'year' => 2027, 'month' => 5, 'start_date' => '2027-05-03', 'deadline' => '2027-03-31'],
        ] as $intake) {
            Intake::firstOrCreate(['name' => $intake['name']], $intake + ['active' => true]);
        }

        CommissionRule::firstOrCreate(['university_id' => null, 'study_level' => 'Bachelor'], [
            'rate_percent' => 12.50, 'currency' => 'GBP', 'active' => true,
        ]);
        CommissionRule::firstOrCreate(['university_id' => null, 'study_level' => 'Master'], [
            'rate_percent' => 15.00, 'currency' => 'GBP', 'active' => true,
        ]);

        foreach ([
            ['slug' => 'welcome', 'name' => 'Welcome Email', 'subject' => 'Welcome to Global Consultancy, {{name}}', 'body' => 'Hi {{name}}, welcome aboard. Your candidate ID is {{uid}}.', 'variables' => 'name,uid'],
            ['slug' => 'offer-received', 'name' => 'Offer Received', 'subject' => 'Your offer from {{university}}', 'body' => 'Hi {{name}}, you received an offer from {{university}} for {{course}}.', 'variables' => 'name,university,course'],
            ['slug' => 'cas-issued', 'name' => 'CAS Issued', 'subject' => 'Your CAS has been issued', 'body' => 'Hi {{name}}, your CAS number is {{cas_number}}.', 'variables' => 'name,cas_number'],
            ['slug' => 'visa-approved', 'name' => 'Visa Approved', 'subject' => 'Visa approved', 'body' => 'Congratulations {{name}}, your visa was approved.', 'variables' => 'name'],
            ['slug' => 'application-status', 'name' => 'Application Status Changed', 'subject' => 'Your application {{app_uid}} is now {{status}}', 'body' => 'Hi {{name}}, your application {{app_uid}} for {{course}} at {{university}} moved to {{status}}.', 'variables' => 'name,app_uid,status,course,university'],
            ['slug' => 'lead-assigned', 'name' => 'Lead Assigned', 'subject' => 'New lead assigned: {{name}}', 'body' => 'A new lead {{name}} ({{email}}) was assigned to you.', 'variables' => 'name,email'],
            ['slug' => 'document-verified', 'name' => 'Document Verified', 'subject' => 'Your document was verified', 'body' => 'Hi {{name}}, your document {{document}} has been verified.', 'variables' => 'name,document'],
        ] as $tpl) {
            EmailTemplate::firstOrCreate(['slug' => $tpl['slug']], $tpl + ['active' => true]);
        }

        foreach ([
            ['key' => 'site.name', 'value' => 'Global Consultancy', 'group' => 'general'],
            ['key' => 'site.email', 'value' => 'info@globalconsultancy.com', 'group' => 'general'],
            ['key' => 'site.phone', 'value' => '+880-000-000000', 'group' => 'general'],
            ['key' => 'commission.default_rate', 'value' => '12.5', 'group' => 'finance'],
            ['key' => 'application.uid_prefix', 'value' => 'APP-', 'group' => 'application'],
            ['key' => 'candidate.uid_prefix', 'value' => 'GC-', 'group' => 'candidate'],
        ] as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        foreach ([
            ['slug' => 'home', 'title' => 'Home'],
            ['slug' => 'about', 'title' => 'About Us'],
            ['slug' => 'services', 'title' => 'Our Services'],
            ['slug' => 'contact', 'title' => 'Contact Us'],
            ['slug' => 'privacy', 'title' => 'Privacy Policy'],
        ] as $page) {
            WebsitePage::firstOrCreate(['slug' => $page['slug']], $page + ['content' => $page['title'].' content.', 'is_published' => true]);
        }

        foreach ([
            ['question' => 'How do I apply for a UK university?', 'answer' => 'Contact our counsellors, shortlist a course, submit documents and we handle the application.', 'category' => 'General', 'sort_order' => 1],
            ['question' => 'What is CAS?', 'answer' => 'CAS is the Confirmation of Acceptance for Studies issued by the university for your visa.', 'category' => 'Visa', 'sort_order' => 2],
            ['question' => 'Do I need IELTS?', 'answer' => 'Most universities require IELTS or equivalent. Some accept MOI.', 'category' => 'English', 'sort_order' => 3],
        ] as $faq) {
            Faq::firstOrCreate(['question' => $faq['question']], $faq + ['is_published' => true]);
        }

        $order = 0;
        foreach ([
            ['DRAFT', 'Draft', 'secondary', false],
            ['PROFILE_CHECK', 'Profile Check', 'info', false],
            ['DOCUMENT_PENDING', 'Document Pending', 'warning', false],
            ['READY_TO_APPLY', 'Ready to Apply', 'info', false],
            ['SUBMITTED', 'Submitted', 'info', false],
            ['ACKNOWLEDGED', 'Acknowledged', 'info', false],
            ['UNDER_REVIEW', 'Under Review', 'info', false],
            ['INTERVIEW_REQUIRED', 'Interview Required', 'warning', false],
            ['CONDITIONAL_OFFER', 'Conditional Offer', 'warning', false],
            ['UNCONDITIONAL_OFFER', 'Unconditional Offer', 'success', false],
            ['DEPOSIT_REQUIRED', 'Deposit Required', 'warning', false],
            ['DEPOSIT_PAID', 'Deposit Paid', 'success', false],
            ['CAS_REQUESTED', 'CAS Requested', 'info', false],
            ['CAS_ISSUED', 'CAS Issued', 'success', false],
            ['VISA_PREPARATION', 'Visa Preparation', 'info', false],
            ['VISA_APPLIED', 'Visa Applied', 'info', false],
            ['VISA_APPROVED', 'Visa Approved', 'success', true],
            ['VISA_REFUSED', 'Visa Refused', 'danger', true],
            ['ENROLLED', 'Enrolled', 'success', true],
            ['WITHDRAWN', 'Withdrawn', 'dark', true],
            ['REJECTED', 'Rejected', 'danger', true],
            ['CLOSED', 'Closed', 'dark', true],
        ] as [$code, $label, $color, $terminal]) {
            ApplicationStatus::firstOrCreate(['code' => $code], [
                'label' => $label, 'color' => $color, 'sort_order' => ++$order, 'terminal' => $terminal, 'active' => true,
            ]);
        }
    }
}
