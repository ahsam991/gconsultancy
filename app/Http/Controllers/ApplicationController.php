<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Models\Application;
use App\Models\Course;
use App\Models\University;
use App\Models\Candidate;
use App\Services\ApplicationStatusService;
use App\Services\AuditService;
use App\Services\CommissionService;
use App\Services\UIDService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApplicationController extends Controller
{
    use AuthorizesRequests;

    protected function scopedQuery(Request $request)
    {
        $user = $request->user();
        $role = $user->role?->name;
        $q = Application::with(['candidate', 'university', 'course']);
        if ($role === 'staff') {
            $q->where(function ($qq) use ($user) {
                $qq->where('assigned_staff_id', $user->id)
                    ->orWhereHas('candidate', fn ($c) => $c->where('assigned_staff_id', $user->id));
            });
        }
        if ($role === 'candidate') {
            $q->whereHas('candidate', fn ($c) => $c->where('user_id', $user->id));
        }
        return $q;
    }

    public function index(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('viewAny', \App\Models\Application::class);
        $query = $this->scopedQuery($request);
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->get('university_id'));
        }
        if ($request->filled('q')) {
            $s = $request->get('q');
            $query->where(function ($qq) use ($s) {
                $qq->where('uid', 'like', "%{$s}%")
                    ->orWhere('university_ref', 'like', "%{$s}%")
                    ->orWhereHas('candidate', fn ($c) => $c->where('first_name', 'like', "%{$s}%")->orWhere('last_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            });
        }
        $applications = $query->orderByDesc('updated_at')->paginate(15)->withQueryString();
        return view('applications.index', compact('applications'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Application::class);
        $candidates = Candidate::orderBy('first_name')->limit(500)->get();
        $universities = University::where('active', true)->orderBy('name')->get();
        $courses = Course::where('active', true)->orderBy('name')->limit(500)->get();
        return view('applications.create', compact('candidates', 'universities', 'courses'));
    }

    public function store(StoreApplicationRequest $request)
    {
        Gate::authorize('create', Application::class);
        $data = $request->validated();
        $data['uid'] = UIDService::applicationUid();
        $data['status'] = $data['status'] ?? 'DRAFT';
        $data['assigned_staff_id'] = $data['assigned_staff_id'] ?? $request->user()->id;
        $data['created_by'] = $request->user()->id;
        $app = Application::create($data);
        AuditService::log('application.created', $app, null, $app->toArray());
        try {
            CommissionService::calculate($app);
        } catch (\Throwable $e) {
        }
        return redirect()->route('applications.show', $app)->with('status', 'Application created: '.$app->uid);
    }

    public function show(Application $application)
    {
        $this->authorize('view', $application);
        $application->load(['candidate', 'university', 'course', 'intake', 'assignedStaff', 'statusHistory.changedBy', 'offer', 'deposit', 'casRecord', 'visaCases', 'enrolment', 'documents', 'tasks', 'notes', 'commission']);
        $allowed = ApplicationStatusService::allowed($application->status);
        $checklist = \App\Services\DocumentService::checklist($application);
        return view('applications.show', compact('application', 'allowed', 'checklist'));
    }

    public function edit(Application $application)
    {
        $this->authorize('update', $application);
        $universities = University::where('active', true)->orderBy('name')->get();
        $courses = Course::where('active', true)->orderBy('name')->limit(500)->get();
        return view('applications.edit', compact('application', 'universities', 'courses'));
    }

    public function update(StoreApplicationRequest $request, Application $application)
    {
        $this->authorize('update', $application);
        $old = $application->toArray();
        $application->update($request->validated());
        AuditService::log('application.updated', $application, $old, $application->fresh()->toArray());
        return redirect()->route('applications.show', $application)->with('status', 'Application updated.');
    }

    public function destroy(Application $application)
    {
        $this->authorize('delete', $application);
        $application->delete();
        AuditService::log('application.deleted', $application);
        return redirect()->route('applications.index')->with('status', 'Application deleted.');
    }

    public function changeStatus(Request $request, Application $application)
    {
        $this->authorize('update', $application);
        $request->validate([
            'status' => 'required|string|max:50',
            'reason' => 'nullable|string',
            'note' => 'nullable|string',
        ]);
        try {
            ApplicationStatusService::transition($application, strtoupper($request->status), $request->reason, $request->note);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['status' => $e->getMessage()]);
        }
        if (in_array(strtoupper($request->status), ['ENROLLED', 'DEPOSIT_PAID'], true)) {
            try {
                CommissionService::calculate($application->fresh());
            } catch (\Throwable $e) {
            }
        }
        return redirect()->route('applications.show', $application)->with('status', 'Status updated to '.$request->status);
    }

    public function timeline(Application $application)
    {
        $this->authorize('view', $application);
        $history = ApplicationStatusService::history($application);
        return view('applications.timeline', compact('application', 'history'));
    }
}
