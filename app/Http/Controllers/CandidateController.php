<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidateRequest;
use App\Models\AcademicQualification;
use App\Models\Candidate;
use App\Models\DocumentType;
use App\Models\EmergencyContact;
use App\Models\EnglishTest;
use App\Models\User;
use App\Services\AuditService;
use App\Services\UIDService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CandidateController extends Controller
{
    use AuthorizesRequests;

    protected function scopedQuery(Request $request)
    {
        $user = $request->user();
        $role = $user->role?->name;
        $q = Candidate::with(['assignedStaff', 'applications']);
        if ($role === 'staff') {
            $q->where('assigned_staff_id', $user->id);
        }
        return $q;
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Candidate::class);
        $query = $this->scopedQuery($request);

        if ($s = $request->get('q')) {
            $query->where(function ($qq) use ($s) {
                $qq->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('uid', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('destination')) {
            $query->where('preferred_destination', $request->get('destination'));
        }
        if ($request->filled('staff') && in_array($request->user()->role?->name, ['admin', 'manager'], true)) {
            $query->where('assigned_staff_id', $request->get('staff'));
        }

        $candidates = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('candidates.index', compact('candidates'));
    }

    public function create()
    {
        Gate::authorize('create', Candidate::class);
        $staff = User::whereHas('role', fn ($q) => $q->where('name', 'staff'))->get();
        return view('candidates.create', compact('staff'));
    }

    public function store(StoreCandidateRequest $request)
    {
        Gate::authorize('create', Candidate::class);
        $data = $request->validated();

        $dup = Candidate::where('email', $data['email'])->orWhere('phone', $data['phone'])->first();
        if ($dup) {
            return redirect()->back()->withInput()->with('warning', 'Possible duplicate: '.$dup->uid.' ('.$dup->email.')');
        }

        $data['uid'] = UIDService::candidateUid();
        $data['created_by'] = $request->user()->id;
        $candidate = Candidate::create($data);

        AuditService::log('candidate.created', $candidate, null, $candidate->toArray());

        return redirect()->route('candidates.show', $candidate)->with('status', 'Candidate created: '.$candidate->uid);
    }

    public function show(Request $request, Candidate $candidate)
    {
        $this->authorize('view', $candidate);
        $candidate->load(['applications.university', 'applications.course', 'documents.documentType', 'tasks', 'appointments']);
        $timeline = $candidate->applications()->with('statusHistory.changedBy')->get();
        return view('candidates.show', compact('candidate', 'timeline'));
    }

    public function edit(Candidate $candidate)
    {
        $this->authorize('update', $candidate);
        $staff = User::whereHas('role', fn ($q) => $q->where('name', 'staff'))->get();
        return view('candidates.edit', compact('candidate', 'staff'));
    }

    public function update(StoreCandidateRequest $request, Candidate $candidate)
    {
        $this->authorize('update', $candidate);
        $old = $candidate->toArray();
        $candidate->update($request->validated());
        AuditService::log('candidate.updated', $candidate, $old, $candidate->fresh()->toArray());
        return redirect()->route('candidates.show', $candidate)->with('status', 'Candidate updated.');
    }

    public function destroy(Candidate $candidate)
    {
        $this->authorize('delete', $candidate);
        $candidate->delete();
        AuditService::log('candidate.deleted', $candidate);
        return redirect()->route('candidates.index')->with('status', 'Candidate deleted.');
    }

    public function bulkAssign(Request $request)
    {
        $ids = $request->input('candidate_ids', $request->input('ids', []));
        $staffId = $request->input('assigned_staff_id', $request->input('assigned_to'));
        $request->merge(['candidate_ids' => $ids, 'assigned_staff_id' => $staffId]);
        $request->validate([
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidates,id',
            'assigned_staff_id' => 'required|exists:users,id',
        ]);
        Candidate::whereIn('id', $ids)->update(['assigned_staff_id' => $staffId]);
        AuditService::log('candidate.bulk_assign', null, null, ['candidate_ids' => $ids, 'assigned_staff_id' => $staffId]);
        return redirect()->route('candidates.index')->with('status', 'Candidates assigned.');
    }

    public function bulkStatus(Request $request)
    {
        $ids = $request->input('candidate_ids', $request->input('ids', []));
        $request->merge(['candidate_ids' => $ids]);
        $request->validate([
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidates,id',
            'status' => 'required|string|max:50',
        ]);
        Candidate::whereIn('id', $ids)->update(['status' => strtoupper($request->status)]);
        AuditService::log('candidate.bulk_status', null, null, ['candidate_ids' => $ids, 'status' => $request->status]);
        return redirect()->route('candidates.index')->with('status', 'Candidate statuses updated.');
    }

    public function archive(Request $request)
    {
        $ids = $request->input('candidate_ids', $request->input('ids', []));
        $request->merge(['candidate_ids' => $ids]);
        $request->validate(['candidate_ids' => 'required|array', 'candidate_ids.*' => 'exists:candidates,id']);
        Candidate::whereIn('id', $ids)->delete();
        AuditService::log('candidate.archived', null, null, ['candidate_ids' => $ids]);
        return redirect()->route('candidates.index')->with('status', 'Candidates archived.');
    }

    public function archived(Request $request)
    {
        Gate::authorize('viewAny', Candidate::class);
        $candidates = Candidate::onlyTrashed()->orderByDesc('deleted_at')->paginate(15);
        return view('candidates.archived', compact('candidates'));
    }

    public function restore($id)
    {
        $candidate = Candidate::onlyTrashed()->findOrFail($id);
        $this->authorize('update', $candidate);
        $candidate->restore();
        AuditService::log('candidate.restored', $candidate);
        return redirect()->route('candidates.archived')->with('status', 'Candidate restored.');
    }

    public function import(Request $request)
    {
        $preview = $request->session()->get('candidate_import_preview', []);
        return view('candidates.import', compact('preview'));
    }

    public function importStore(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
        $rows = array_map('str_getcsv', file($request->file('file')->getRealPath()));
        $header = array_map('trim', array_shift($rows));
        // Legacy CSV header aliases mapped to canonical DB columns (single source of truth: migrations).
        $aliases = ['preferred_country' => 'preferred_destination', 'current_status' => 'status'];
        $header = array_map(fn ($h) => $aliases[$h] ?? $h, $header);
        $preview = [];
        foreach (array_slice($rows, 0, 50) as $row) {
            if (count($row) === count($header)) {
                $preview[] = array_combine($header, $row);
            }
        }
        $request->session()->put('candidate_import_preview', $preview);
        return redirect()->route('candidates.import')->with('status', count($preview).' rows ready for preview.');
    }

    public function importConfirm(Request $request)
    {
        $preview = $request->session()->get('candidate_import_preview', []);
        $count = 0;
        foreach ($preview as $row) {
            if (empty($row['email'] ?? null) || empty($row['first_name'] ?? null)) {
                continue;
            }
            if (Candidate::where('email', $row['email'])->exists()) {
                continue;
            }
            Candidate::create([
                'uid' => UIDService::candidateUid(),
                'first_name' => $row['first_name'] ?? '',
                'last_name' => $row['last_name'] ?? '',
                'email' => $row['email'],
                'phone' => $row['phone'] ?? ('import-'.uniqid()),
                'preferred_destination' => $row['preferred_destination'] ?? null,
                'status' => 'NEW',
                'created_by' => $request->user()->id,
            ]);
            $count++;
        }
        $request->session()->forget('candidate_import_preview');
        AuditService::log('candidate.imported', null, null, ['count' => $count]);
        return redirect()->route('candidates.index')->with('status', "Imported {$count} candidates.");
    }

    public function export(Request $request)
    {
        $rows = $this->scopedQuery($request)->limit(5000)->get();
        $filename = 'candidates-'.now()->format('Ymd-His').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];
        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['uid', 'first_name', 'last_name', 'email', 'phone', 'preferred_destination', 'status']);
            foreach ($rows as $c) {
                fputcsv($out, [$c->uid, $c->first_name, $c->last_name, $c->email, $c->phone, $c->preferred_destination, $c->status]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public static function wizardSteps(): array
    {
        return [1 => 'Personal Info', 2 => 'Academic', 3 => 'English Test', 4 => 'Emergency Contact', 5 => 'Documents', 6 => 'Review & Submit'];
    }

    public function wizard(Candidate $candidate, $step = 1)
    {
        $this->authorize('update', $candidate);
        $step = max(1, min(6, (int) $step));
        $candidate->load(['qualifications', 'englishTests', 'emergencyContacts', 'documents.documentType', 'addresses']);
        $completion = $this->completion($candidate);
        $steps = self::wizardSteps();
        $staff = User::whereHas('role', fn ($q) => $q->whereIn('name', ['staff', 'manager']))->orderBy('name')->get();
        $types = DocumentType::active()->orderBy('name')->get();
        return view('candidates.wizard', compact('candidate', 'step', 'steps', 'completion', 'staff', 'types'));
    }

    public function wizardStore(Request $request, Candidate $candidate, $step)
    {
        $this->authorize('update', $candidate);
        $step = max(1, min(6, (int) $step));
        $draft = $request->boolean('save_draft');

        if ($step === 1) {
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'dob' => 'nullable|date|before:today',
                'gender' => 'nullable|string|max:20',
                'nationality' => 'nullable|string|max:100',
                'passport_no' => 'nullable|string|max:50',
                'passport_expiry' => 'nullable|date|after:today',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'preferred_destination' => 'nullable|string|max:100',
                'preferred_level' => 'nullable|string|max:100',
                'preferred_subject' => 'nullable|string|max:255',
                'referral_source' => 'nullable|string|max:255',
                'assigned_staff_id' => 'nullable|exists:users,id',
            ]);
            $candidate->update($data);
        } elseif ($step === 2) {
            $data = $request->validate([
                'level' => 'required|string|max:100',
                'institution' => 'required|string|max:255',
                'country' => 'nullable|string|max:100',
                'passing_year' => 'nullable|integer|min:1980|max:2100',
                'result' => 'nullable|string|max:100',
                'grading_scale' => 'nullable|string|max:100',
            ]);
            $candidate->qualifications()->create($data);
        } elseif ($step === 3) {
            $data = $request->validate([
                'test_type' => 'required|string|max:50',
                'overall' => 'nullable|numeric|min:0|max:9',
                'listening' => 'nullable|numeric|min:0|max:9',
                'reading' => 'nullable|numeric|min:0|max:9',
                'writing' => 'nullable|numeric|min:0|max:9',
                'speaking' => 'nullable|numeric|min:0|max:9',
                'test_date' => 'nullable|date|before_or_equal:today',
                'expiry_date' => 'nullable|date|after:test_date',
            ]);
            $candidate->englishTests()->create($data);
        } elseif ($step === 4) {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'relationship' => 'required|string|max:100',
                'phone' => 'required|string|max:50',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
            ]);
            $candidate->emergencyContacts()->updateOrCreate(
                ['candidate_id' => $candidate->id, 'phone' => $data['phone']],
                $data
            );
        } elseif ($step === 6 && ! $draft) {
            $candidate->update(['profile_completion' => $this->completion($candidate->fresh())]);
            if ($candidate->status === 'NEW') {
                $candidate->update(['status' => 'PROFILE_PENDING']);
            }
            AuditService::log('candidate.profile_submitted', $candidate);
            return redirect()->route('candidates.show', $candidate)->with('status', 'Profile submitted.');
        }

        $candidate->update(['profile_completion' => $this->completion($candidate->fresh())]);
        AuditService::log('candidate.wizard_step', $candidate, null, ['step' => $step, 'draft' => $draft]);
        $next = $draft ? $step : min(6, $step + 1);
        return redirect()->route('candidates.wizard', [$candidate, $next])->with('status', $draft ? 'Draft saved.' : 'Step saved. Continue.');
    }

    protected function completion(Candidate $candidate): int
    {
        $checks = [
            filled($candidate->first_name) && filled($candidate->phone),
            $candidate->qualifications()->exists(),
            $candidate->englishTests()->exists(),
            $candidate->emergencyContacts()->exists(),
            $candidate->documents()->exists(),
        ];
        return (int) round(collect($checks)->filter()->count() / count($checks) * 100);
    }
}
