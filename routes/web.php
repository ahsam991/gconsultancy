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

/*
|--------------------------------------------------------------------------
| Auth (keep existing URIs)
|--------------------------------------------------------------------------
*/
Route::get('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/login', [AuthController::class, 'authenticate'])->middleware('throttle:10,1')->name('login.attempt');
Route::get('/auth/register', [AuthController::class, 'register'])->name('register');
Route::post('/auth/register', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('register.store');
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
    Route::get('search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');
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
