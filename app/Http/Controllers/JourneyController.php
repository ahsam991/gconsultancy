<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CasRecord;
use App\Models\Deposit;
use App\Models\Enrolment;
use App\Models\Offer;
use App\Models\VisaCase;
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
}
