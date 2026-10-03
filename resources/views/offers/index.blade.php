@extends('layouts.app')
@section('title','Offers')
@section('content')
<h4 class="mb-3">Offers</h4>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="offersTable">
<thead><tr><th>Application</th><th>Candidate</th><th>Type</th><th>Amount</th><th>Status</th></tr></thead>
<tbody>@forelse(($offers ?? []) as $o)<tr><td><a href="{{ route('applications.show', $o->application_id) }}">{{ $o->application->uid ?? $o->application_id }}</a></td><td>{{ $o->application->candidate->first_name ?? '' }} {{ $o->application->candidate->last_name ?? '' }}</td><td>{{ $o->offer_type ?? $o->type ?? '—' }}</td><td>£{{ number_format($o->amount ?? 0,2) }}</td><td><x-status-badge :status="$o->status ?? 'pending'"/></td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
