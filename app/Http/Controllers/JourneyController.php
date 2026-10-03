<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CasRecord;
use App\Models\Deposit;
use App\Models\Enrolment;
use App\Models\Offer;
use App\Models\OfferCondition;
use App\Models\VisaCase;
use App\Services\ApplicationStatusService;
use App\Services\AuditService;
use Illuminate\Http\Request;

class JourneyController extends Controller
{
    public function index(Request $request)
    {
        $applications = Application::with(['candidate', 'university', 'course'])
            ->orderByDesc('updated_at')->paginate(15);
        return view('journey.index', compact('applications'));
    }

    public function offers(Request $request)
    {
        $offers = Offer::with('application.candidate')->orderByDesc('created_at')->paginate(15);
        return view('offers.index', compact('offers'));
    }

    public function offerStore(Request $request, Application $application)
    {
        $data = $request->validate([
            'offer_type' => 'required|in:Conditional,Unconditional,CONDITIONAL,UNCONDITIONAL',
            'offer_date' => 'required|date',
            'deadline' => 'nullable|date',
            'deposit_amount' => 'nullable|numeric|min:0',
            'scholarship_amount' => 'nullable|numeric|min:0',
            'scholarship_conditions' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ]);
        $data['application_id'] = $application->id;
        $offer = Offer::create($data);
        AuditService::log('offer.created', $application, null, $offer->toArray());
        return redirect()->route('applications.show', $application)->with('status', 'Offer recorded.');
    }

    public function deposits(Request $request)
    {
        $deposits = Deposit::with('application.candidate')->orderByDesc('created_at')->paginate(15);
        return view('journey.deposits', compact('deposits'));
    }

    public function depositStore(Request $request, Application $application)
    {
        $data = $request->validate([
            'required_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'transaction_ref' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
        ]);
        $data['application_id'] = $application->id;
        $deposit = Deposit::create($data);
        AuditService::log('deposit.created', $application, null, $deposit->toArray());
        return redirect()->route('applications.show', $application)->with('status', 'Deposit recorded.');
    }

    public function casIndex(Request $request)
    {
        $casRecords = CasRecord::with('application.candidate')->orderByDesc('created_at')->paginate(15);
        return view('cas.index', compact('casRecords'));
    }

    public function casStore(Request $request, Application $application)
    {
        $data = $request->validate([
            'cas_number' => 'nullable|string|max:255',
            'requested_date' => 'nullable|date',
            'issued_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'cas_fee' => 'nullable|numeric|min:0',
        ]);
        $data['application_id'] = $application->id;
        $cas = CasRecord::create($data);
        AuditService::log('cas.created', $application, null, $cas->toArray());
        return redirect()->route('applications.show', $application)->with('status', 'CAS recorded.');
    }

    public function visaIndex(Request $request)
    {
        $visas = VisaCase::with(['application', 'candidate'])->orderByDesc('created_at')->paginate(15);
        return view('visa.index', compact('visas'));
    }

    public function visaStore(Request $request, Application $application)
    {
        $data = $request->validate([
            'destination' => 'required|string|max:100',
            'visa_type' => 'nullable|string|max:100',
            'application_date' => 'nullable|date',
            'biometrics_date' => 'nullable|date',
            'interview_date' => 'nullable|date',
            'decision_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:255',
            'result' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        $data['application_id'] = $application->id;
        $data['candidate_id'] = $application->candidate_id;
        $visa = VisaCase::create($data);
        AuditService::log('visa.created', $application, null, $visa->toArray());
        return redirect()->route('applications.show', $application)->with('status', 'Visa case recorded.');
    }

    public function enrolmentIndex(Request $request)
    {
        $enrolments = Enrolment::with(['application', 'candidate'])->orderByDesc('created_at')->paginate(15);
        return view('enrolments.index', compact('enrolments'));
    }

    public function enrolmentStore(Request $request, Application $application)
    {
        $data = $request->validate([
            'campus_id' => 'nullable|integer',
            'enrolment_date' => 'required|date',
            'student_number' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
        ]);
        $data['application_id'] = $application->id;
        $data['candidate_id'] = $application->candidate_id;
        $enrolment = Enrolment::create($data);
        AuditService::log('enrolment.created', $application, null, $enrolment->toArray());
        return redirect()->route('applications.show', $application)->with('status', 'Enrolment recorded.');
    }

    public function offerShow(Request $request, $id)
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
        $offer = Offer::with(['conditions', 'application.candidate', 'application.university', 'application.course'])->findOrFail($id);
        $this->ensureOfferVisible($request, $offer);
        $requiredTotal = $offer->conditions->where('is_required', true)->count();
        $requiredDone = $offer->conditions->where('is_required', true)->where('is_completed', true)->count();
        $progress = $requiredTotal > 0 ? (int) round($requiredDone / $requiredTotal * 100) : 0;
        return view('journey.offer-show', compact('offer', 'requiredTotal', 'requiredDone', 'progress'));
    }

    public function conditionStore(Request $request, $id)
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
        $offer = Offer::with('application')->findOrFail($id);
        $this->ensureOfferVisible($request, $offer);
        $data = $request->validate([
            'condition_text' => 'required|string',
            'is_required' => 'nullable|boolean',
            'deadline' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $condition = OfferCondition::create([
            'offer_id' => $offer->id,
            'condition_text' => $data['condition_text'],
            'is_required' => $request->boolean('is_required', true),
            'is_submitted' => false,
            'is_verified' => false,
            'is_completed' => false,
            'deadline' => $data['deadline'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        AuditService::log('offer_condition.created', $offer->application, null, $condition->toArray());
        $this->maybePromoteToUnconditional($offer->fresh());
        return redirect()->back()->with('success', 'Condition added.');
    }

    public function conditionUpdate(Request $request, $id)
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
        $condition = OfferCondition::with('offer.application')->findOrFail($id);
        $this->ensureOfferVisible($request, $condition->offer);
        $data = $request->validate([
            'condition_text' => 'sometimes|string',
            'is_required' => 'nullable|boolean',
            'is_submitted' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'is_completed' => 'nullable|boolean',
            'deadline' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $old = $condition->toArray();
        $payload = array_intersect_key($data, array_flip(['condition_text', 'deadline', 'notes']));
        foreach (['is_required', 'is_submitted', 'is_verified', 'is_completed'] as $flag) {
            if ($request->has($flag)) {
                $payload[$flag] = $request->boolean($flag);
            }
        }
        if (array_key_exists('is_completed', $payload)) {
            $payload['completed_at'] = $payload['is_completed'] ? ($condition->completed_at ?? now()) : null;
        }
        if (array_key_exists('is_verified', $payload)) {
            $payload['verified_at'] = $payload['is_verified'] ? ($condition->verified_at ?? now()) : null;
        }
        $condition->update($payload);
        AuditService::log('offer_condition.updated', $condition->offer->application, $old, $condition->fresh()->toArray());
        $this->maybePromoteToUnconditional($condition->offer->fresh());
        return redirect()->back()->with('success', 'Condition updated.');
    }

    public function conditionToggle(Request $request, $id)
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
        $condition = OfferCondition::with('offer.application')->findOrFail($id);
        $this->ensureOfferVisible($request, $condition->offer);
        $old = $condition->toArray();
        $completed = ! (bool) $condition->is_completed;
        $condition->update([
            'is_completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);
        AuditService::log('offer_condition.toggled', $condition->offer->application, $old, $condition->fresh()->toArray());
        $this->maybePromoteToUnconditional($condition->offer->fresh());
        return redirect()->back()->with('success', $completed ? 'Condition marked complete.' : 'Condition reopened.');
    }

    protected function ensureOfferVisible(Request $request, Offer $offer): void
    {
        if ($request->user()?->role?->name === 'staff') {
            $userId = $request->user()->id;
            $application = $offer->application;
            $assigned = $application?->assigned_staff_id;
            $candidateAssigned = $application?->candidate?->assigned_staff_id;
            if ((int) $assigned !== (int) $userId && (int) $candidateAssigned !== (int) $userId) {
                abort(403);
            }
        }
    }

    protected function maybePromoteToUnconditional(Offer $offer): void
    {
        $type = strtoupper((string) ($offer->type ?? $offer->getAttribute('offer_type') ?? ''));
        if (! in_array($type, ['CONDITIONAL'], true)) {
            return;
        }
        $requiredTotal = $offer->conditions()->where('is_required', true)->count();
        if ($requiredTotal === 0) {
            return;
        }
        $incomplete = $offer->conditions()->where('is_required', true)->where('is_completed', false)->count();
        if ($incomplete > 0) {
            return;
        }
        $application = $offer->application;
        if (! $application || strtoupper((string) $application->status) === 'UNCONDITIONAL_OFFER') {
            return;
        }
        try {
            ApplicationStatusService::transition($application, 'UNCONDITIONAL_OFFER', 'All offer conditions completed');
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
