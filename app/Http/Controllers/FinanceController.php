<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Services\AuditService;
use App\Services\CommissionService;
use App\Services\UIDService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function commissions(Request $request)
    {
        $query = Commission::with(['application', 'candidate', 'university']);
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $commissions = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('finance.commissions.index', compact('commissions'));
    }

    public function claimCommission(Request $request, Commission $commission)
    {
        $commission->update(['status' => 'CLAIMED', 'claimed_date' => now()->toDateString()]);
        AuditService::log('commission.claimed', $commission->application, null, $commission->toArray());
        return redirect()->back()->with('status', 'Commission claimed.');
    }

    public function receiveCommission(Request $request, Commission $commission)
    {
        $commission->update(['status' => 'FULLY_RECEIVED', 'received_date' => now()->toDateString()]);
        AuditService::log('commission.received', $commission->application, null, $commission->toArray());
        return redirect()->back()->with('status', 'Commission marked received.');
    }

    public function invoices(Request $request)
    {
        $invoices = Invoice::with(['university', 'candidate', 'application'])
            ->orderByDesc('created_at')->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function invoiceCreate()
    {
        $commissions = Commission::with(['university', 'candidate'])->orderByDesc('id')->limit(200)->get();
        return view('invoices.form', compact('commissions'));
    }

    public function invoiceStore(Request $request)
    {
        $data = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'candidate_id' => 'required|exists:candidates,id',
            'application_id' => 'required|exists:applications,id',
            'commission_id' => 'nullable|exists:commissions,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string',
            'items.*.amount' => 'required_with:items|numeric|min:0',
        ]);
        $data['invoice_number'] = UIDService::invoiceNumber();
        $data['tax_amount'] = $data['tax_amount'] ?? 0;
        $data['total'] = $data['subtotal'] + $data['tax_amount'];
        $data['status'] = 'PENDING';
        $invoice = Invoice::create($data);
        if (! empty($data['items'])) {
            foreach ($data['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                ]);
            }
        }
        AuditService::log('invoice.created', null, null, $invoice->toArray());
        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice created: '.$invoice->invoice_number);
    }

    public function invoiceShow(Invoice $invoice)
    {
        $invoice->load(['items', 'payments', 'university', 'candidate', 'application']);
        return view('invoices.show', compact('invoice'));
    }

    public function invoicePdf(Request $request, Invoice $invoice)
    {
        $invoice->load(['items', 'university', 'candidate', 'application']);
        if ($request->get('preview')) {
            return view('finance.invoice-pdf', compact('invoice'));
        }
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('finance.invoice-pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');
        AuditService::log('invoice.pdf', null, null, ['id' => $invoice->id, 'number' => $invoice->invoice_number]);
        return $pdf->download(($invoice->invoice_number ?? 'invoice-'.$invoice->id).'.pdf');
    }

    public function paymentStore(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string|max:50',
            'transaction_ref' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
        ]);
        $data['status'] = 'COMPLETED';
        $payment = Payment::create($data);
        $invoice = Invoice::findOrFail($data['invoice_id']);
        $paid = (float) $invoice->payments()->where('status', 'COMPLETED')->sum('amount');
        if ($paid >= (float) $invoice->total) {
            $invoice->update(['status' => 'PAID', 'paid_at' => now()]);
        } elseif ($paid > 0) {
            $invoice->update(['status' => 'PARTIAL']);
        }
        AuditService::log('payment.recorded', null, null, $payment->toArray());
        return redirect()->route('invoices.show', $invoice)->with('status', 'Payment recorded.');
    }

    /* ------------------------------------------------------------------
     | Referral partners + partner payments (added, existing untouched)
     |------------------------------------------------------------------ */

    protected function blockCandidate(Request $request): void
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
    }

    public function referrals(Request $request)
    {
        $this->blockCandidate($request);
        $partners = \App\Models\ReferralPartner::withCount('payments')
            ->when($request->filled('q'), function ($q) use ($request) {
                $s = $request->get('q');
                $q->where(fn ($qq) => $qq->where('name', 'like', "%{$s}%")->orWhere('company', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            })
            ->when($request->filled('active'), fn ($q) => $q->where('active', (bool) $request->get('active')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        $payments = \App\Models\ReferralPayment::with(['commission.candidate', 'referralPartner'])
            ->when($request->filled('partner_id'), fn ($q) => $q->where('referral_partner_id', $request->get('partner_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->get('status')))
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'pay_page')
            ->withQueryString();
        $commissions = \App\Models\Commission::with(['candidate', 'university'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('finance.referrals', compact('partners', 'payments', 'commissions'));
    }

    public function referralStore(Request $request)
    {
        $this->blockCandidate($request);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'commission_share_percent' => 'required|numeric|min:0|max:100',
            'type' => 'nullable|string|max:50',
            'active' => 'nullable|boolean',
        ]);
        $data['type'] = $data['type'] ?? 'Agent';
        $data['active'] = array_key_exists('active', $data) ? (bool) $data['active'] : true;
        $partner = \App\Models\ReferralPartner::create($data);
        AuditService::log('referral.created', null, null, $partner->toArray());

        return redirect()->back()->with('status', 'Referral partner added.');
    }

    public function referralUpdate(Request $request, \App\Models\ReferralPartner $referralPartner)
    {
        $this->blockCandidate($request);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'commission_share_percent' => 'sometimes|required|numeric|min:0|max:100',
            'type' => 'nullable|string|max:50',
            'active' => 'nullable|boolean',
        ]);
        if (array_key_exists('active', $data)) {
            $data['active'] = (bool) $data['active'];
        }
        $referralPartner->update($data);
        AuditService::log('referral.updated', null, null, $referralPartner->fresh()->toArray());

        return redirect()->back()->with('status', 'Referral partner updated.');
    }

    public function referralDestroy(Request $request, \App\Models\ReferralPartner $referralPartner)
    {
        $this->blockCandidate($request);
        $old = $referralPartner->toArray();
        $referralPartner->delete();
        AuditService::log('referral.deleted', null, $old, null);

        return redirect()->back()->with('status', 'Referral partner removed.');
    }

    public function payPartner(Request $request)
    {
        $this->blockCandidate($request);
        $data = $request->validate([
            'referral_partner_id' => 'required|exists:referral_partners,id',
            'commission_id' => 'required|exists:commissions,id',
            'share_percent' => 'required|numeric|min:0|max:100',
        ]);
        $commission = \App\Models\Commission::findOrFail($data['commission_id']);
        $shareAmount = round(((float) $commission->amount) * ((float) $data['share_percent'] / 100), 2);
        $payment = \App\Models\ReferralPayment::create([
            'commission_id' => $commission->id,
            'referral_partner_id' => $data['referral_partner_id'],
            'share_percent' => $data['share_percent'],
            'share_amount' => $shareAmount,
            'status' => 'pending',
        ]);
        // Link commission to partner without touching $fillable.
        $commission->forceFill(['referral_partner_id' => $data['referral_partner_id']])->save();
        AuditService::log('referral.paid_pending', $commission->application, null, $payment->toArray());

        return redirect()->back()->with('status', 'Partner share recorded as pending: '.$shareAmount);
    }

    public function markPaid(Request $request, \App\Models\ReferralPayment $referralPayment)
    {
        $this->blockCandidate($request);
        $referralPayment->update(['status' => 'paid', 'paid_at' => now()]);
        AuditService::log('referral.paid', null, null, $referralPayment->fresh()->toArray());

        return redirect()->back()->with('status', 'Referral payment marked paid.');
    }

    /* ------------------------------------------------------------------
     | Student payments (candidate/application scoped + receipt upload)
     |------------------------------------------------------------------ */

    public function studentPayments(Request $request)
    {
        $this->blockCandidate($request);
        $payments = \App\Models\StudentPayment::with(['candidate', 'application'])
            ->when($request->filled('candidate_id'), fn ($q) => $q->where('candidate_id', $request->get('candidate_id')))
            ->when($request->filled('application_id'), fn ($q) => $q->where('application_id', $request->get('application_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->get('status')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();
        $candidates = \App\Models\Candidate::orderBy('first_name')->limit(200)->get(['id', 'first_name', 'last_name', 'uid']);
        $applications = \App\Models\Application::orderByDesc('id')->limit(200)->get(['id', 'uid', 'candidate_id']);

        return view('finance.student-payments', compact('payments', 'candidates', 'applications'));
    }

    public function studentPaymentStore(Request $request)
    {
        $this->blockCandidate($request);
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'application_id' => 'nullable|exists:applications,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|max:10',
            'purpose' => 'required|string|max:100',
            'status' => 'nullable|string|max:50',
            'payment_date' => 'nullable|date',
            'transaction_ref' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);
        $payload = [
            'candidate_id' => $data['candidate_id'],
            'application_id' => $data['application_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'GBP',
            'purpose' => $data['purpose'],
            'status' => $data['status'] ?? 'pending',
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'transaction_ref' => $data['transaction_ref'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
        if ($request->hasFile('receipt')) {
            $payload['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }
        $payment = \App\Models\StudentPayment::create($payload);
        AuditService::log('student_payment.created', null, null, $payment->toArray());

        return redirect()->back()->with('status', 'Student payment recorded.');
    }

    public function studentPaymentUpdate(Request $request, \App\Models\StudentPayment $studentPayment)
    {
        $this->blockCandidate($request);
        $data = $request->validate([
            'amount' => 'sometimes|required|numeric|min:0.01',
            'currency' => 'nullable|string|max:10',
            'purpose' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'payment_date' => 'nullable|date',
            'transaction_ref' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);
        $payload = array_intersect_key($data, array_flip(['amount', 'currency', 'purpose', 'status', 'payment_date', 'transaction_ref', 'notes']));
        if ($request->hasFile('receipt')) {
            $payload['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }
        $studentPayment->update($payload);
        AuditService::log('student_payment.updated', null, null, $studentPayment->fresh()->toArray());

        return redirect()->back()->with('status', 'Student payment updated.');
    }

    /* ------------------------------------------------------------------
     | Clawback: commission -> CLAWED_BACK + related invoice -> CLAWED_BACK
     |------------------------------------------------------------------ */

    public function clawback(Request $request, \App\Models\Commission $commission)
    {
        $this->blockCandidate($request);
        $data = $request->validate([
            'clawback_amount' => 'required|numeric|min:0.01',
            'clawback_reason' => 'required|string|max:1000',
            'clawback_deadline' => 'nullable|date|after_or_equal:today',
        ]);
        // Bypass $fillable (strict ownership: do not edit the model).
        $commission->forceFill([
            'status' => 'CLAWED_BACK',
            'clawback_amount' => $data['clawback_amount'],
            'clawback_reason' => $data['clawback_reason'],
            'clawback_deadline' => $data['clawback_deadline'] ?? null,
        ])->save();
        \App\Models\Invoice::where('commission_id', $commission->id)->update(['status' => 'CLAWED_BACK']);
        AuditService::log('commission.clawed_back', $commission->application, null, $commission->fresh()->toArray());

        return redirect()->back()->with('status', 'Commission marked CLAWED_BACK.');
    }

    /* ------------------------------------------------------------------
     | Revenue overview: cards + Chart.js datasets
     |------------------------------------------------------------------ */

    public function revenue(Request $request)
    {
        $this->blockCandidate($request);
        $claimed = (float) \App\Models\Commission::where('status', 'CLAIMED')->sum('amount');
        $received = (float) \App\Models\Commission::where('status', 'FULLY_RECEIVED')->sum('amount');
        $outstanding = (float) \App\Models\Commission::whereIn('status', ['PENDING', 'READY_TO_CLAIM', 'DUE'])->sum('amount');
        $expected = $claimed + $outstanding;
        $clawed = (float) \App\Models\Commission::where('status', 'CLAWED_BACK')->sum('clawback_amount');
        if ($clawed <= 0) {
            $clawed = (float) \App\Models\Commission::where('status', 'CLAWED_BACK')->sum('amount');
        }
        $cards = [
            'claimed' => $claimed,
            'received' => $received,
            'outstanding' => $outstanding,
            'expected' => $expected,
            'clawed' => $clawed,
        ];

        $byUniversity = \App\Models\Commission::selectRaw('universities.name as label, SUM(commissions.amount) as total')
            ->join('universities', 'universities.id', '=', 'commissions.university_id')
            ->groupBy('universities.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $byCountry = \App\Models\Commission::selectRaw('COALESCE(countries.name, ?) as label, SUM(commissions.amount) as total', ['Unknown'])
            ->join('universities', 'universities.id', '=', 'commissions.university_id')
            ->leftJoin('countries', 'countries.id', '=', 'universities.country_id')
            ->groupBy('countries.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $byMonth = \App\Models\Commission::selectRaw("strftime('%Y-%m', COALESCE(received_date, created_at)) as label, SUM(amount) as total")
            ->groupBy('label')
            ->orderBy('label')
            ->limit(12)
            ->get();

        $revenueByUniversity = ['labels' => $byUniversity->pluck('label')->values()->all(), 'data' => $byUniversity->pluck('total')->map(fn ($v) => (float) $v)->values()->all()];
        $revenueByCountry = ['labels' => $byCountry->pluck('label')->values()->all(), 'data' => $byCountry->pluck('total')->map(fn ($v) => (float) $v)->values()->all()];
        $revenueByMonth = ['labels' => $byMonth->pluck('label')->values()->all(), 'data' => $byMonth->pluck('total')->map(fn ($v) => (float) $v)->values()->all()];

        return view('finance.revenue', compact('cards', 'revenueByUniversity', 'revenueByCountry', 'revenueByMonth'));
    }
}
