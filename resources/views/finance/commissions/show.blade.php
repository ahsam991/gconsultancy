@extends('layouts.app')
@section('title','Commission Detail')
@section('content')
<h4 class="mb-3">Commission <x-status-badge :status="$commission->status ?? 'pending'"/></h4>
<div class="card shadow-sm"><div class="card-body"><dl class="row mb-0">
<dt class="col-4">University</dt><dd class="col-8">{{ $commission->university->name ?? '—' }}</dd>
<dt class="col-4">Candidate</dt><dd class="col-8">{{ $commission->candidate->first_name ?? '' }} {{ $commission->candidate->last_name ?? '' }}</dd>
<dt class="col-4">Amount</dt><dd class="col-8">£{{ number_format($commission->amount ?? 0,2) }}</dd>
<dt class="col-4">Status</dt><dd class="col-8">{{ $commission->status ?? '—' }}</dd>
</dl>
<div class="d-flex gap-2 mt-3"><form method="POST" action="{{ route('commissions.claim', $commission) }}">@csrf<button class="btn btn-warning">Claim</button></form><form method="POST" action="{{ route('commissions.receive', $commission) }}">@csrf<button class="btn btn-success">Mark Received</button></form><a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary">Back</a></div>
</div></div>
@endsection
