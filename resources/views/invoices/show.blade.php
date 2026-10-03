@extends('layouts.app')
@section('title','Invoice')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Invoice {{ $invoice->invoice_no ?? $invoice->id }} <x-status-badge :status="$invoice->status ?? 'unpaid'"/></h4><div class="d-flex gap-2"><a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-secondary">Download PDF</a><a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div></div>
<div class="card shadow-sm mb-3" id="printable"><div class="card-body">
<div class="d-flex justify-content-between mb-3"><div><strong>Global Consultancy</strong><div class="small text-muted">Invoice to: {{ $invoice->university->name ?? '—' }}</div></div><div class="text-end small">No: {{ $invoice->invoice_no ?? $invoice->id }}<br>Date: {{ $invoice->created_at ?? '' }}</div></div>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Description</th><th class="text-end">Amount</th></tr></thead><tbody>
@forelse(($invoice->items ?? []) as $it)<tr><td>{{ $it->description ?? '' }}</td><td class="text-end">£{{ number_format($it->amount ?? 0,2) }}</td></tr>@empty<tr><td colspan="2" class="text-muted">No line items.</td></tr>@endforelse
</tbody><tfoot><tr><th>Total</th><th class="text-end">£{{ number_format($invoice->total ?? 0,2) }}</th></tr></tfoot></table></div>
</div></div>
<div class="card shadow-sm"><div class="card-header fw-semibold">Record Payment</div><div class="card-body">
@include('finance.payments._form',['invoice'=>$invoice])
<h6 class="mt-3">Payments</h6>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Date</th><th>Amount</th><th>Method</th></tr></thead><tbody>
@forelse(($invoice->payments ?? $payments ?? []) as $p)<tr><td>{{ $p->created_at ?? $p->paid_at ?? '' }}</td><td>£{{ number_format($p->amount ?? 0,2) }}</td><td>{{ $p->method ?? '—' }}</td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No payments.</td></tr>@endforelse
</tbody></table></div>
</div></div>
@endsection
