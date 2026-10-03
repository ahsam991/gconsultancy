<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CounsellingSession;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CounsellingController extends Controller
{
    protected function denyCandidates(Request $request): void
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
    }

    protected function scopedCandidates(Request $request)
    {
        $query = Candidate::query()->orderBy('first_name')->orderBy('last_name');
        if ($request->user()?->role?->name === 'staff') {
            $query->where('assigned_staff_id', $request->user()->id);
        }
        return $query;
    }

    protected function scopedSessions(Request $request)
    {
        $query = CounsellingSession::with(['candidate', 'staff']);
        if ($request->user()?->role?->name === 'staff') {
            $query->whereHas('candidate', fn ($q) => $q->where('assigned_staff_id', $request->user()->id));
        }
        return $query;
    }

    protected function ensureVisible(Request $request, CounsellingSession $session): void
    {
        if ($request->user()?->role?->name === 'staff'
            && (int) $session->candidate?->assigned_staff_id !== (int) $request->user()->id) {
            abort(403);
        }
    }

    protected function rules(): array
    {
        return [
            'candidate_id' => 'required|exists:candidates,id',
            'session_date' => 'required|date',
            'purpose' => 'required|string|max:255',
            'discussion' => 'required|string',
            'recommendation' => 'nullable|string',
            'preferred_destination' => 'nullable|string|max:100',
            'preferred_level' => 'nullable|string|max:100',
            'preferred_subject' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'ielts' => 'nullable|string|max:50',
            'next_action' => 'nullable|string|max:255',
            'followup_date' => 'nullable|date|after_or_equal:session_date',
            'status' => 'nullable|string|max:50',
        ];
    }

    public function index(Request $request)
    {
        $this->denyCandidates($request);
        $query = $this->scopedSessions($request);
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->get('candidate_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $sessions = $query->orderByDesc('session_date')->paginate(15)->withQueryString();
        $candidates = $this->scopedCandidates($request)->limit(500)->get();
        return view('counselling.index', compact('sessions', 'candidates'));
    }

    public function create(Request $request)
    {
        $this->denyCandidates($request);
        $candidates = $this->scopedCandidates($request)->limit(500)->get();
        return view('counselling.create', compact('candidates'));
    }

    public function store(Request $request)
    {
        $this->denyCandidates($request);
        $data = $request->validate($this->rules());
        $candidate = $this->scopedCandidates($request)->findOrFail($data['candidate_id']);
        $data['staff_id'] = $request->user()->id;
        $session = CounsellingSession::create($data);
        AuditService::log('counselling.created', $candidate, null, $session->toArray());
        return redirect()
            ->to(Route::has('counselling.edit') ? route('counselling.edit', $session) : url('/counselling'))
            ->with('success', 'Counselling session recorded.');
    }

    public function show(Request $request, CounsellingSession $counselling)
    {
        $this->denyCandidates($request);
        $this->ensureVisible($request, $counselling);
        return redirect()
            ->to(Route::has('counselling.edit') ? route('counselling.edit', $counselling) : url('/counselling'));
    }

    public function edit(Request $request, CounsellingSession $counselling)
    {
        $this->denyCandidates($request);
        $this->ensureVisible($request, $counselling);
        $candidates = $this->scopedCandidates($request)->limit(500)->get();
        $session = $counselling;
        return view('counselling.edit', compact('session', 'candidates'));
    }

    public function update(Request $request, CounsellingSession $counselling)
    {
        $this->denyCandidates($request);
        $this->ensureVisible($request, $counselling);
        $data = $request->validate($this->rules());
        $this->scopedCandidates($request)->findOrFail($data['candidate_id']);
        $old = $counselling->toArray();
        $counselling->update($data);
        AuditService::log('counselling.updated', $counselling->candidate, $old, $counselling->fresh()->toArray());
        return redirect()
            ->to(Route::has('counselling.edit') ? route('counselling.edit', $counselling) : url('/counselling'))
            ->with('success', 'Counselling session updated.');
    }

    public function destroy(Request $request, CounsellingSession $counselling)
    {
        $this->denyCandidates($request);
        $this->ensureVisible($request, $counselling);
        $candidate = $counselling->candidate;
        $counselling->delete();
        AuditService::log('counselling.deleted', $candidate);
        return redirect()
            ->to(Route::has('counselling.index') ? route('counselling.index') : url('/counselling'))
            ->with('success', 'Counselling session deleted.');
    }
}
