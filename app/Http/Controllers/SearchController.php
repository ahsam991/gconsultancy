<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Course;
use App\Models\Lead;
use App\Models\University;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $candidates = collect();
        $applications = collect();
        $universities = collect();
        $courses = collect();
        $leads = collect();

        if (strlen($q) >= 2) {
            $role = $request->user()->role?->name;
            $candidates = Candidate::where(function ($qq) use ($q) {
                $qq->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('uid', 'like', "%{$q}%");
            })->when($role === 'staff', fn ($qq) => $qq->where('assigned_staff_id', $request->user()->id))
              ->limit(10)->get();

            $applications = Application::where('uid', 'like', "%{$q}%")
                ->orWhere('university_ref', 'like', "%{$q}%")
                ->when($role === 'staff', fn ($qq) => $qq->where('assigned_staff_id', $request->user()->id))
                ->limit(10)->get();

            $universities = University::where('name', 'like', "%{$q}%")->limit(10)->get();
            $courses = Course::where('name', 'like', "%{$q}%")->limit(10)->get();

            if (in_array($role, ['admin', 'manager'], true)) {
                $leads = Lead::where('first_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->limit(10)->get();
            }
        }

        return view('search.results', compact('q', 'candidates', 'applications', 'universities', 'courses', 'leads'));
    }
}
