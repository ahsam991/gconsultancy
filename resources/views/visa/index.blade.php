@extends('layouts.app')
@section('title','Visa Cases')
@section('content')
<h4 class="mb-3">Visa Cases</h4>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="visaTable">
<thead><tr><th>Application</th><th>Candidate</th><th>Type</th><th>Status</th><th>Applied</th></tr></thead>
<tbody>@forelse(($visaCases ?? $visas ?? []) as $v)<tr><td><a href="{{ route('applications.show', $v->application_id) }}">{{ $v->application->uid ?? $v->application_id }}</a></td><td>{{ $v->application->candidate->first_name ?? '' }}</td><td>{{ $v->visa_type ?? '—' }}</td><td><x-status-badge :status="$v->status ?? 'pending'"/></td><td>{{ $v->applied_at ?? '—' }}</td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
