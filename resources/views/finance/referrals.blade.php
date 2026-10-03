@extends('layouts.app')
@section('title','Referral Partners')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Referral Partners</h4>
    @if(Route::has('reports.index'))
    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Reports</a>
    @endif
</div>

@if(Route::has('referrals.store'))
<x-filter-panel>
<form method="POST" action="{{ route('referrals.store') }}" class="row g-2 align-items-end w-100">
    @csrf
    <div class="col-md-3"><label class="form-label small">Name *</label><input name="name" value="{{ old('name') }}" class="form-control" required maxlength="255"></div>
    <div class="col-md-2"><label class="form-label small">Company</label><input name="company" value="{{ old('company') }}" class="form-control" maxlength="255"></div>
    <div class="col-md-2"><label class="form-label small">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" maxlength="255"></div>
    <div class="col-md-2"><label class="form-label small">Share % *</label><input type="number" step="0.01" min="0" max="100" name="commission_share_percent" value="{{ old('commission_share_percent', 10) }}" class="form-control" required></div>
    <div class="col-md-2"><label class="form-label small">Type</label><select name="type" class="form-select"><option>Agent</option><option>Sub-agent</option><option>School</option><option>Other</option></select></div>
    <div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div>
</form>
</x-filter-panel>
@endif

<div class="card shadow-sm mb-3"><div class="card-body">
<x-datatable id="refPartners">
<thead><tr><th>Name</th><th>Company</th><th>Email</th><th>Share %</th><th>Type</th><th>Status</th><th>Payments</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($partners ?? []) as $p)
<tr>
    <td>{{ $p->name }}</td>
    <td>{{ $p->company ?? '—' }}</td>
    <td>{{ $p->email ?? '—' }}</td>
    <td>{{ number_format((float) $p->commission_share_percent, 2) }}%</td>
    <td>{{ $p->type ?? 'Agent' }}</td>
    <td><x-status-badge :status="$p->active ? 'active' : 'inactive'"/></td>
    <td>{{ $p->payments_count ?? $p->payments()->count() }}</td>
    <td class="text-nowrap">
        @if(Route::has('referrals.update'))
        <form method="POST" action="{{ route('referrals.update', $p) }}" class="d-inline">
            @csrf @method('PATCH')
            <input type="hidden" name="commission_share_percent" value="{{ $p->commission_share_percent }}">
            <input type="hidden" name="active" value="{{ $p->active ? 0 : 1 }}">
            <button class="btn btn-sm btn-outline-secondary" title="{{ $p->active ? 'Deactivate' : 'Activate' }}">{{ $p->active ? 'Disable' : 'Enable' }}</button>
        </form>
        @endif
        @if(Route::has('referrals.destroy'))
        <form method="POST" action="{{ route('referrals.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Remove this partner?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="8" class="text-center text-muted">No referral partners yet.</td></tr>
@endforelse
</tbody>
</x-datatable>
<div class="mt-2">{{ ($partners ?? null)?->links() }}</div>
</div></div>

<div class="row">
<div class="col-md-4 mb-3">
<div class="card shadow-sm h-100"><div class="card-body">
<h6>Pay partner share</h6>
<p class="small text-muted">Creates a pending referral payment. Share = commission amount × share % ÷ 100.</p>
@if(Route::has('referrals.pay'))
<form method="POST" action="{{ route('referrals.pay') }}" class="row g-2">
    @csrf
    <div class="col-12"><label class="form-label small">Partner *</label><select name="referral_partner_id" class="form-select" required><option value="">Select…</option>@foreach(($partners ?? []) as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ number_format((float) $p->commission_share_percent,2) }}%)</option>@endforeach</select></div>
    <div class="col-12"><label class="form-label small">Commission *</label><select name="commission_id" class="form-select" required><option value="">Select…</option>@foreach(($commissions ?? []) as $c)<option value="{{ $c->id }}">#{{ $c->id }} — {{ $c->candidate->first_name ?? '' }} {{ $c->candidate->last_name ?? '' }} — £{{ number_format($c->amount ?? 0,2) }}</option>@endforeach</select></div>
    <div class="col-12"><label class="form-label small">Share % *</label><input type="number" step="0.01" min="0" max="100" name="share_percent" class="form-control" value="10" required></div>
    <div class="col-12"><button class="btn btn-primary w-100">Record pending payment</button></div>
</form>
@endif
</div></div>
</div>
<div class="col-md-8 mb-3">
<div class="card shadow-sm h-100"><div class="card-body">
<h6>Partner payments</h6>
<x-datatable id="refPayments">
<thead><tr><th>Partner</th><th>Commission</th><th>Share %</th><th>Share amount</th><th>Status</th><th>Paid at</th><th></th></tr></thead>
<tbody>
@forelse(($payments ?? []) as $pay)
<tr>
    <td>{{ $pay->referralPartner->name ?? '#'.$pay->referral_partner_id }}</td>
    <td>#{{ $pay->commission_id }} — £{{ number_format($pay->commission->amount ?? 0,2) }}</td>
    <td>{{ number_format((float) $pay->share_percent,2) }}%</td>
    <td>£{{ number_format($pay->share_amount ?? 0,2) }}</td>
    <td><x-status-badge :status="$pay->status ?? ''"/></td>
    <td>{{ $pay->paid_at ?? '—' }}</td>
    <td>
        @if(strtolower((string) $pay->status) !== 'paid' && Route::has('referral-payments.paid'))
        <form method="POST" action="{{ route('referral-payments.paid', $pay) }}">@csrf<button class="btn btn-sm btn-outline-success">Mark paid</button></form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted">No partner payments recorded.</td></tr>
@endforelse
</tbody>
</x-datatable>
<div class="mt-2">{{ ($payments ?? null)?->links() }}</div>
</div></div>
</div>
</div>
@endsection
