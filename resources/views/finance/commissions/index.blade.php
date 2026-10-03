@extends('layouts.app')
@section('title','Commissions')
@section('content')
<h4 class="mb-3">Commissions</h4>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="commTable">
<thead><tr><th>University</th><th>Candidate</th><th>Application</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>@forelse(($commissions ?? []) as $c)<tr>
<td>{{ $c->university->name ?? '—' }}</td><td>{{ $c->candidate->first_name ?? '' }} {{ $c->candidate->last_name ?? '' }}</td>
<td><a href="{{ route('applications.show', $c->application_id) }}">{{ $c->application->uid ?? $c->application_id }}</a></td>
<td>£{{ number_format($c->amount ?? 0,2) }}</td><td><x-status-badge :status="$c->status ?? 'pending'"/></td>
<td class="text-nowrap"><a href="{{ route('applications.show', $c->application_id) }}" class="btn btn-sm btn-outline-info">View</a><form method="POST" action="{{ route('commissions.claim', $c) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-warning">Claim</button></form><form method="POST" action="{{ route('commissions.receive', $c) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success">Receive</button></form></td>
</tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
