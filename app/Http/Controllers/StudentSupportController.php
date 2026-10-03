<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Application;
use App\Models\ApplicationFee;
use App\Models\Arrival;
use App\Models\Candidate;
use App\Models\PredepartureChecklist;
use App\Models\Sponsorship;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class StudentSupportController extends Controller
{
    public const PREDEPARTURE_STEPS = [
        'visa_approved',
        'accommodation',
        'flight',
        'airport_pickup',
        'insurance',
        'orientation',
        'documents_ready',
        'emergency_contact',
    ];

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

    protected function scopedApplications(Request $request)
    {
        $query = Application::with(['candidate', 'university', 'course']);
        if ($request->user()?->role?->name === 'staff') {
            $userId = $request->user()->id;
            $query->where(function ($qq) use ($userId) {
                $qq->where('assigned_staff_id', $userId)
                    ->orWhereHas('candidate', fn ($c) => $c->where('assigned_staff_id', $userId));
            });
        }
        return $query;
    }

    protected static function predepartureProgress(?PredepartureChecklist $checklist): int
    {
        if (! $checklist) {
            return 0;
        }
        $done = 0;
        foreach (self::PREDEPARTURE_STEPS as $step) {
            if ((bool) $checklist->{$step}) {
                $done++;
            }
        }
        return (int) round($done / count(self::PREDEPARTURE_STEPS) * 100);
    }

    // ── Accommodations ──────────────────────────────────────────────

    public function accommodationIndex(Request $request)
    {
        $this->denyCandidates($request);
        $query = Accommodation::with(['candidate', 'application']);
        if ($request->user()?->role?->name === 'staff') {
            $userId = $request->user()->id;
            $query->where(function ($qq) use ($userId) {
                $qq->whereHas('candidate', fn ($c) => $c->where('assigned_staff_id', $userId))
                    ->orWhereHas('application', fn ($a) => $a->where('assigned_staff_id', $userId));
            });
        }
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->get('candidate_id'));
        }
        $accommodations = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $candidates = $this->scopedCandidates($request)->limit(500)->get();
        $applications = $this->scopedApplications($request)->orderByDesc('created_at')->limit(500)->get();
        return view('support.accommodations', compact('accommodations', 'candidates', 'applications'));
    }

    public function accommodationStore(Request $request)
    {
        $this->denyCandidates($request);
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'application_id' => 'nullable|exists:applications,id',
            'provider' => 'nullable|string|max:255',
            'property' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date|after_or_equal:check_in',
            'booking_status' => 'nullable|string|max:50',
            'deposit' => 'nullable|numeric|min:0',
        ]);
        $candidate = $this->scopedCandidates($request)->findOrFail($data['candidate_id']);
        $accommodation = Accommodation::create($data);
        AuditService::log('accommodation.created', $candidate, null, $accommodation->toArray());
        return redirect()
            ->to(Route::has('support.accommodations') ? route('support.accommodations', ['candidate_id' => $candidate->id]) : url('/support/accommodations'))
            ->with('success', 'Accommodation recorded.');
    }

    public function accommodationUpdate(Request $request, $id)
    {
        $this->denyCandidates($request);
        $accommodation = Accommodation::findOrFail($id);
        $data = $request->validate([
            'candidate_id' => 'sometimes|exists:candidates,id',
            'application_id' => 'nullable|exists:applications,id',
            'provider' => 'nullable|string|max:255',
            'property' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date|after_or_equal:check_in',
            'booking_status' => 'nullable|string|max:50',
            'deposit' => 'nullable|numeric|min:0',
        ]);
        if (isset($data['candidate_id'])) {
            $this->scopedCandidates($request)->findOrFail($data['candidate_id']);
        }
        $old = $accommodation->toArray();
        $accommodation->update($data);
        AuditService::log('accommodation.updated', $accommodation->candidate, $old, $accommodation->fresh()->toArray());
        return redirect()->back()->with('success', 'Accommodation updated.');
    }

    public function accommodationDestroy(Request $request, $id)
    {
        $this->denyCandidates($request);
        $accommodation = Accommodation::findOrFail($id);
        $candidate = $accommodation->candidate;
        $accommodation->delete();
        AuditService::log('accommodation.deleted', $candidate);
        return redirect()->back()->with('success', 'Accommodation deleted.');
    }

    // ── Pre-departure checklist ─────────────────────────────────────

    public function predepartureShow(Request $request, $id)
    {
        $this->denyCandidates($request);
        $candidate = $this->scopedCandidates($request)->findOrFail($id);
        $checklist = PredepartureChecklist::firstOrCreate(['candidate_id' => $candidate->id]);
        $progress = self::predepartureProgress($checklist);
        $steps = self::PREDEPARTURE_STEPS;
        $candidates = $this->scopedCandidates($request)->limit(500)->get();
        return view('support.predeparture', compact('candidate', 'checklist', 'progress', 'steps', 'candidates'));
    }

    public function predepartureUpdate(Request $request, $id)
    {
        $this->denyCandidates($request);
        $candidate = $this->scopedCandidates($request)->findOrFail($id);
        $checklist = PredepartureChecklist::firstOrCreate(['candidate_id' => $candidate->id]);
        $data = [];
        foreach (self::PREDEPARTURE_STEPS as $step) {
            $data[$step] = $request->boolean($step);
        }
        $old = $checklist->toArray();
        $checklist->update($data);
        AuditService::log('predeparture.updated', $candidate, $old, $checklist->fresh()->toArray());
        return redirect()->back()->with('success', 'Pre-departure checklist updated.');
    }

    // ── Arrival ─────────────────────────────────────────────────────

    public function arrivalShow(Request $request, $id)
    {
        $this->denyCandidates($request);
        $candidate = $this->scopedCandidates($request)->findOrFail($id);
        $arrival = Arrival::firstOrCreate(['candidate_id' => $candidate->id]);
        $candidates = $this->scopedCandidates($request)->limit(500)->get();
        return view('support.arrival', compact('candidate', 'arrival', 'candidates'));
    }

    public function arrivalUpdate(Request $request, $id)
    {
        $this->denyCandidates($request);
        $candidate = $this->scopedCandidates($request)->findOrFail($id);
        $arrival = Arrival::firstOrCreate(['candidate_id' => $candidate->id]);
        $data = $request->validate([
            'arrival_date' => 'nullable|date',
            'brp_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $data['arrived'] = $request->boolean('arrived');
        $data['university_registered'] = $request->boolean('university_registered');
        $data['accommodation_confirmed'] = $request->boolean('accommodation_confirmed');
        $old = $arrival->toArray();
        $arrival->update($data);
        AuditService::log('arrival.updated', $candidate, $old, $arrival->fresh()->toArray());
        return redirect()->back()->with('success', 'Arrival record updated.');
    }

    // ── Sponsorships ────────────────────────────────────────────────

    public function sponsorshipIndex(Request $request)
    {
        $this->denyCandidates($request);
        $query = Sponsorship::with('candidate');
        if ($request->user()?->role?->name === 'staff') {
            $query->whereHas('candidate', fn ($q) => $q->where('assigned_staff_id', $request->user()->id));
        }
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->get('candidate_id'));
        }
        $sponsorships = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $candidates = $this->scopedCandidates($request)->limit(500)->get();
        return view('support.sponsorships', compact('sponsorships', 'candidates'));
    }

    public function sponsorshipStore(Request $request)
    {
        $this->denyCandidates($request);
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'sponsor_name' => 'required|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'income' => 'nullable|numeric|min:0',
            'contact' => 'nullable|string|max:255',
            'funding_amount' => 'nullable|numeric|min:0',
            'evidence_path' => 'nullable|string|max:255',
        ]);
        $candidate = $this->scopedCandidates($request)->findOrFail($data['candidate_id']);
        $data['verified'] = $request->boolean('verified');
        $sponsorship = Sponsorship::create($data);
        AuditService::log('sponsorship.created', $candidate, null, $sponsorship->toArray());
        return redirect()
            ->to(Route::has('support.sponsorships') ? route('support.sponsorships', ['candidate_id' => $candidate->id]) : url('/support/sponsorships'))
            ->with('success', 'Sponsorship recorded.');
    }

    public function sponsorshipUpdate(Request $request, $id)
    {
        $this->denyCandidates($request);
        $sponsorship = Sponsorship::findOrFail($id);
        $data = $request->validate([
            'candidate_id' => 'sometimes|exists:candidates,id',
            'sponsor_name' => 'sometimes|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'income' => 'nullable|numeric|min:0',
            'contact' => 'nullable|string|max:255',
            'funding_amount' => 'nullable|numeric|min:0',
            'evidence_path' => 'nullable|string|max:255',
        ]);
        if (isset($data['candidate_id'])) {
            $this->scopedCandidates($request)->findOrFail($data['candidate_id']);
        }
        $data['verified'] = $request->boolean('verified');
        $old = $sponsorship->toArray();
        $sponsorship->update($data);
        AuditService::log('sponsorship.updated', $sponsorship->candidate, $old, $sponsorship->fresh()->toArray());
        return redirect()->back()->with('success', 'Sponsorship updated.');
    }

    public function sponsorshipDestroy(Request $request, $id)
    {
        $this->denyCandidates($request);
        $sponsorship = Sponsorship::findOrFail($id);
        $candidate = $sponsorship->candidate;
        $sponsorship->delete();
        AuditService::log('sponsorship.deleted', $candidate);
        return redirect()->back()->with('success', 'Sponsorship deleted.');
    }

    // ── Application fees ────────────────────────────────────────────

    public function feeIndex(Request $request)
    {
        $this->denyCandidates($request);
        $query = ApplicationFee::with('application.candidate');
        if ($request->user()?->role?->name === 'staff') {
            $userId = $request->user()->id;
            $query->whereHas('application', function ($qq) use ($userId) {
                $qq->where('assigned_staff_id', $userId)
                    ->orWhereHas('candidate', fn ($c) => $c->where('assigned_staff_id', $userId));
            });
        }
        if ($request->filled('application_id')) {
            $query->where('application_id', $request->get('application_id'));
        }
        $fees = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $applications = $this->scopedApplications($request)->orderByDesc('created_at')->limit(500)->get();
        return view('support.fees', compact('fees', 'applications'));
    }

    public function feeStore(Request $request)
    {
        $this->denyCandidates($request);
        $data = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:50',
            'payment_date' => 'nullable|date',
            'receipt' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $application = $this->scopedApplications($request)->findOrFail($data['application_id']);
        $fee = ApplicationFee::create([
            'application_id' => $application->id,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'GBP',
            'status' => $data['status'] ?? 'pending',
            'payment_date' => $data['payment_date'] ?? null,
            'receipt_path' => $data['receipt'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        AuditService::log('application_fee.created', $application, null, $fee->toArray());
        return redirect()->back()->with('success', 'Application fee recorded.');
    }

    public function feeUpdate(Request $request, $id)
    {
        $this->denyCandidates($request);
        $fee = ApplicationFee::findOrFail($id);
        $data = $request->validate([
            'application_id' => 'sometimes|exists:applications,id',
            'amount' => 'sometimes|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:50',
            'payment_date' => 'nullable|date',
            'receipt' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        if (isset($data['application_id'])) {
            $this->scopedApplications($request)->findOrFail($data['application_id']);
        }
        $mapped = $data;
        if (array_key_exists('receipt', $data)) {
            $mapped['receipt_path'] = $data['receipt'];
            unset($mapped['receipt']);
        }
        $old = $fee->toArray();
        $fee->update($mapped);
        AuditService::log('application_fee.updated', $fee->application, $old, $fee->fresh()->toArray());
        return redirect()->back()->with('success', 'Application fee updated.');
    }
}
