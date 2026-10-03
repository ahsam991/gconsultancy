<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityRequest;
use App\Models\Country;
use App\Models\Course;
use App\Models\Scholarship;
use App\Models\University;
use App\Models\UniversityContact;
use App\Services\AuditService;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function index(Request $request)
    {
        $query = University::with('country');
        if ($s = $request->get('q')) {
            $query->where('name', 'like', "%{$s}%");
        }
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->get('country_id'));
        }
        $universities = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('universities.index', compact('universities'));
    }

    public function create()
    {
        $countries = Country::where('active', true)->orderBy('name')->get();
        return view('universities.create', compact('countries'));
    }

    public function store(StoreUniversityRequest $request)
    {
        $data = $request->validated();
        $data['uid'] = 'UNI-'.strtoupper(uniqid());
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $university = University::create($data);
        AuditService::log('university.created', $university, null, $university->toArray());
        return redirect()->route('universities.show', $university)->with('status', 'University created.');
    }

    public function show(University $university)
    {
        $university->load(['courses', 'country']);
        return view('universities.show', compact('university'));
    }

    public function edit(University $university)
    {
        $countries = Country::where('active', true)->orderBy('name')->get();
        return view('universities.edit', compact('university', 'countries'));
    }

    public function update(StoreUniversityRequest $request, University $university)
    {
        $old = $university->toArray();
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $university->update($data);
        AuditService::log('university.updated', $university, $old, $university->fresh()->toArray());
        return redirect()->route('universities.show', $university)->with('status', 'University updated.');
    }

    public function destroy(University $university)
    {
        $university->update(['active' => false]);
        $university->delete();
        AuditService::log('university.deleted', $university);
        return redirect()->route('universities.index')->with('status', 'University deleted.');
    }

    public function courses(University $university)
    {
        $courses = $university->courses()->paginate(15);
        return view('universities.courses', compact('university', 'courses'));
    }

    /* ------------------------- contacts (nested) ------------------------- */

    public function contactsIndex(University $university)
    {
        $contacts = $university->contacts()->orderByDesc('is_primary')->orderBy('name')->paginate(15);
        return view('universities.contacts', compact('university', 'contacts'));
    }

    public function contactStore(Request $request, University $university)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);
        $data['university_id'] = $university->id;
        $data['is_primary'] = $request->boolean('is_primary');
        if ($data['is_primary']) {
            UniversityContact::where('university_id', $university->id)->update(['is_primary' => false]);
        }
        UniversityContact::create($data);
        return back()->with('status', 'Contact added.');
    }

    public function contactUpdate(Request $request, University $university, UniversityContact $contact)
    {
        abort_if($contact->university_id !== $university->id, 404);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);
        $data['is_primary'] = $request->boolean('is_primary');
        if ($data['is_primary']) {
            UniversityContact::where('university_id', $university->id)->where('id', '!=', $contact->id)->update(['is_primary' => false]);
        }
        $contact->update($data);
        return back()->with('status', 'Contact updated.');
    }

    public function contactDestroy(University $university, UniversityContact $contact)
    {
        abort_if($contact->university_id !== $university->id, 404);
        $contact->delete();
        return back()->with('status', 'Contact deleted.');
    }

    /* ------------------------- scholarships (nested) ------------------------- */

    public function scholarshipsIndex(University $university)
    {
        $scholarships = Scholarship::with('course')
            ->where('university_id', $university->id)
            ->orderByDesc('created_at')
            ->paginate(15);
        $courses = Course::where('university_id', $university->id)->orderBy('name')->get(['id', 'name']);
        return view('universities.scholarships', compact('university', 'scholarships', 'courses'));
    }

    public function scholarshipStore(Request $request, University $university)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'amount_type' => 'nullable|string|max:50',
            'criteria' => 'nullable|string|max:2000',
            'deadline' => 'nullable|date',
            'course_id' => 'nullable|exists:courses,id',
            'active' => 'nullable|boolean',
        ]);
        $data['university_id'] = $university->id;
        $data['active'] = $request->boolean('active', true);
        Scholarship::create($data);
        return back()->with('status', 'Scholarship added.');
    }

    public function scholarshipUpdate(Request $request, University $university, Scholarship $scholarship)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'amount_type' => 'nullable|string|max:50',
            'criteria' => 'nullable|string|max:2000',
            'deadline' => 'nullable|date',
            'course_id' => 'nullable|exists:courses,id',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $scholarship->update($data);
        return back()->with('status', 'Scholarship updated.');
    }

    public function scholarshipDestroy(University $university, Scholarship $scholarship)
    {
        $scholarship->delete();
        return back()->with('status', 'Scholarship deleted.');
    }
}
