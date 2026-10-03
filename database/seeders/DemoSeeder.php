<?php

namespace Database\Seeders;

use App\Models\AcademicQualification;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\Appointment;
use App\Models\Campus;
use App\Models\Candidate;
use App\Models\CandidateDocument;
use App\Models\CasRecord;
use App\Models\Commission;
use App\Models\Communication;
use App\Models\Country;
use App\Models\Course;
use App\Models\Deposit;
use App\Models\DocumentType;
use App\Models\EnglishTest;
use App\Models\Enrolment;
use App\Models\Intake;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Role;
use App\Models\StudyLevel;
use App\Models\Subject;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\University;
use App\Models\User;
use App\Models\VisaCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Candidate::count() >= 10 && University::count() >= 6) {
            return;
        }

        $roles = [
            'admin' => Role::where('name', 'admin')->first(),
            'manager' => Role::where('name', 'manager')->first(),
            'staff' => Role::where('name', 'staff')->first(),
            'candidate' => Role::where('name', 'candidate')->first(),
        ];

        $users = [];
        foreach ([
            ['name' => 'Admin User', 'email' => 'admin@globalconsultancy.com', 'role' => 'admin'],
            ['name' => 'Manager User', 'email' => 'manager@globalconsultancy.com', 'role' => 'manager'],
            ['name' => 'Staff User', 'email' => 'staff@globalconsultancy.com', 'role' => 'staff'],
            ['name' => 'Candidate User', 'email' => 'candidate@globalconsultancy.com', 'role' => 'candidate'],
        ] as $u) {
            $user = User::firstOrCreate(['email' => $u['email']], [
                'name' => $u['name'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            if ($roles[$u['role']]) {
                $user->role_id = $roles[$u['role']]->id;
                $user->save();
            }
            if ($u['role'] !== 'candidate' && Hash::needsRehash($user->password)) {
                $user->password = Hash::make('password123');
                $user->save();
            }
            $users[$u['role']] = $user;
        }

        $uk = Country::where('iso_code', 'GB')->first();
        $bd = Country::where('iso_code', 'BD')->first();

        $uniData = [
            ['uid' => 'UNI-000001', 'name' => 'University of London', 'city' => 'London', 'commission_rate' => 12.5],
            ['uid' => 'UNI-000002', 'name' => 'Manchester Metropolitan', 'city' => 'Manchester', 'commission_rate' => 15],
            ['uid' => 'UNI-000003', 'name' => 'Birmingham City University', 'city' => 'Birmingham', 'commission_rate' => 10],
            ['uid' => 'UNI-000004', 'name' => 'Leeds Beckett University', 'city' => 'Leeds', 'commission_rate' => 12],
            ['uid' => 'UNI-000005', 'name' => 'Coventry University', 'city' => 'Coventry', 'commission_rate' => 14],
            ['uid' => 'UNI-000006', 'name' => 'University of Greenwich', 'city' => 'London', 'commission_rate' => 13],
        ];
        $universities = [];
        foreach ($uniData as $ud) {
            $universities[] = University::firstOrCreate(['uid' => $ud['uid']], $ud + [
                'country_id' => $uk?->id, 'partner_status' => 'Partner', 'active' => true,
            ]);
        }

        foreach ($universities as $uni) {
            Campus::firstOrCreate(['university_id' => $uni->id, 'name' => 'Main Campus'], [
                'city' => $uni->city, 'active' => true,
            ]);
        }

        $levels = StudyLevel::all();
        $subjects = Subject::all();
        $intakes = Intake::all();
        $campuses = Campus::all();

        $courseNames = [
            'BSc Business Management', 'MSc Computer Science', 'BEng Mechanical Engineering',
            'LLB Law', 'MBA Global Business', 'BSc Nursing', 'MSc Data Science',
            'BA Accounting and Finance', 'MSc Cyber Security', 'BSc Civil Engineering',
            'MA International Relations', 'MSc Public Health', 'BSc Psychology',
            'MSc Artificial Intelligence', 'BA Marketing Management',
        ];
        $courses = [];
        $i = 0;
        foreach ($courseNames as $cn) {
            $i++;
            $uid = 'CRS-'.str_pad((string) $i, 6, '0', STR_PAD_LEFT);
            $courses[] = Course::firstOrCreate(['uid' => $uid], [
                'university_id' => $universities[$i % count($universities)]->id,
                'campus_id' => $campuses->first()?->id,
                'subject_id' => $subjects->isNotEmpty() ? $subjects[$i % $subjects->count()]->id : null,
                'study_level_id' => $levels->isNotEmpty() ? $levels[$i % $levels->count()]->id : null,
                'name' => $cn,
                'study_mode' => 'Full-time',
                'duration_months' => 12,
                'tuition_fee' => 14000 + ($i * 250),
                'currency' => 'GBP',
                'deposit_amount' => 3000,
                'ielts_required' => 6.5,
                'intake_months' => 'Jan,May,Sep',
                'active' => true,
                'featured' => $i <= 3,
            ]);
        }

        $docTypes = DocumentType::all();
        $source = LeadSource::first();

        for ($c = 1; $c <= 10; $c++) {
            $uid = 'GC-'.str_pad((string) $c, 6, '0', STR_PAD_LEFT);
            $candidate = Candidate::firstOrCreate(['uid' => $uid], [
                'first_name' => 'Candidate'.$c,
                'last_name' => 'Demo',
                'email' => "candidate{$c}@example.com",
                'phone' => '+88010000000'.str_pad((string) $c, 2, '0', STR_PAD_LEFT),
                'country_id' => $bd?->id,
                'preferred_destination' => 'UK',
                'preferred_level' => 'Master',
                'assigned_staff_id' => $users['staff']->id ?? null,
                'assigned_manager_id' => $users['manager']->id ?? null,
                'status' => 'COUNSELLING',
                'profile_completion' => 80,
            ]);

            AcademicQualification::firstOrCreate(
                ['candidate_id' => $candidate->id, 'level' => 'HSC', 'institution' => 'Dhaka College'],
                ['passing_year' => 2020, 'result' => 'GPA 4.5', 'country' => 'Bangladesh']
            );
            AcademicQualification::firstOrCreate(
                ['candidate_id' => $candidate->id, 'level' => 'Bachelor', 'institution' => 'National University'],
                ['passing_year' => 2024, 'result' => 'CGPA 3.4', 'country' => 'Bangladesh']
            );
            EnglishTest::firstOrCreate(
                ['candidate_id' => $candidate->id, 'test_type' => 'IELTS'],
                ['overall' => 6.5, 'listening' => 6.5, 'reading' => 6.0, 'writing' => 6.0, 'speaking' => 6.5, 'test_date' => '2026-01-15']
            );

            for ($a = 0; $a < 2; $a++) {
                $appUid = 'APP-'.str_pad((string) (($c - 1) * 2 + $a + 1), 6, '0', STR_PAD_LEFT);
                $course = $courses[(($c - 1) * 2 + $a) % count($courses)];
                $app = Application::firstOrCreate(['uid' => $appUid], [
                    'candidate_id' => $candidate->id,
                    'university_id' => $course->university_id,
                    'campus_id' => $course->campus_id,
                    'course_id' => $course->id,
                    'intake_id' => $intakes->isNotEmpty() ? $intakes->first()->id : null,
                    'assigned_staff_id' => $users['staff']->id ?? null,
                    'status' => $a === 0 ? 'SUBMITTED' : 'DRAFT',
                    'priority' => 'Normal',
                    'tuition_fee' => $course->tuition_fee,
                    'currency' => 'GBP',
                ]);

                ApplicationStatusHistory::firstOrCreate([
                    'application_id' => $app->id, 'previous_status' => null, 'new_status' => 'DRAFT',
                ], ['changed_by' => $users['staff']->id ?? $users['admin']->id, 'note' => 'Created']);

                if ($docTypes->isNotEmpty()) {
                    CandidateDocument::firstOrCreate(
                        ['candidate_id' => $candidate->id, 'application_id' => $app->id, 'document_type_id' => $docTypes->first()->id],
                        ['original_filename' => 'passport.pdf', 'stored_path' => 'docs/passport.pdf', 'verification_status' => 'UPLOADED', 'uploaded_by' => $users['staff']->id ?? $users['admin']->id]
                    );
                }

                Task::firstOrCreate(
                    ['title' => "Follow up candidate {$candidate->uid} - app {$app->uid}"],
                    ['candidate_id' => $candidate->id, 'application_id' => $app->id, 'assigned_to' => $users['staff']->id ?? $users['admin']->id, 'status' => 'NEW', 'priority' => 'Medium']
                );
            }

            Appointment::firstOrCreate(
                ['candidate_id' => $candidate->id, 'staff_id' => $users['staff']->id ?? $users['admin']->id, 'appointment_date' => now()->addDays($c)->setHour(10)->setMinute(0)->setSecond(0)],
                ['type' => 'Counselling', 'status' => 'SCHEDULED']
            );

            Communication::firstOrCreate(
                ['candidate_id' => $candidate->id, 'user_id' => $users['staff']->id ?? $users['admin']->id, 'subject' => 'Welcome call'],
                ['channel' => 'Call', 'body' => 'Initial counselling call completed.', 'direction' => 'Outbound']
            );
        }

        $sampleApps = Application::take(3)->get();
        $idx = 0;
        foreach ($sampleApps as $app) {
            $idx++;
            Offer::firstOrCreate(['application_id' => $app->id], [
                'type' => $idx % 2 ? 'Conditional' : 'Unconditional',
                'offer_date' => now()->subDays(10), 'status' => 'RECEIVED', 'deposit_amount' => 3000,
            ]);
            Deposit::firstOrCreate(['application_id' => $app->id], [
                'required_amount' => 3000, 'paid_amount' => $idx === 1 ? 3000 : 0,
                'status' => $idx === 1 ? 'PAID' : 'PENDING',
            ]);
            CasRecord::firstOrCreate(['application_id' => $app->id], [
                'cas_number' => $idx === 1 ? 'CAS-2026-00000'.$idx : null,
                'status' => $idx === 1 ? 'ISSUED' : 'NOT_REQUESTED',
            ]);
            VisaCase::firstOrCreate(['application_id' => $app->id], [
                'candidate_id' => $app->candidate_id, 'destination' => 'UK', 'status' => 'NOT_STARTED',
            ]);
            Enrolment::firstOrCreate(['application_id' => $app->id], [
                'candidate_id' => $app->candidate_id, 'status' => 'PENDING',
            ]);
            Commission::firstOrCreate(['application_id' => $app->id], [
                'candidate_id' => $app->candidate_id, 'university_id' => $app->university_id,
                'amount' => 1800, 'currency' => 'GBP', 'rate_percent' => 12.5, 'status' => 'PENDING',
            ]);
        }

        $commissions = Commission::take(2)->get();
        $invN = 1;
        foreach ($commissions as $comm) {
            $invNo = 'INV-'.str_pad((string) $invN, 6, '0', STR_PAD_LEFT);
            $invoice = Invoice::firstOrCreate(['invoice_number' => $invNo], [
                'university_id' => $comm->university_id,
                'commission_id' => $comm->id,
                'candidate_id' => $comm->candidate_id,
                'application_id' => $comm->application_id,
                'issue_date' => now()->toDateString(),
                'subtotal' => $comm->amount, 'total' => $comm->amount, 'status' => 'SENT',
            ]);
            InvoiceItem::firstOrCreate(
                ['invoice_id' => $invoice->id, 'description' => 'Commission for '.$comm->application_id],
                ['quantity' => 1, 'unit_price' => $comm->amount, 'total' => $comm->amount]
            );
            Payment::firstOrCreate(
                ['invoice_id' => $invoice->id, 'transaction_ref' => 'TXN-00000'.$invN],
                ['amount' => $comm->amount, 'payment_date' => now()->toDateString(), 'method' => 'Bank Transfer', 'received_by' => $users['admin']->id]
            );
            $invN++;
        }

        foreach ([
            ['first_name' => 'Lead', 'last_name' => 'One', 'email' => 'lead1@example.com'],
            ['first_name' => 'Lead', 'last_name' => 'Two', 'email' => 'lead2@example.com'],
            ['first_name' => 'Lead', 'last_name' => 'Three', 'email' => 'lead3@example.com'],
        ] as $ld) {
            Lead::firstOrCreate(['email' => $ld['email']], $ld + [
                'phone' => '+8801999999999', 'source_id' => $source?->id, 'status' => 'NEW',
                'assigned_to' => $users['staff']->id ?? null,
            ]);
        }

        Testimonial::firstOrCreate(['candidate_name' => 'Rahim Uddin'], [
            'country' => 'Bangladesh', 'university' => 'University of London', 'rating' => 5,
            'content' => 'Great support from counselling to visa.', 'is_featured' => true, 'is_published' => true,
        ]);
        TeamMember::firstOrCreate(['email' => 'counsellor@globalconsultancy.com'], [
            'name' => 'Senior Counsellor', 'role' => 'Counsellor', 'sort_order' => 1, 'active' => true,
        ]);
    }
}
