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
}
