<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseShortlist;
use App\Models\SavedCourse;
use Illuminate\Http\Request;

class EngagementController extends Controller
{
    protected function resolveCandidateId(Request $request): ?int
    {
        if ($request->filled('candidate_id')) {
            return (int) $request->input('candidate_id');
        }

        if ($request->user()) {
            $candidate = Candidate::where('user_id', $request->user()->id)->first();
            if ($candidate) {
                return $candidate->id;
            }
        }

        return null;
    }

    public function wishlistIndex(Request $request)
    {
        $candidateId = $this->resolveCandidateId($request);
        $saved = collect();
        $candidate = null;

        if ($candidateId) {
            $candidate = Candidate::find($candidateId);
            $saved = SavedCourse::with(['course.university'])
                ->where('candidate_id', $candidateId)
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString();
        }

        $candidates = Candidate::orderBy('first_name')->limit(200)->get(['id', 'first_name', 'last_name', 'email']);

        return view('engagement.wishlist', compact('saved', 'candidate', 'candidates', 'candidateId'));
    }

    public function wishlistStore(Request $request)
    {
        $request->validate([
            'candidate_id' => 'nullable|exists:candidates,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $candidateId = $this->resolveCandidateId($request);
        abort_if(! $candidateId, 422, 'Candidate is required.');

        SavedCourse::firstOrCreate([
            'candidate_id' => $candidateId,
            'course_id' => $request->input('course_id'),
        ]);

        return back()->with('status', 'Course saved to wishlist.');
    }

    public function wishlistToggle(Request $request)
    {
        $request->validate([
            'candidate_id' => 'nullable|exists:candidates,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $candidateId = $this->resolveCandidateId($request);
        abort_if(! $candidateId, 422, 'Candidate is required.');

        $existing = SavedCourse::where('candidate_id', $candidateId)
            ->where('course_id', $request->input('course_id'))
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('status', 'Course removed from wishlist.');
        }

        SavedCourse::create([
            'candidate_id' => $candidateId,
            'course_id' => $request->input('course_id'),
        ]);

        return back()->with('status', 'Course saved to wishlist.');
    }

    public function wishlistDestroy(Request $request, $id)
    {
        $saved = SavedCourse::findOrFail($id);
        $saved->delete();

        return back()->with('status', 'Saved course removed.');
    }

    public function shortlistStore(Request $request)
    {
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'course_id' => 'required|exists:courses,id',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['staff_id'] = $request->user()?->id;
        abort_if(! $data['staff_id'], 403, 'Staff login required.');

        $shortlist = CourseShortlist::where('candidate_id', $data['candidate_id'])
            ->where('course_id', $data['course_id'])
            ->first();

        if ($shortlist) {
            $shortlist->update([
                'notes' => $data['notes'] ?? $shortlist->notes,
                'staff_id' => $data['staff_id'] ?? $shortlist->staff_id,
            ]);
        } else {
            CourseShortlist::create($data);
        }

        return back()->with('status', 'Course shortlisted.');
    }

    public function shortlistUpdate(Request $request, $id)
    {
        $shortlist = CourseShortlist::findOrFail($id);

        $data = $request->validate([
            'notes' => 'nullable|string|max:2000',
        ]);

        $shortlist->update($data);

        return back()->with('status', 'Shortlist notes updated.');
    }

    public function shortlistDestroy(Request $request, $id)
    {
        $shortlist = CourseShortlist::findOrFail($id);
        $shortlist->delete();

        return back()->with('status', 'Shortlist entry removed.');
    }

    public function compare(Request $request)
    {
        $raw = $request->input('ids', $request->input('course_ids', []));

        if (is_string($raw)) {
            $raw = explode(',', $raw);
        }

        $ids = collect(is_array($raw) ? $raw : [$raw])
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->take(3)
            ->values()
            ->all();

        $courses = collect();
        if (! empty($ids)) {
            $courses = Course::with(['university.country'])
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn ($c) => array_search($c->id, $ids))
                ->values();
        }

        return view('engagement.compare', compact('courses', 'ids'));
    }

    public function eligibility(Request $request)
    {
        $filters = $request->validate([
            'gpa' => 'nullable|numeric|min:0|max:4',
            'ielts' => 'nullable|numeric|min:0|max:9',
            'ielts_score' => 'nullable|numeric|min:0|max:9',
            'budget' => 'nullable|numeric|min:0',
            'destination' => 'nullable|string|max:100',
        ]);

        $score = $filters['ielts'] ?? $filters['ielts_score'] ?? null;
        $budget = $filters['budget'] ?? null;
        $destination = $filters['destination'] ?? null;

        $countries = Country::where('active', true)->orderBy('name')->get(['id', 'name']);

        $courses = collect();
        if ($request->isMethod('post') || $request->filled(['budget', 'ielts', 'ielts_score', 'destination', 'gpa'])) {
            $query = Course::with(['university.country'])->where('active', true);

            if (! is_null($budget) && $budget !== '') {
                $query->where('tuition_fee', '<=', $budget);
            }

            if (! is_null($score) && $score !== '') {
                $query->where(function ($q) use ($score) {
                    $q->whereNull('ielts_required')->orWhere('ielts_required', '<=', $score);
                });
            }

            if (! is_null($destination) && $destination !== '') {
                $query->whereHas('university', function ($q) use ($destination) {
                    if (is_numeric($destination)) {
                        $q->where('country_id', $destination);
                    } else {
                        $q->whereHas('country', fn ($c) => $c->where('name', 'like', '%'.$destination.'%'));
                    }
                });
            }

            $courses = $query->orderBy('tuition_fee')->paginate(15)->withQueryString();
        }

        return view('engagement.eligibility', compact('courses', 'countries', 'filters'));
    }

    public function eligibilityCheck(Request $request)
    {
        return $this->eligibility($request);
    }
}
