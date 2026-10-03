@extends('layouts.app')
@section('title','Student Payments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Student Payments</h4>
    @if(Route::has('revenue.index'))
    <a href="{{ route('revenue.index') }}" class="btn btn-sm btn-outline-primary">Revenue</a>
    @endif
</div>

<x-filter-panel>
<form method="GET" action="{{ Route::has('student-payments.index') ? route('student-payments.index') : url()->current() }}" class="row g-2 align-items-end w-100">
    <div class="col-md-3"><label class="form-label small">Candidate</label><select name="candidate_id" class="form-select"><option value="">All</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}" @selected(request('candidate_id')==$c->id)>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->uid ?? $c->id }})</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Application</label><select name="application_id" class="form-select"><option value="">All</option>@foreach(($applications ?? []) as $a)<option value="{{ $a->id }}" @selected(request('application_id')==$a->id)>{{ $a->uid ?? $a->id }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['pending','paid','verified','rejected'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Filter</button><a href="{{ Route::has('student-payments.index') ? route('student-payments.index') : url()->current() }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>

@if(Route::has('student-payments.store'))
<div class="card shadow-sm mb-3"><div class="card-body">
<h6>Record student payment</h6>
<form method="POST" action="{{ route('student-payments.store') }}" enctype="multipart/form-data" class="row g-2">
    @csrf
    <div class="col-md-3"><label class="form-label small">Candidate *</label><select name="candidate_id" class="form-select" required><option value="">Select…</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}" @selected(old('candidate_id')==$c->id)>{{ $c->first_name }} {{ $c->last_name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Application</label><select name="application_id" class="form-select"><option value="">None</option>@foreach(($applications ?? []) as $a)<option value="{{ $a->id }}" @selected(old('application_id')==$a->id)>{{ $a->uid ?? $a->id }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Amount *</label><input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="form-control" required></div>
    <div class="col-md-2"><label class="form-label small">Currency</label><select name="currency" class="form-select"><option>GBP</option><option>BDT</option><option>USD</option></select></div>
    <div class="col-md-2"><label class="form-label small">Purpose *</label><select name="purpose" class="form-select" required><option>Tuition</option><option>Deposit</option><option>Visa fee</option><option>Service charge</option><option>Other</option></select></div>
    <div class="col-md-2"><label class="form-label small">Status</label><select name="status" class="form-select"><option>pending</option><option>paid</option><option>verified</option></select></div>
    <div class="col-md-2"><label class="form-label small">Payment date</label><input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="form-control"></div>
    <div class="col-md-3"><label class="form-label small">Transaction ref</label><input name="transaction_ref" value="{{ old('transaction_ref') }}" class="form-control" maxlength="255"></div>
    <div class="col-md-3"><label class="form-label small">Receipt (pdf/jpg/png, 5MB)</label><input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Save</button></div>
</form>
</div></div>
@endif

<div class="card shadow-sm"><div class="card-body">
<x-datatable id="stdPayTable">
<thead><tr><th>Candidate</th><th>Application</th><th>Amount</th><th>Purpose</th><th>Status</th><th>Date</th><th>Receipt</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($payments ?? []) as $p)
<tr>
    <td>{{ $p->candidate->first_name ?? '' }} {{ $p->candidate->last_name ?? '' }}</td>
    <td>{{ $p->application->uid ?? ($p->application_id ?? '—') }}</td>
    <td>{{ $p->currency ?? 'GBP' }} {{ number_format($p->amount ?? 0,2) }}</td>
    <td>{{ $p->purpose ?? '—' }}</td>
    <td><x-status-badge :status="$p->status ?? ''"/></td>
    <td>{{ $p->payment_date ?? $p->created_at?->toDateString() }}</td>
    <td>@if(!empty($p->receipt_path))<a href="{{ asset('storage/'.$p->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-info">View</a>@else — @endif</td>
    <td>
        @if(Route::has('student-payments.update'))
        <form method="POST" action="{{ route('student-payments.update', $p) }}" enctype="multipart/form-data" class="d-flex gap-1">
            @csrf @method('PATCH')
            <select name="status" class="form-select form-select-sm"><option @selected($p->status=='pending')>pending</option><option @selected($p->status=='paid')>paid</option><option @selected($p->status=='verified')>verified</option><option @selected($p->status=='rejected')>rejected</option></select>
            <button class="btn btn-sm btn-outline-primary">Update</button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="8" class="text-center text-muted">No student payments found.</td></tr>
@endforelse
</tbody>
</x-datatable>
<div class="mt-2">{{ ($payments ?? null)?->links() }}</div>
</div></div>
@endsection
