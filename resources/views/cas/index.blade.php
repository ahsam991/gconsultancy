@extends('layouts.app')
@section('title','CAS Records')
@section('content')
<h4 class="mb-3">CAS Records</h4>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="casTable">
<thead><tr><th>Application</th><th>Candidate</th><th>CAS No</th><th>Status</th><th>Issued</th></tr></thead>
<tbody>@forelse(($casRecords ?? $cas ?? []) as $c)<tr><td><a href="{{ route('applications.show', $c->application_id) }}">{{ $c->application->uid ?? $c->application_id }}</a></td><td>{{ $c->application->candidate->first_name ?? '' }}</td><td>{{ $c->cas_number ?? '—' }}</td><td><x-status-badge :status="$c->status ?? 'pending'"/></td><td>{{ $c->issued_at ?? '—' }}</td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
