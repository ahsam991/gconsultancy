<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\CandidateDocument;
use App\Models\DocumentType;
use App\Services\DocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    use AuthorizesRequests;

    protected function candidate(Request $request): ?Candidate
    {
        return Candidate::where('user_id', $request->user()->id)->first();
    }

    protected function progress(Candidate $candidate): int
    {
        $steps = 0;
        $total = 5;
        if ($candidate->first_name) $steps++;
        if ($candidate->documents()->exists()) $steps++;
        if ($candidate->applications()->exists()) $steps++;
        if ($candidate->applications()->whereIn('status', ['CONDITIONAL_OFFER', 'UNCONDITIONAL_OFFER', 'DEPOSIT_PAID', 'CAS_ISSUED', 'VISA_APPROVED', 'ENROLLED'])->exists()) $steps++;
        if ($candidate->applications()->where('status', 'ENROLLED')->exists()) $steps++;
        return (int) round(($steps / $total) * 100);
    }

    public function dashboard(Request $request)
    {
        $candidate = $this->candidate($request);
        if (! $candidate) {
            return view('portal.dashboard', ['candidate' => null, 'progress' => 0, 'applications' => collect(), 'documents' => collect()]);
        }
        $candidate->load(['applications.university', 'applications.course', 'documents', 'appointments', 'tasks']);
        $progress = $this->progress($candidate);
        $applications = $candidate->applications;
        $documents = $candidate->documents;
        return view('portal.dashboard', compact('candidate', 'progress', 'applications', 'documents'));
    }

    public function profile(Request $request)
    {
        $candidate = $this->candidate($request);
        abort_if(! $candidate, 404);
        $candidate->load(['qualifications', 'englishTests']);
        return view('portal.profile', compact('candidate'));
    }

    public function profileUpdate(Request $request)
    {
        $candidate = $this->candidate($request);
        abort_if(! $candidate, 404);
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'passport_no' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'preferred_destination' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);
        $candidate->update($data);
        return redirect()->route('portal.profile')->with('status', 'Profile updated.');
    }

    public function applications(Request $request)
    {
        $candidate = $this->candidate($request);
        $applications = $candidate
            ? Application::with(['university', 'course'])->where('candidate_id', $candidate->id)->paginate(15)
            : collect();
        return view('portal.applications', compact('applications', 'candidate'));
    }

    public function applicationShow(Request $request, Application $application)
    {
        $candidate = $this->candidate($request);
        abort_if(! $candidate || (int) $application->candidate_id !== (int) $candidate->id, 403);
        $application->load(['university', 'course', 'statusHistory', 'offers', 'visaCases']);
        return view('portal.application-show', compact('application', 'candidate'));
    }

    public function documents(Request $request)
    {
        $candidate = $this->candidate($request);
        $documents = $candidate ? $candidate->documents()->with('documentType')->paginate(15) : collect();
        $types = DocumentType::orderBy('name')->get();
        return view('portal.documents', compact('documents', 'candidate', 'types'));
    }

    public function documentStore(Request $request)
    {
        $candidate = $this->candidate($request);
        abort_if(! $candidate, 403);
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'application_id' => 'nullable|exists:applications,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);
        DocumentService::store($request->file('file'), $candidate, $request->document_type_id, $request->application_id, $request->user()->id);
        return redirect()->route('portal.documents')->with('status', 'Document uploaded.');
    }

    public function appointments(Request $request)
    {
        $candidate = $this->candidate($request);
        $appointments = $candidate ? $candidate->appointments()->orderBy('appointment_date')->paginate(15) : collect();
        return view('portal.appointments', compact('appointments', 'candidate'));
    }

    public function appointmentStore(Request $request)
    {
        $candidate = $this->candidate($request);
        abort_if(! $candidate, 403);
        $data = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string|max:10',
            'type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        \App\Models\Appointment::create([
            'candidate_id' => $candidate->id,
            'staff_id' => $candidate->assigned_staff_id ?? \App\Models\User::whereHas('role', fn ($q) => $q->where('name', 'staff'))->value('id') ?? $request->user()->id,
            'type' => $data['type'] ?? 'Counselling',
            'appointment_date' => $data['date'].' '.substr($data['time'], 0, 5).':00',
            'status' => 'SCHEDULED',
            'notes' => $data['notes'] ?? null,
        ]);
        return redirect()->route('portal.appointments')->with('status', 'Appointment requested.');
    }

    public function tasks(Request $request)
    {
        $candidate = $this->candidate($request);
        $tasks = $candidate ? $candidate->tasks()->orderBy('due_date')->paginate(15) : collect();
        return view('portal.tasks', compact('tasks', 'candidate'));
    }

    public function notifications(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return view('portal.notifications', compact('notifications'));
    }

    public function notificationRead(Request $request, string $notification)
    {
        $n = $request->user()->notifications()->findOrFail($notification);
        $n->markAsRead();
        return redirect()->back()->with('status', 'Marked as read.');
    }
}
