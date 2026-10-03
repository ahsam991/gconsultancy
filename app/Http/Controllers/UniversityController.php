<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityRequest;
use App\Models\Country;
use App\Models\University;
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
}
