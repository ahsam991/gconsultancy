@extends('layouts.app')
@section('title','Application Fees')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Application Fees</h4>
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
<form method="GET" action="{{ Route::has('support.fees') ? route('support.fees') : url('/support/fees') }}" class="row g-2 align-items-end">
    <div class="col-md-6">
        <label class="form-label small" for="filter_application">Application</label>
        <select name="application_id" id="filter_application" class="form-select" onchange="this.form.submit()">
            <option value="">All applications</option>
            @foreach(($applications ?? []) as $a)
            <option value="{{ $a->id }}" @selected(request('application_id') == $a->id)>{{ $a->uid }} — {{ $a->candidate->first_name ?? '' }} {{ $a->candidate->last_name ?? '' }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 d-flex gap-1">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ Route::has('support.fees') ? route('support.fees') : url('/support/fees') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>
</div></div>

<div class="card shadow-sm mb-3">
<div class="card-header fw-semibold">Record Application Fee</div>
<div class="card-body">
<form method="POST" action="{{ Route::has('support.fees.store') ? route('support.fees.store') : url('/support/fees') }}">
    @csrf
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label" for="application_id">Application <span class="text-danger">*</span></label>
            <select name="application_id" id="application_id" class="form-select @error('application_id') is-invalid @enderror" required>
                <option value="">Select application</option>
                @foreach(($applications ?? []) as $a)
                <option value="{{ $a->id }}" @selected(old('application_id', request('application_id')) == $a->id)>{{ $a->uid }} — {{ $a->candidate->first_name ?? '' }} {{ $a->candidate->last_name ?? '' }}</option>
                @endforeach
            </select>
            @error('application_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="amount">Amount <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="amount" id="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required>
            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="currency">Currency</label>
            <input name="currency" id="currency" value="{{ old('currency', 'GBP') }}" class="form-control @error('currency') is-invalid @enderror" maxlength="10">
            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                @foreach(['pending','paid','waived','refunded'] as $st)
                <option value="{{ $st }}" @selected(old('status', 'pending') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="payment_date">Payment Date</label>
            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date') }}" class="form-control @error('payment_date') is-invalid @enderror">
            @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="receipt">Receipt</label>
            <input name="receipt" id="receipt" value="{{ old('receipt') }}" class="form-control @error('receipt') is-invalid @enderror" placeholder="e.g. receipts/fee-1024.pdf" maxlength="255">
            @error('receipt')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 mb-3">
            <label class="form-label" for="notes">Notes</label>
            <input name="notes" id="notes" value="{{ old('notes') }}" class="form-control @error('notes') is-invalid @enderror" placeholder="Fee notes">
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Record Fee</button>
</form>
</div>
</div>

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>Application</th><th>Amount</th><th>Status</th><th>Payment Date</th><th>Receipt</th><th class="text-end">Actions</th></tr></thead>
<tbody>
@forelse(($fees ?? []) as $fee)
<tr>
    <td>{{ $fee->application->uid ?? $fee->application_id }}</td>
    <td>{{ $fee->currency ?? '' }} {{ $fee->amount !== null ? number_format($fee->amount, 2) : '—' }}</td>
    <td><span class="badge bg-secondary">{{ $fee->status ?? 'pending' }}</span></td>
    <td>{{ $fee->payment_date ? \Carbon\Carbon::parse($fee->payment_date)->format('d M Y') : '—' }}</td>
    <td class="small">{{ $fee->receipt_path ?? '—' }}</td>
    <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#feeEdit{{ $fee->id }}">Edit</button>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted py-4">No application fees found.</td></tr>
@endforelse
</tbody>
</table></div>
{{ ($fees ?? null)?->links() }}
</div></div>

@foreach(($fees ?? []) as $fee)
<div class="modal fade" id="feeEdit{{ $fee->id }}" tabindex="-1" aria-labelledby="feeEditLabel{{ $fee->id }}" aria-hidden="true">
<div class="modal-dialog modal-lg"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title" id="feeEditLabel{{ $fee->id }}">Edit Application Fee</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
<form method="POST" action="{{ Route::has('support.fees.update') ? route('support.fees.update', $fee->id) : url('/support/fees/'.$fee->id) }}">
@csrf @method('PUT')
<div class="modal-body">
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Amount</label><input type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $fee->amount) }}" class="form-control"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Currency</label><input name="currency" value="{{ old('currency', $fee->currency) }}" class="form-control" maxlength="10"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['pending','paid','waived','refunded'] as $st)
                <option value="{{ $st }}" @selected(old('status', $fee->status) === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3"><label class="form-label">Payment Date</label><input type="date" name="payment_date" value="{{ old('payment_date', $fee->payment_date ? \Carbon\Carbon::parse($fee->payment_date)->format('Y-m-d') : '') }}" class="form-control"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Receipt</label><input name="receipt" value="{{ old('receipt', $fee->receipt_path) }}" class="form-control" maxlength="255"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Notes</label><input name="notes" value="{{ old('notes', $fee->notes) }}" class="form-control"></div>
    </div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save changes</button></div>
</form>
</div></div>
</div>
@endforeach
@endsection
