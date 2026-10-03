<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\JourneyController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UniversityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/study/{destination}', [PublicController::class, 'studyDestination'])->name('study.destination');
foreach (['uk' => 'UK', 'usa' => 'USA', 'canada' => 'Canada', 'australia' => 'Australia', 'europe' => 'Europe'] as $slug => $label) {
    Route::get('/study-'.$slug, fn () => redirect()->route('study.destination', $slug, 301))->name('study.'.$slug);
}
Route::get('/universities', [PublicController::class, 'universities'])->name('public.universities');
Route::get('/courses', [PublicController::class, 'courses'])->name('public.courses');
Route::get('/course-finder', [CourseController::class, 'finder'])->name('course-finder');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'contactSubmit'])->middleware('throttle:20,1')->name('contact.submit');
Route::get('/book-appointment', [PublicController::class, 'appointment'])->name('appointment.book');
Route::post('/book-appointment', [PublicController::class, 'appointmentStore'])->middleware('throttle:20,1')->name('appointment.store');
Route::get('/apply-online', [PublicController::class, 'apply'])->name('apply');
Route::post('/apply-online', [PublicController::class, 'applyStore'])->middleware('throttle:20,1')->name('apply.store');
Route::post('/enquiries', [PublicController::class, 'enquirySubmit'])->middleware('throttle:20,1')->name('enquiries.store');
Route::post('/course-apply', [PublicController::class, 'courseApply'])->middleware('throttle:20,1')->name('course.apply');
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
Route::get('/compare', [\App\Http\Controllers\EngagementController::class, 'compare'])->name('engagement.compare');
Route::get('/eligibility-check', [\App\Http\Controllers\EngagementController::class, 'eligibility'])->name('engagement.eligibility');
Route::post('/eligibility-check', [\App\Http\Controllers\EngagementController::class, 'eligibilityCheck'])->name('engagement.eligibility.check');
Route::get('/availability/slots', [\App\Http\Controllers\AvailabilityController::class, 'slots'])->middleware('throttle:60,1')->name('availability.slots');

/*
|--------------------------------------------------------------------------
| Auth (keep existing URIs)
|--------------------------------------------------------------------------
*/
Route::get('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/login', [AuthController::class, 'authenticate'])->middleware('throttle:10,1')->name('login.attempt');
Route::get('/auth/register', [AuthController::class, 'register'])->name('register');
Route::post('/auth/register', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('register.store');
Route::get('/auth/2fa', [AuthController::class, 'twoFactorChallenge'])->name('2fa.challenge');
Route::post('/auth/2fa', [AuthController::class, 'twoFactorVerify'])->middleware('throttle:10,1')->name('2fa.verify');
Route::middleware(['auth'])->group(function () {
    Route::get('/auth/2fa/setup', [AuthController::class, 'twoFactorSetup'])->name('2fa.setup');
    Route::post('/auth/2fa/confirm', [AuthController::class, 'twoFactorConfirm'])->name('2fa.confirm');
    Route::post('/auth/2fa/disable', [AuthController::class, 'twoFactorDisable'])->name('2fa.disable');
});
Route::get('/auth/forgot-password', [AuthController::class, 'forgetPassword'])->name('password.request');
Route::post('/auth/forgot-password', [AuthController::class, 'forgetPassword'])->name('password.email');
Route::get('/auth/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard (role-aware redirect inside controller)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('crm')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| CRM (auth + per-action policy checks in controllers)
| Prefixed /crm so public catalogue routes (/universities, /courses)
| are never shadowed (Laravel keeps last route per method+URI).
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('crm')->group(function () {
    // Candidates
    Route::get('/candidates-export', [CandidateController::class, 'export'])->name('candidates.export');
    Route::get('/candidates-import', [CandidateController::class, 'import'])->name('candidates.import');
    Route::post('/candidates-import', [CandidateController::class, 'importStore'])->name('candidates.import.store');
    Route::post('/candidates-import-confirm', [CandidateController::class, 'importConfirm'])->name('candidates.import.confirm');
    Route::post('/candidates-bulk-assign', [CandidateController::class, 'bulkAssign'])->name('candidates.bulk-assign');
    Route::post('/candidates-bulk-status', [CandidateController::class, 'bulkStatus'])->name('candidates.bulk-status');
    Route::post('/candidates-archive', [CandidateController::class, 'archive'])->name('candidates.archive');
    Route::get('/candidates-archived', [CandidateController::class, 'archived'])->name('candidates.archived');
    Route::post('/candidates-archived/{id}/restore', [CandidateController::class, 'restore'])->name('candidates.restore');
    Route::get('/candidates/{candidate}/wizard/{step?}', [CandidateController::class, 'wizard'])->name('candidates.wizard')->whereNumber('step');
    Route::post('/candidates/{candidate}/wizard/{step}', [CandidateController::class, 'wizardStore'])->name('candidates.wizard.store')->whereNumber('step');
    Route::resource('candidates', CandidateController::class);

    // Applications
    Route::post('applications/{application}/status', [ApplicationController::class, 'changeStatus'])->name('applications.status');
    Route::get('applications/{application}/timeline', [ApplicationController::class, 'timeline'])->name('applications.timeline');
    Route::resource('applications', ApplicationController::class);

    // Documents
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::post('documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    Route::post('documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');
    Route::resource('documents', DocumentController::class);

    // Universities & Courses
    Route::get('universities/{university}/courses', [UniversityController::class, 'courses'])->name('universities.courses');
    Route::resource('universities', UniversityController::class);
    Route::get('courses-finder', [CourseController::class, 'finder'])->name('courses.finder');
    Route::resource('courses', CourseController::class);

    // Tasks / Appointments / Notes / Communications
    Route::resource('tasks', TaskController::class);
    Route::get('appointments', [TaskController::class, 'appointmentsIndex'])->name('appointments.index');
    Route::get('appointments/create', [TaskController::class, 'appointmentCreate'])->name('appointments.create');
    Route::post('appointments', [TaskController::class, 'appointmentStore'])->name('appointments.store');
    Route::get('appointments/{appointment}', [TaskController::class, 'appointmentShow'])->name('appointments.show');
    Route::get('appointments/{appointment}/edit', [TaskController::class, 'appointmentEdit'])->name('appointments.edit');
    Route::match(['put', 'patch'], 'appointments/{appointment}', [TaskController::class, 'appointmentUpdate'])->name('appointments.update');
    Route::delete('appointments/{appointment}', [TaskController::class, 'appointmentDestroy'])->name('appointments.destroy');
    Route::post('notes', [TaskController::class, 'noteStore'])->name('notes.store');
    Route::delete('notes/{note}', [TaskController::class, 'noteDestroy'])->name('notes.destroy');
    Route::post('communications', [TaskController::class, 'communicationStore'])->name('communications.store');

    // Leads
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::resource('leads', LeadController::class);

    // Journey (offers / deposits / CAS / visa / enrolment)
    Route::get('journey', [JourneyController::class, 'index'])->name('journey.index');
    Route::get('journey/offers', [JourneyController::class, 'offers'])->name('journey.offers');
    Route::post('applications/{application}/offers', [JourneyController::class, 'offerStore'])->name('journey.offers.store');
    Route::get('journey/deposits', [JourneyController::class, 'deposits'])->name('journey.deposits');
    Route::post('applications/{application}/deposits', [JourneyController::class, 'depositStore'])->name('journey.deposits.store');
    Route::get('journey/cas', [JourneyController::class, 'casIndex'])->name('journey.cas');
    Route::post('applications/{application}/cas', [JourneyController::class, 'casStore'])->name('journey.cas.store');
    Route::get('journey/visas', [JourneyController::class, 'visaIndex'])->name('journey.visas');
    Route::post('applications/{application}/visas', [JourneyController::class, 'visaStore'])->name('journey.visas.store');
    Route::get('journey/enrolments', [JourneyController::class, 'enrolmentIndex'])->name('journey.enrolments');
    Route::post('applications/{application}/enrolments', [JourneyController::class, 'enrolmentStore'])->name('journey.enrolments.store');

    // Finance
    Route::get('commissions', [FinanceController::class, 'commissions'])->name('commissions.index');
    Route::post('commissions/{commission}/claim', [FinanceController::class, 'claimCommission'])->name('commissions.claim');
    Route::post('commissions/{commission}/receive', [FinanceController::class, 'receiveCommission'])->name('commissions.receive');
    Route::get('invoices', [FinanceController::class, 'invoices'])->name('invoices.index');
    Route::get('invoices/create', [FinanceController::class, 'invoiceCreate'])->name('invoices.create');
    Route::post('invoices', [FinanceController::class, 'invoiceStore'])->name('invoices.store');
    Route::get('invoices/{invoice}', [FinanceController::class, 'invoiceShow'])->name('invoices.show');
    Route::get('invoices/{invoice}/pdf', [FinanceController::class, 'invoicePdf'])->name('invoices.pdf');
    Route::post('payments', [FinanceController::class, 'paymentStore'])->name('payments.store');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/candidates', [ReportController::class, 'candidates'])->name('reports.candidates');
    Route::get('reports/applications', [ReportController::class, 'applications'])->name('reports.applications');
    Route::get('reports/visa', [ReportController::class, 'visa'])->name('reports.visa');
    Route::get('reports/finance', [ReportController::class, 'finance'])->name('reports.finance');
    Route::get('reports/enrolments', [ReportController::class, 'enrolments'])->name('reports.enrolments');
    Route::get('reports/staff', [ReportController::class, 'staff'])->name('reports.staff');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/funnel', [ReportController::class, 'funnel'])->name('reports.funnel');
    Route::get('reports/marketing', [ReportController::class, 'marketing'])->name('reports.marketing');
    Route::get('reports/universities', [ReportController::class, 'universities'])->name('reports.universities');
    Route::get('reports/deadlines', [ReportController::class, 'deadlines'])->name('reports.deadlines');
    Route::get('reports/sla', [ReportController::class, 'sla'])->name('reports.sla');
    Route::get('search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');

    // Counselling
    Route::resource('counselling', \App\Http\Controllers\CounsellingController::class);

    // Student support
    Route::get('support/accommodations', [\App\Http\Controllers\StudentSupportController::class, 'accommodationIndex'])->name('support.accommodations');
    Route::post('support/accommodations', [\App\Http\Controllers\StudentSupportController::class, 'accommodationStore'])->name('support.accommodations.store');
    Route::put('support/accommodations/{id}', [\App\Http\Controllers\StudentSupportController::class, 'accommodationUpdate'])->name('support.accommodations.update');
    Route::delete('support/accommodations/{id}', [\App\Http\Controllers\StudentSupportController::class, 'accommodationDestroy'])->name('support.accommodations.destroy');
    Route::get('support/predeparture/{id}', [\App\Http\Controllers\StudentSupportController::class, 'predepartureShow'])->name('support.predeparture');
    Route::put('support/predeparture/{id}', [\App\Http\Controllers\StudentSupportController::class, 'predepartureUpdate'])->name('support.predeparture.update');
    Route::get('support/arrival/{id}', [\App\Http\Controllers\StudentSupportController::class, 'arrivalShow'])->name('support.arrival');
    Route::put('support/arrival/{id}', [\App\Http\Controllers\StudentSupportController::class, 'arrivalUpdate'])->name('support.arrival.update');
    Route::get('support/sponsorships', [\App\Http\Controllers\StudentSupportController::class, 'sponsorshipIndex'])->name('support.sponsorships');
    Route::post('support/sponsorships', [\App\Http\Controllers\StudentSupportController::class, 'sponsorshipStore'])->name('support.sponsorships.store');
    Route::put('support/sponsorships/{id}', [\App\Http\Controllers\StudentSupportController::class, 'sponsorshipUpdate'])->name('support.sponsorships.update');
    Route::delete('support/sponsorships/{id}', [\App\Http\Controllers\StudentSupportController::class, 'sponsorshipDestroy'])->name('support.sponsorships.destroy');
    Route::get('support/fees', [\App\Http\Controllers\StudentSupportController::class, 'feeIndex'])->name('support.fees');
    Route::post('support/fees', [\App\Http\Controllers\StudentSupportController::class, 'feeStore'])->name('support.fees.store');
    Route::put('support/fees/{id}', [\App\Http\Controllers\StudentSupportController::class, 'feeUpdate'])->name('support.fees.update');

    // Journey extras
    Route::get('journey/offers/{id}', [JourneyController::class, 'offerShow'])->name('journey.offers.show');
    Route::post('journey/offers/{id}/conditions', [JourneyController::class, 'conditionStore'])->name('journey.conditions.store');
    Route::put('journey/conditions/{id}', [JourneyController::class, 'conditionUpdate'])->name('journey.conditions.update');
    Route::patch('journey/conditions/{id}/toggle', [JourneyController::class, 'conditionToggle'])->name('journey.conditions.toggle');

    // Engagement (CRM)
    Route::get('engagement/wishlist', [\App\Http\Controllers\EngagementController::class, 'wishlistIndex'])->name('engagement.wishlist.index');
    Route::post('engagement/wishlist', [\App\Http\Controllers\EngagementController::class, 'wishlistStore'])->name('engagement.wishlist.store');
    Route::post('engagement/wishlist/toggle', [\App\Http\Controllers\EngagementController::class, 'wishlistToggle'])->name('engagement.wishlist.toggle');
    Route::delete('engagement/wishlist/{id}', [\App\Http\Controllers\EngagementController::class, 'wishlistDestroy'])->name('engagement.wishlist.destroy');
    Route::post('engagement/shortlists', [\App\Http\Controllers\EngagementController::class, 'shortlistStore'])->name('engagement.shortlist.store');
    Route::match(['put', 'patch'], 'engagement/shortlists/{id}', [\App\Http\Controllers\EngagementController::class, 'shortlistUpdate'])->name('engagement.shortlist.update');
    Route::delete('engagement/shortlists/{id}', [\App\Http\Controllers\EngagementController::class, 'shortlistDestroy'])->name('engagement.shortlist.destroy');

    // Messages
    Route::get('messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{candidate}', [\App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');

    // Availabilities + calendar
    Route::get('availabilities', [\App\Http\Controllers\AvailabilityController::class, 'index'])->name('availabilities.index');
    Route::post('availabilities', [\App\Http\Controllers\AvailabilityController::class, 'store'])->name('availabilities.store');
    Route::match(['put', 'patch'], 'availabilities/{availability}', [\App\Http\Controllers\AvailabilityController::class, 'update'])->name('availabilities.update');
    Route::delete('availabilities/{availability}', [\App\Http\Controllers\AvailabilityController::class, 'destroy'])->name('availabilities.destroy');
    Route::get('calendar', [\App\Http\Controllers\AvailabilityController::class, 'calendar'])->name('calendar.index');

    // Automation
    Route::get('automation/rules', [\App\Http\Controllers\AutomationController::class, 'index'])->name('automation.rules');
    Route::post('automation/rules', [\App\Http\Controllers\AutomationController::class, 'store'])->name('automation.rules.store');
    Route::match(['put', 'patch'], 'automation/rules/{rule}', [\App\Http\Controllers\AutomationController::class, 'update'])->name('automation.rules.update');
    Route::delete('automation/rules/{rule}', [\App\Http\Controllers\AutomationController::class, 'destroy'])->name('automation.rules.destroy');
    Route::get('automation/logs', [\App\Http\Controllers\AutomationController::class, 'logs'])->name('automation.logs');

    // Finance extensions
    Route::get('referrals', [FinanceController::class, 'referrals'])->name('referrals.index');
    Route::post('referrals', [FinanceController::class, 'referralStore'])->name('referrals.store');
    Route::patch('referrals/{referralPartner}', [FinanceController::class, 'referralUpdate'])->name('referrals.update');
    Route::delete('referrals/{referralPartner}', [FinanceController::class, 'referralDestroy'])->name('referrals.destroy');
    Route::post('referrals/pay', [FinanceController::class, 'payPartner'])->name('referrals.pay');
    Route::post('referral-payments/{referralPayment}/paid', [FinanceController::class, 'markPaid'])->name('referral-payments.paid');
    Route::get('student-payments', [FinanceController::class, 'studentPayments'])->name('student-payments.index');
    Route::post('student-payments', [FinanceController::class, 'studentPaymentStore'])->name('student-payments.store');
    Route::patch('student-payments/{studentPayment}', [FinanceController::class, 'studentPaymentUpdate'])->name('student-payments.update');
    Route::post('commissions/{commission}/clawback', [FinanceController::class, 'clawback'])->name('commissions.clawback');
    Route::get('revenue', [FinanceController::class, 'revenue'])->name('revenue.index');

    // University nested
    Route::get('universities/{university}/contacts', [UniversityController::class, 'contactsIndex'])->name('universities.contacts.index');
    Route::post('universities/{university}/contacts', [UniversityController::class, 'contactStore'])->name('universities.contacts.store');
    Route::match(['put', 'patch'], 'universities/{university}/contacts/{contact}', [UniversityController::class, 'contactUpdate'])->name('universities.contacts.update');
    Route::delete('universities/{university}/contacts/{contact}', [UniversityController::class, 'contactDestroy'])->name('universities.contacts.destroy');
    Route::get('universities/{university}/scholarships', [UniversityController::class, 'scholarshipsIndex'])->name('universities.scholarships.index');
    Route::post('universities/{university}/scholarships', [UniversityController::class, 'scholarshipStore'])->name('universities.scholarships.store');
    Route::match(['put', 'patch'], 'universities/{university}/scholarships/{scholarship}', [UniversityController::class, 'scholarshipUpdate'])->name('universities.scholarships.update');
    Route::delete('universities/{university}/scholarships/{scholarship}', [UniversityController::class, 'scholarshipDestroy'])->name('universities.scholarships.destroy');
    Route::get('courses/{course}/requirements', [CourseController::class, 'requirementsIndex'])->name('courses.requirements.index');
    Route::post('courses/{course}/requirements', [CourseController::class, 'requirementStore'])->name('courses.requirements.store');
    Route::delete('courses/{course}/requirements/{requirement}', [CourseController::class, 'requirementDestroy'])->name('courses.requirements.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin-only: users / settings / audit / notifications + CMS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('crm')->group(function () {
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::match(['put', 'patch'], 'settings', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('teams', \App\Http\Controllers\TeamController::class);
    Route::resource('email-templates', \App\Http\Controllers\EmailTemplateController::class);

    Route::get('users', [SettingController::class, 'usersIndex'])->name('users.index');
    Route::get('users/create', [SettingController::class, 'userCreate'])->name('users.create');
    Route::post('users', [SettingController::class, 'userStore'])->name('users.store');
    Route::get('users/{user}', [SettingController::class, 'userShow'])->name('users.show');
    Route::get('users/{user}/edit', [SettingController::class, 'userEdit'])->name('users.edit');
    Route::match(['put', 'patch'], 'users/{user}', [SettingController::class, 'userUpdate'])->name('users.update');
    Route::delete('users/{user}', [SettingController::class, 'userDestroy'])->name('users.destroy');

    Route::get('audit-logs', [SettingController::class, 'auditIndex'])->name('audit.index');
    Route::get('sessions', [SettingController::class, 'sessionsIndex'])->name('settings.sessions');
    Route::delete('sessions/{id}', [SettingController::class, 'sessionTerminate'])->name('settings.sessions.terminate');
    Route::get('login-history', [SettingController::class, 'loginHistory'])->name('settings.login-history');
    Route::get('notifications', [SettingController::class, 'notificationsIndex'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [SettingController::class, 'notificationRead'])->name('notifications.read');
    Route::post('notifications-read-all', [SettingController::class, 'notificationsReadAll'])->name('notifications.read-all');

    // CMS
    Route::get('cms/pages', [CmsController::class, 'pagesIndex'])->name('cms.pages.index');
    Route::get('cms/pages/create', [CmsController::class, 'pageCreate'])->name('cms.pages.create');
    Route::post('cms/pages', [CmsController::class, 'pageStore'])->name('cms.pages.store');
    Route::get('cms/pages/{page}', [CmsController::class, 'pageShow'])->name('cms.pages.show');
    Route::get('cms/pages/{page}/edit', [CmsController::class, 'pageEdit'])->name('cms.pages.edit');
    Route::match(['put', 'patch'], 'cms/pages/{page}', [CmsController::class, 'pageUpdate'])->name('cms.pages.update');
    Route::delete('cms/pages/{page}', [CmsController::class, 'pageDestroy'])->name('cms.pages.destroy');

    Route::get('cms/testimonials', [CmsController::class, 'testimonialsIndex'])->name('cms.testimonials.index');
    Route::get('cms/testimonials/create', [CmsController::class, 'testimonialCreate'])->name('cms.testimonials.create');
    Route::post('cms/testimonials', [CmsController::class, 'testimonialStore'])->name('cms.testimonials.store');
    Route::get('cms/testimonials/{testimonial}', [CmsController::class, 'testimonialShow'])->name('cms.testimonials.show');
    Route::get('cms/testimonials/{testimonial}/edit', [CmsController::class, 'testimonialEdit'])->name('cms.testimonials.edit');
    Route::match(['put', 'patch'], 'cms/testimonials/{testimonial}', [CmsController::class, 'testimonialUpdate'])->name('cms.testimonials.update');
    Route::delete('cms/testimonials/{testimonial}', [CmsController::class, 'testimonialDestroy'])->name('cms.testimonials.destroy');

    Route::get('cms/team', [CmsController::class, 'teamIndex'])->name('cms.team.index');
    Route::get('cms/team/create', [CmsController::class, 'teamCreate'])->name('cms.team.create');
    Route::post('cms/team', [CmsController::class, 'teamStore'])->name('cms.team.store');
    Route::get('cms/team/{team}', [CmsController::class, 'teamShow'])->name('cms.team.show');
    Route::get('cms/team/{team}/edit', [CmsController::class, 'teamEdit'])->name('cms.team.edit');
    Route::match(['put', 'patch'], 'cms/team/{team}', [CmsController::class, 'teamUpdate'])->name('cms.team.update');
    Route::delete('cms/team/{team}', [CmsController::class, 'teamDestroy'])->name('cms.team.destroy');

    Route::get('cms/faqs', [CmsController::class, 'faqsIndex'])->name('cms.faqs.index');
    Route::get('cms/faqs/create', [CmsController::class, 'faqCreate'])->name('cms.faqs.create');
    Route::post('cms/faqs', [CmsController::class, 'faqStore'])->name('cms.faqs.store');
    Route::get('cms/faqs/{faq}', [CmsController::class, 'faqShow'])->name('cms.faqs.show');
    Route::get('cms/faqs/{faq}/edit', [CmsController::class, 'faqEdit'])->name('cms.faqs.edit');
    Route::match(['put', 'patch'], 'cms/faqs/{faq}', [CmsController::class, 'faqUpdate'])->name('cms.faqs.update');
    Route::delete('cms/faqs/{faq}', [CmsController::class, 'faqDestroy'])->name('cms.faqs.destroy');

    // CMS extensions: banners, menus, blog, forms, media
    Route::get('cms/banners', [CmsController::class, 'bannersIndex'])->name('cms.banners.index');
    Route::get('cms/banners/create', [CmsController::class, 'bannerCreate'])->name('cms.banners.create');
    Route::post('cms/banners', [CmsController::class, 'bannerStore'])->name('cms.banners.store');
    Route::get('cms/banners/{banner}', [CmsController::class, 'bannerShow'])->name('cms.banners.show');
    Route::get('cms/banners/{banner}/edit', [CmsController::class, 'bannerEdit'])->name('cms.banners.edit');
    Route::match(['put', 'patch'], 'cms/banners/{banner}', [CmsController::class, 'bannerUpdate'])->name('cms.banners.update');
    Route::delete('cms/banners/{banner}', [CmsController::class, 'bannerDestroy'])->name('cms.banners.destroy');
    Route::get('cms/menus', [CmsController::class, 'menusIndex'])->name('cms.menus.index');
    Route::get('cms/menus/create', [CmsController::class, 'menuCreate'])->name('cms.menus.create');
    Route::post('cms/menus', [CmsController::class, 'menuStore'])->name('cms.menus.store');
    Route::get('cms/menus/{menu}', [CmsController::class, 'menuShow'])->name('cms.menus.show');
    Route::get('cms/menus/{menu}/edit', [CmsController::class, 'menuEdit'])->name('cms.menus.edit');
    Route::match(['put', 'patch'], 'cms/menus/{menu}', [CmsController::class, 'menuUpdate'])->name('cms.menus.update');
    Route::delete('cms/menus/{menu}', [CmsController::class, 'menuDestroy'])->name('cms.menus.destroy');
    Route::get('cms/menus/{menu}/items', [CmsController::class, 'menuItems'])->name('cms.menus.items');
    Route::post('cms/menus/{menu}/items', [CmsController::class, 'menuItemStore'])->name('cms.menus.items.store');
    Route::match(['put', 'patch'], 'cms/menus/{menu}/items/{item}', [CmsController::class, 'menuItemUpdate'])->name('cms.menus.items.update');
    Route::delete('cms/menus/{menu}/items/{item}', [CmsController::class, 'menuItemDestroy'])->name('cms.menus.items.destroy');
    Route::get('cms/blog-categories', [CmsController::class, 'blogCategoriesIndex'])->name('cms.blog-categories.index');
    Route::post('cms/blog-categories', [CmsController::class, 'blogCategoryStore'])->name('cms.blog-categories.store');
    Route::match(['put', 'patch'], 'cms/blog-categories/{category}', [CmsController::class, 'blogCategoryUpdate'])->name('cms.blog-categories.update');
    Route::delete('cms/blog-categories/{category}', [CmsController::class, 'blogCategoryDestroy'])->name('cms.blog-categories.destroy');
    Route::get('cms/blog', [CmsController::class, 'blogPostsIndex'])->name('cms.blog.index');
    Route::get('cms/blog/create', [CmsController::class, 'blogPostCreate'])->name('cms.blog.create');
    Route::post('cms/blog', [CmsController::class, 'blogPostStore'])->name('cms.blog.store');
    Route::get('cms/blog/{post}', [CmsController::class, 'blogPostShow'])->name('cms.blog.show');
    Route::get('cms/blog/{post}/edit', [CmsController::class, 'blogPostEdit'])->name('cms.blog.edit');
    Route::match(['put', 'patch'], 'cms/blog/{post}', [CmsController::class, 'blogPostUpdate'])->name('cms.blog.update');
    Route::delete('cms/blog/{post}', [CmsController::class, 'blogPostDestroy'])->name('cms.blog.destroy');
    Route::get('cms/forms', [CmsController::class, 'formsIndex'])->name('cms.forms.index');
    Route::get('cms/forms/create', [CmsController::class, 'formCreate'])->name('cms.forms.create');
    Route::post('cms/forms', [CmsController::class, 'formStore'])->name('cms.forms.store');
    Route::get('cms/forms/{form}', [CmsController::class, 'formShow'])->name('cms.forms.show');
    Route::get('cms/forms/{form}/edit', [CmsController::class, 'formEdit'])->name('cms.forms.edit');
    Route::match(['put', 'patch'], 'cms/forms/{form}', [CmsController::class, 'formUpdate'])->name('cms.forms.update');
    Route::delete('cms/forms/{form}', [CmsController::class, 'formDestroy'])->name('cms.forms.destroy');
    Route::get('cms/forms/{form}/submissions', [CmsController::class, 'formSubmissions'])->name('cms.forms.submissions.index');
    Route::get('cms/forms/{form}/submissions/{submission}', [CmsController::class, 'formSubmissionShow'])->name('cms.forms.submissions.show');
    Route::get('cms/media', [CmsController::class, 'mediaIndex'])->name('cms.media.index');
    Route::post('cms/media', [CmsController::class, 'mediaStore'])->name('cms.media.store');
    Route::match(['put', 'patch'], 'cms/media/{media}', [CmsController::class, 'mediaUpdate'])->name('cms.media.update');
    Route::delete('cms/media/{media}', [CmsController::class, 'mediaDestroy'])->name('cms.media.destroy');

    // Branches, GDPR, workflow, system
    Route::resource('branches', \App\Http\Controllers\BranchController::class);
    Route::get('gdpr/consents', [\App\Http\Controllers\GdprController::class, 'consents'])->name('gdpr.consents');
    Route::get('gdpr/requests', [\App\Http\Controllers\GdprController::class, 'requests'])->name('gdpr.requests');
    Route::post('gdpr/requests', [\App\Http\Controllers\GdprController::class, 'storeRequest'])->name('gdpr.requests.store');
    Route::patch('gdpr/requests/{gdprRequest}', [\App\Http\Controllers\GdprController::class, 'updateRequest'])->name('gdpr.requests.update');
    Route::get('gdpr/requests/{gdprRequest}/download', [\App\Http\Controllers\GdprController::class, 'downloadExport'])->name('gdpr.requests.download');
    Route::get('gdpr/access-logs', [\App\Http\Controllers\GdprController::class, 'accessLogs'])->name('gdpr.access-logs');
    Route::get('workflow/templates', [\App\Http\Controllers\WorkflowController::class, 'templates'])->name('workflow.templates');
    Route::post('workflow/templates', [\App\Http\Controllers\WorkflowController::class, 'templateStore'])->name('workflow.templates.store');
    Route::patch('workflow/templates/{template}', [\App\Http\Controllers\WorkflowController::class, 'templateUpdate'])->name('workflow.templates.update');
    Route::delete('workflow/templates/{template}', [\App\Http\Controllers\WorkflowController::class, 'templateDestroy'])->name('workflow.templates.destroy');
    Route::get('workflow/transitions', [\App\Http\Controllers\WorkflowController::class, 'transitions'])->name('workflow.transitions');
    Route::post('workflow/transitions', [\App\Http\Controllers\WorkflowController::class, 'transitionStore'])->name('workflow.transitions.store');
    Route::patch('workflow/transitions/{transition}', [\App\Http\Controllers\WorkflowController::class, 'transitionUpdate'])->name('workflow.transitions.update');
    Route::delete('workflow/transitions/{transition}', [\App\Http\Controllers\WorkflowController::class, 'transitionDestroy'])->name('workflow.transitions.destroy');
    Route::get('workflow/fields', [\App\Http\Controllers\WorkflowController::class, 'fields'])->name('workflow.fields');
    Route::post('workflow/fields', [\App\Http\Controllers\WorkflowController::class, 'fieldStore'])->name('workflow.fields.store');
    Route::patch('workflow/fields/{field}', [\App\Http\Controllers\WorkflowController::class, 'fieldUpdate'])->name('workflow.fields.update');
    Route::delete('workflow/fields/{field}', [\App\Http\Controllers\WorkflowController::class, 'fieldDestroy'])->name('workflow.fields.destroy');
    Route::get('system/health', [\App\Http\Controllers\SystemController::class, 'health'])->name('system.health');
    Route::get('system/backups', [\App\Http\Controllers\SystemController::class, 'backups'])->name('system.backups');
    Route::post('system/backups', [\App\Http\Controllers\SystemController::class, 'backupStore'])->name('system.backups.store');
    Route::get('system/backups/{backup}/download', [\App\Http\Controllers\SystemController::class, 'backupDownload'])->name('system.backups.download');
    Route::get('system/mail-logs', [\App\Http\Controllers\SystemController::class, 'mailLogs'])->name('system.mail-logs');
    Route::get('system/queue', [\App\Http\Controllers\SystemController::class, 'queue'])->name('system.queue');
    Route::post('system/queue/{failedId}/retry', [\App\Http\Controllers\SystemController::class, 'queueRetry'])->name('system.queue.retry');
    Route::delete('system/queue/{id}', [\App\Http\Controllers\SystemController::class, 'queueDelete'])->name('system.queue.delete');
});

/*
|--------------------------------------------------------------------------
| Candidate portal
|--------------------------------------------------------------------------
*/
Route::prefix('portal')->middleware(['auth', 'role:candidate'])->name('portal.')->group(function () {
    Route::get('/dashboard', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [PortalController::class, 'profile'])->name('profile');
    Route::match(['put', 'patch'], '/profile', [PortalController::class, 'profileUpdate'])->name('profile.update');
    Route::get('/applications', [PortalController::class, 'applications'])->name('applications');
    Route::get('/applications/{application}', [PortalController::class, 'applicationShow'])->name('applications.show');
    Route::get('/documents', [PortalController::class, 'documents'])->name('documents');
    Route::post('/documents', [PortalController::class, 'documentStore'])->name('documents.store');
    Route::get('/appointments', [PortalController::class, 'appointments'])->name('appointments');
    Route::post('/appointments', [PortalController::class, 'appointmentStore'])->name('appointments.store');
    Route::get('/tasks', [PortalController::class, 'tasks'])->name('tasks');
    Route::get('/notifications', [PortalController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{notification}/read', [PortalController::class, 'notificationRead'])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| Finance panels + new reports (added only, existing routes untouched)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('crm')->group(function () {
    // Referral partners
    Route::get('referrals', [FinanceController::class, 'referrals'])->name('referrals.index');
    Route::post('referrals', [FinanceController::class, 'referralStore'])->name('referrals.store');
    Route::match(['put', 'patch'], 'referrals/{referralPartner}', [FinanceController::class, 'referralUpdate'])->name('referrals.update');
    Route::delete('referrals/{referralPartner}', [FinanceController::class, 'referralDestroy'])->name('referrals.destroy');
    Route::post('referrals/pay', [FinanceController::class, 'payPartner'])->name('referrals.pay');
    Route::post('referral-payments/{referralPayment}/paid', [FinanceController::class, 'markPaid'])->name('referral-payments.paid');
    // Student payments
    Route::get('student-payments', [FinanceController::class, 'studentPayments'])->name('student-payments.index');
    Route::post('student-payments', [FinanceController::class, 'studentPaymentStore'])->name('student-payments.store');
    Route::match(['put', 'patch'], 'student-payments/{studentPayment}', [FinanceController::class, 'studentPaymentUpdate'])->name('student-payments.update');
    // Clawback + revenue
    Route::post('commissions/{commission}/clawback', [FinanceController::class, 'clawback'])->name('commissions.clawback');
    Route::get('revenue', [FinanceController::class, 'revenue'])->name('revenue.index');
    // New reports (CSV/XLSX via ?format=csv|xlsx, legacy export() untouched)
    Route::get('reports/funnel', [ReportController::class, 'funnel'])->name('reports.funnel');
    Route::get('reports/marketing', [ReportController::class, 'marketing'])->name('reports.marketing');
    Route::get('reports/universities', [ReportController::class, 'universities'])->name('reports.universities');
    Route::get('reports/deadlines', [ReportController::class, 'deadlines'])->name('reports.deadlines');
    Route::get('reports/sla', [ReportController::class, 'sla'])->name('reports.sla');
});
