<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Offer;
use App\Models\Testimonial;
use App\Models\University;
use App\Models\VisaCase;
use App\Models\WebsiteEnquiry;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $universities = University::where('active', true)->where('featured', true)->limit(6)->get();
        $courses = Course::with('university')->where('active', true)->where('featured', true)->limit(6)->get();
        $testimonials = Testimonial::where('is_published', true)->orderByDesc('is_featured')->limit(3)->get();
        $faqs = Faq::where('is_published', true)->orderBy('sort_order')->limit(6)->get();
        $stats = [
            'offers' => Offer::count(),
            'visa_rate' => ($v = VisaCase::whereIn('result', ['Approved', 'Refused'])->count()) ? (int) round(VisaCase::where('result', 'Approved')->count() / $v * 100) : 0,
            'partners' => University::where('active', true)->count(),
        ];
        return view('public.home', compact('universities', 'courses', 'testimonials', 'faqs', 'stats'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function services()
    {
        return view('public.services');
    }

    public function studyDestination(string $destination)
    {
        $country = Country::where('name', 'like', "%{$destination}%")
            ->orWhere('iso_code', strtoupper($destination))->first();
        $universities = University::when($country, fn ($q) => $q->where('country_id', $country->id))
            ->where('active', true)->paginate(12);
        return view('public.study', compact('country', 'universities', 'destination'));
    }

    public function universities(Request $request)
    {
        $universities = University::where('active', true)->orderBy('name')->paginate(12);
        return view('public.universities', compact('universities'));
    }

    public function courses(Request $request)
    {
        $courses = Course::with('university')->where('active', true)->orderBy('name')->paginate(12);
        return view('public.courses', compact('courses'));
    }

    public function courseFinder(Request $request)
    {
        return app(CourseController::class)->finder($request);
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function contactSubmit(Request $request)
    {
        return $this->enquirySubmit($request);
    }

    public function appointment()
    {
        return view('public.appointment');
    }

    public function appointmentStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'destination' => 'nullable|string|max:100',
            'date' => 'nullable|date',
            'time' => 'nullable|string|max:10',
            'mode' => 'nullable|string|max:50',
            'message' => 'nullable|string',
        ]);
        WebsiteEnquiry::create([
            'type' => 'Appointment',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'destination' => $data['destination'] ?? null,
            'preferred_date' => $data['date'] ?? null,
            'message' => trim(($data['message'] ?? '').' | Time: '.($data['time'] ?? 'flexible').' | Mode: '.($data['mode'] ?? 'In person'), ' |'),
            'status' => 'NEW',
        ]);
        return redirect()->back()->with('status', 'Appointment request received.');
    }

    public function apply()
    {
        return view('public.apply');
    }

    public function applyStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'qualification' => 'nullable|string|max:255',
            'passing_year' => 'nullable|string|max:10',
            'english_score' => 'nullable|string|max:50',
            'destination' => 'nullable|string|max:100',
            'intake' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);
        $parts = preg_split('/\s+/', trim($data['name']), 2);
        $source = LeadSource::firstOrCreate(['name' => 'Website'], ['active' => true]);
        $lead = Lead::create([
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? '',
            'email' => $data['email'],
            'phone' => $data['phone'],
            'preferred_destination' => $data['destination'] ?? null,
            'source_id' => $source->id,
            'message' => trim(implode(' | ', array_filter([
                isset($data['qualification']) ? 'Qualification: '.$data['qualification'] : null,
                isset($data['passing_year']) ? 'Year: '.$data['passing_year'] : null,
                isset($data['english_score']) ? 'English: '.$data['english_score'] : null,
                isset($data['intake']) ? 'Intake: '.$data['intake'] : null,
                isset($data['subject']) ? 'Subject: '.$data['subject'] : null,
                $data['message'] ?? null,
            ]))),
            'status' => 'NEW',
        ]);
        WebsiteEnquiry::create([
            'type' => 'Apply',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'destination' => $data['destination'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $lead->message,
            'status' => 'NEW',
        ]);
        return redirect()->back()->with('status', 'Application received. Our counsellor will contact you.');
    }

    public function enquirySubmit(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'destination' => 'nullable|string|max:100',
            'message' => 'required|string',
        ]);
        WebsiteEnquiry::create([
            'type' => $data['type'] ?? 'Contact',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'destination' => $data['destination'] ?? null,
            'message' => $data['message'],
            'status' => 'NEW',
        ]);
        $parts = preg_split('/\s+/', trim($data['name']), 2);
        Lead::create([
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? '',
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'preferred_destination' => $data['destination'] ?? null,
            'notes' => $data['message'],
            'status' => 'NEW',
        ]);
        return redirect()->back()->with('status', 'Enquiry submitted successfully.');
    }

    public function courseApply(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
        ]);
        Lead::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'notes' => 'Course interest: '.$data['course_id'],
            'status' => 'NEW',
        ]);
        return redirect()->back()->with('status', 'Course application received.');
    }
}
