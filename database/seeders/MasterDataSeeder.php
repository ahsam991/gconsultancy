<?php

namespace Database\Seeders;

use App\Models\ApplicationStatus;
use App\Models\AutomationRule;
use App\Models\Banner;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Branch;
use App\Models\City;
use App\Models\CommissionRule;
use App\Models\Country;
use App\Models\CustomField;
use App\Models\DocumentType;
use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\Form;
use App\Models\Intake;
use App\Models\LeadSource;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Scholarship;
use App\Models\Setting;
use App\Models\SlaPolicy;
use App\Models\StatusTransition;
use App\Models\StudyLevel;
use App\Models\Subject;
use App\Models\Testimonial;
use App\Models\WebsitePage;
use App\Models\WorkflowTemplate;
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
            ['name' => 'Malaysia', 'iso_code' => 'MY', 'dial_code' => '+60'],
            ['name' => 'Finland', 'iso_code' => 'FI', 'dial_code' => '+358'],
            ['name' => 'France', 'iso_code' => 'FR', 'dial_code' => '+33'],
            ['name' => 'Netherlands', 'iso_code' => 'NL', 'dial_code' => '+31'],
            ['name' => 'Sweden', 'iso_code' => 'SE', 'dial_code' => '+46'],
            ['name' => 'Ireland', 'iso_code' => 'IE', 'dial_code' => '+353'],
            ['name' => 'Spain', 'iso_code' => 'ES', 'dial_code' => '+34'],
            ['name' => 'Italy', 'iso_code' => 'IT', 'dial_code' => '+39'],
            ['name' => 'United Arab Emirates', 'iso_code' => 'AE', 'dial_code' => '+971'],
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
            ['name' => 'Undergraduate', 'code' => 'UGD'],
            ['name' => 'Pre-Masters', 'code' => 'PRM'],
            ['name' => 'Masters', 'code' => 'MST'],
            ['name' => 'Diploma', 'code' => 'DIP'],
            ['name' => 'Bachelor', 'code' => 'BCH'],
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
            ['slug' => 'deposit-reminder', 'name' => 'Deposit Reminder', 'subject' => 'Deposit due for {{app_uid}}', 'body' => 'Hi {{name}}, your deposit of {{amount}} for {{app_uid}} is due on {{due_date}}.', 'variables' => 'name,app_uid,amount,due_date'],
            ['slug' => 'appointment-confirmation', 'name' => 'Appointment Confirmation', 'subject' => 'Appointment confirmed: {{date}}', 'body' => 'Hi {{name}}, your {{type}} appointment is confirmed for {{date}}. {{location}}', 'variables' => 'name,type,date,location'],
            ['slug' => 'task-reminder', 'name' => 'Task Reminder', 'subject' => 'Reminder: {{title}} due {{due}}', 'body' => 'Hi {{name}}, task {{title}} is due on {{due}}.', 'variables' => 'name,title,due'],
        ] as $tpl) {
            EmailTemplate::firstOrCreate(['slug' => $tpl['slug']], $tpl + ['active' => true]);
        }

        foreach ([
            ['key' => 'company_name', 'value' => 'Global Consultancy Education', 'group' => 'company'],
            ['key' => 'company_short', 'value' => 'G Consultancy', 'group' => 'company'],
            ['key' => 'company_legal', 'value' => 'GC CONSULTANCY LIMITED', 'group' => 'company'],
            ['key' => 'company_number', 'value' => '02773896', 'group' => 'company'],
            ['key' => 'company_tagline', 'value' => 'Your Dream to Study Abroad - Just Click & Achieve It', 'group' => 'company'],
            ['key' => 'company_sub_tagline', 'value' => 'Find The Right Path with Global Consultancy!', 'group' => 'company'],
            ['key' => 'company_usp', 'value' => '100% FREE Education Counselling and Application Processing', 'group' => 'company'],
            ['key' => 'company_bio_short', 'value' => 'Global Consultancy is the best student consultancy firm in UK with 100% proven VISA.', 'group' => 'company'],
            ['key' => 'address_london', 'value' => 'Suite 3, 2nd Floor, LMC Business Wing 38-44 Whitechapel Road, London, England, E1 1JX', 'group' => 'contact'],
            ['key' => 'address_khulna', 'value' => 'House No 52, Alingon House R#5, Road No-5, Nirala R/A, Khulna', 'group' => 'contact'],
            ['key' => 'phone_london', 'value' => '+447402993321', 'group' => 'contact'],
            ['key' => 'phone_london_local', 'value' => '07402 993321', 'group' => 'contact'],
            ['key' => 'phone_bd1', 'value' => '+8801744742686', 'group' => 'contact'],
            ['key' => 'phone_bd2', 'value' => '+8801935017746', 'group' => 'contact'],
            ['key' => 'email_primary', 'value' => 'info@gconsultancy.co.uk', 'group' => 'contact'],
            ['key' => 'website_primary', 'value' => 'https://www.gconsultancy.co.uk', 'group' => 'contact'],
            ['key' => 'service_areas', 'value' => 'Khulna, Bagerhat, Satkhira, Sonadanga, Khalishpur (Khulna Division, Bangladesh); London, UK', 'group' => 'contact'],
            ['key' => 'social_facebook', 'value' => 'https://www.facebook.com/GCEduLimited', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/global_consultancy_education', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/GlobalCons78186', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@GlobalConsultancy-xf5jh', 'group' => 'social'],
            ['key' => 'followers_count', 'value' => '19000', 'group' => 'social'],
            ['key' => 'reviews_count', 'value' => '2', 'group' => 'social'],
            ['key' => 'business_hours', 'value' => 'Always open', 'group' => 'company'],
            ['key' => 'destinations_list', 'value' => 'UK, USA, Canada, Australia, EU, Malaysia, Finland', 'group' => 'company'],
            ['key' => 'seo_keywords', 'value' => 'Global Consultancy Education, G Consultancy, study abroad from Bangladesh to UK, 100% FREE counselling, UK student visa success, Khulna education consultancy, London education consultancy Whitechapel Road, study in UK USA Canada Australia EU Malaysia Finland, British Council certified agency', 'group' => 'seo'],
            ['key' => 'british_council_certified', 'value' => '1', 'group' => 'company'],
            ['key' => 'site.name', 'value' => 'Global Consultancy Education', 'group' => 'general'],
            ['key' => 'site.email', 'value' => 'info@gconsultancy.co.uk', 'group' => 'general'],
            ['key' => 'site.phone', 'value' => '+447402993321', 'group' => 'general'],
            ['key' => 'commission.default_rate', 'value' => '12.5', 'group' => 'finance'],
            ['key' => 'application.uid_prefix', 'value' => 'APP-', 'group' => 'application'],
            ['key' => 'candidate.uid_prefix', 'value' => 'GC-', 'group' => 'candidate'],
        ] as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        foreach ([
            ['slug' => 'home', 'title' => 'Home', 'meta_title' => 'Global Consultancy Education | Study Abroad UK, USA, Canada, Australia', 'meta_description' => 'Your Dream to Study Abroad - Just Click & Achieve It. 100% FREE education counselling and application processing. Best student consultancy in UK with 100% proven VISA.'],
            ['slug' => 'about', 'title' => 'About Us', 'meta_title' => 'About Global Consultancy Education', 'meta_description' => 'GC Consultancy Limited (Company No 02773896) - the largest UK universities representative with offices in London and Khulna.'],
            ['slug' => 'services', 'title' => 'Our Services', 'meta_title' => 'Services: Free Counselling, Admission, Visa, Accommodation', 'meta_description' => 'Free counselling, admission services, university selection, document preparation, scholarships, visa guidance, accommodation and post-arrival support.'],
            ['slug' => 'contact', 'title' => 'Contact Us', 'meta_title' => 'Contact: London Whitechapel Road & Khulna Nirala', 'meta_description' => 'London: Suite 3, LMC Business Wing, 38-44 Whitechapel Road E1 1JX. Khulna: House 52, Nirala R/A. Call 07402 993321.'],
            ['slug' => 'privacy', 'title' => 'Privacy Policy', 'content' => 'Global Consultancy Education collects your name, contact details and academic documents solely to process your study-abroad application. We never sell your data. You may request export or deletion at any time via info@gconsultancy.co.uk.'],
            ['slug' => 'terms', 'title' => 'Terms of Service', 'content' => 'Counselling and application processing are 100% free. You are responsible for the accuracy of documents you provide. Visa decisions rest solely with the relevant embassy or high commission.'],
            ['slug' => 'visa-success-stories', 'title' => 'Visa Success Stories', 'meta_title' => 'UK Student Visa Success Stories', 'meta_description' => 'Real UK visa success stories from our students. 100% proven VISA record.'],
            ['slug' => 'study-destinations', 'title' => 'Study Destinations', 'meta_title' => 'Study in UK, USA, Canada, Australia, EU, Malaysia, Finland', 'meta_description' => 'Study destinations: UK, USA, Canada, Australia, EU, Malaysia, Finland and Middle East. 15+ countries.'],
        ] as $page) {
            WebsitePage::firstOrCreate(['slug' => $page['slug']], $page + ['content' => $page['title'].' content.', 'is_published' => true]);
        }

        BlogCategory::firstOrCreate(['slug' => 'visa-success-stories'], ['name' => 'Visa Success Stories', 'active' => true]);

        Banner::firstOrCreate(['title' => 'Home Hero - September Intake', 'location' => 'home_hero'], [
            'link' => '/apply-online', 'active' => true, 'sort_order' => 1,
        ]);

        Testimonial::firstOrCreate(['candidate_name' => 'Taniya Azad', 'university' => 'UK University'], [
            'country' => 'UK', 'country_flag' => 'GB', 'course' => 'Masters', 'rating' => 5,
            'content' => 'UK STUDENT VISA SUCCESS! Congratulations Taniya Azad! Another dream achieved with Global Consultancy - 100% FREE counselling and proven visa support.',
            'visa_success_story' => true, 'visa_type' => 'Student', 'is_featured' => true, 'is_published' => true,
        ]);

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

        // Branches (idempotent)
        foreach ([
            ['name' => 'Head Office', 'code' => 'HO', 'city' => 'Dhaka', 'country' => 'Bangladesh'],
            ['name' => 'Dhaka', 'code' => 'DHK', 'city' => 'Dhaka', 'country' => 'Bangladesh'],
            ['name' => 'London', 'code' => 'LDN', 'city' => 'London', 'country' => 'United Kingdom'],
        ] as $branch) {
            Branch::firstOrCreate(['code' => $branch['code']], $branch + ['active' => true]);
        }

        // SLA policies (idempotent)
        foreach ([
            ['name' => 'New Lead Response', 'event' => 'new_lead', 'hours' => 2],
            ['name' => 'New Application Response', 'event' => 'new_application', 'hours' => 24],
            ['name' => 'Missing Document Follow-up', 'event' => 'missing_document', 'hours' => 48],
            ['name' => 'Overdue Task Follow-up', 'event' => 'task_overdue', 'hours' => 24],
        ] as $policy) {
            SlaPolicy::firstOrCreate(['event' => $policy['event']], $policy + ['active' => true]);
        }

        // Automation rule example (idempotent)
        AutomationRule::firstOrCreate(
            ['name' => 'Notify staff on document rejection'],
            [
                'trigger_event' => 'document_rejected',
                'trigger_status' => null,
                'action_type' => 'notify_staff',
                'action_config' => ['notify_staff' => true, 'create_task' => true, 'task_title' => 'Follow up rejected document'],
                'active' => true,
            ]
        );

        // Workflow template + status transitions (idempotent)
        $template = WorkflowTemplate::firstOrCreate(
            ['name' => 'Default Application Pipeline'],
            ['description' => 'Default end-to-end application pipeline from draft to enrolment.', 'active' => true]
        );
        $pipeline = ['DRAFT', 'PROFILE_CHECK', 'DOCUMENT_PENDING', 'READY_TO_APPLY', 'SUBMITTED', 'ACKNOWLEDGED', 'UNDER_REVIEW', 'CONDITIONAL_OFFER', 'UNCONDITIONAL_OFFER', 'DEPOSIT_REQUIRED', 'DEPOSIT_PAID', 'CAS_REQUESTED', 'CAS_ISSUED', 'VISA_PREPARATION', 'VISA_APPLIED', 'VISA_APPROVED', 'ENROLLED'];
        foreach (array_keys(array_slice($pipeline, 0, -1)) as $i) {
            StatusTransition::firstOrCreate(
                ['workflow_template_id' => $template->id, 'from_status' => $pipeline[$i], 'to_status' => $pipeline[$i + 1]],
                ['required_permission' => null, 'automation' => null, 'active' => true]
            );
        }

        // Menus + items (idempotent)
        $header = Menu::firstOrCreate(['name' => 'Main Header'], ['location' => 'header', 'active' => true]);
        $footer = Menu::firstOrCreate(['name' => 'Main Footer'], ['location' => 'footer', 'active' => true]);
        foreach ([
            ['menu_id' => $header->id, 'label' => 'Home', 'url' => '/', 'sort_order' => 1],
            ['menu_id' => $header->id, 'label' => 'About Us', 'url' => '/about', 'sort_order' => 2],
            ['menu_id' => $header->id, 'label' => 'Contact', 'url' => '/contact', 'sort_order' => 3],
        ] as $item) {
            MenuItem::firstOrCreate(['menu_id' => $item['menu_id'], 'label' => $item['label']], $item + ['parent_id' => null, 'active' => true]);
        }
        foreach ([
            ['menu_id' => $footer->id, 'label' => 'Privacy Policy', 'url' => '/p/privacy', 'sort_order' => 1],
            ['menu_id' => $footer->id, 'label' => 'Terms', 'url' => '/p/terms', 'sort_order' => 2],
        ] as $item) {
            MenuItem::firstOrCreate(['menu_id' => $item['menu_id'], 'label' => $item['label']], $item + ['parent_id' => null, 'active' => true]);
        }

        // Blog categories + posts (idempotent)
        $cat1 = BlogCategory::firstOrCreate(['slug' => 'study-abroad'], ['name' => 'Study Abroad', 'active' => true]);
        $cat2 = BlogCategory::firstOrCreate(['slug' => 'visa-guides'], ['name' => 'Visa Guides', 'active' => true]);
        if (!BlogPost::where('slug', 'uk-study-guide-2026')->exists()) {
            BlogPost::create([
                'title' => 'UK Study Guide 2026',
                'slug' => 'uk-study-guide-2026',
                'blog_category_id' => $cat1->id,
                'excerpt' => 'Everything you need to know to study in the UK in 2026.',
                'content' => 'Full UK study guide content.',
                'author_id' => null,
                'status' => 'published',
                'published_at' => now(),
            ]);
        }
        if (!BlogPost::where('slug', 'uk-student-visa-checklist')->exists()) {
            BlogPost::create([
                'title' => 'UK Student Visa Checklist',
                'slug' => 'uk-student-visa-checklist',
                'blog_category_id' => $cat2->id,
                'excerpt' => 'Documents and steps for a successful UK student visa.',
                'content' => 'Visa checklist content.',
                'author_id' => null,
                'status' => 'published',
                'published_at' => now(),
            ]);
        }

        // Banner (idempotent)
        \App\Models\Banner::firstOrCreate(
            ['title' => 'Home Hero'],
            ['image' => null, 'link' => null, 'location' => 'home_hero', 'active' => true, 'sort_order' => 0]
        );

        // Counselling form (idempotent)
        Form::firstOrCreate(
            ['slug' => 'counselling'],
            [
                'name' => 'Counselling Form',
                'fields' => [
                    ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required' => true, 'options' => null],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'options' => null],
                    ['name' => 'phone', 'label' => 'Phone', 'type' => 'text', 'required' => false, 'options' => null],
                    ['name' => 'destination', 'label' => 'Preferred Destination', 'type' => 'select', 'required' => false, 'options' => ['UK', 'USA', 'Canada', 'Australia']],
                    ['name' => 'level', 'label' => 'Study Level', 'type' => 'select', 'required' => false, 'options' => ['Foundation', 'Bachelor', 'Master', 'PhD']],
                ],
                'active' => true,
            ]
        );

        // Scholarships (idempotent)
        Scholarship::firstOrCreate(
            ['title' => 'Merit Scholarship 2026'],
            ['amount' => 2000, 'amount_type' => 'fixed', 'criteria' => 'Minimum GPA 3.5', 'deadline' => null, 'university_id' => null, 'course_id' => null, 'active' => true]
        );
        Scholarship::firstOrCreate(
            ['title' => 'Early Bird 10% Discount'],
            ['amount' => 10, 'amount_type' => 'percentage', 'criteria' => 'Apply before deadline', 'deadline' => null, 'university_id' => null, 'course_id' => null, 'active' => true]
        );

        // Custom fields (idempotent)
        CustomField::firstOrCreate(
            ['module' => 'candidate', 'name' => 'emergency_contact_name'],
            ['label' => 'Emergency Contact Name', 'field_type' => 'text', 'options' => null, 'is_required' => false, 'sort_order' => 1, 'active' => true]
        );
        CustomField::firstOrCreate(
            ['module' => 'application', 'name' => 'priority_note'],
            ['label' => 'Priority Note', 'field_type' => 'textarea', 'options' => null, 'is_required' => false, 'sort_order' => 2, 'active' => true]
        );
    }
}
