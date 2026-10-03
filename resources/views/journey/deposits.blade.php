@extends('layouts.app')
@section('title','Deposits')
@section('content')
<h4 class="mb-3">Deposits</h4>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="depositsTable">
<thead><tr><th>Application</th><th>Candidate</th><th>Required</th><th>Paid</th><th>Status</th><th>Paid Date</th></tr></thead>
<tbody>@forelse(($deposits ?? []) as $d)<tr><td><a href="{{ route('applications.show', $d->application_id) }}">{{ $d->application->uid ?? $d->application_id }}</a></td><td>{{ $d->application->candidate->first_name ?? '' }} {{ $d->application->candidate->last_name ?? '' }}</td><td>£{{ number_format($d->required_amount ?? 0,2) }}</td><td>£{{ number_format($d->paid_amount ?? 0,2) }}</td><td><x-status-badge :status="$d->status ?? 'pending'"/></td><td>{{ $d->paid_date ?? '—' }}</td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
