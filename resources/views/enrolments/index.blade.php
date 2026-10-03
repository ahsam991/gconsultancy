@extends('layouts.app')
@section('title','Enrolments')
@section('content')
<h4 class="mb-3">Enrolments</h4>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="enrTable">
<thead><tr><th>Application</th><th>Candidate</th><th>Student ID</th><th>Status</th><th>Date</th></tr></thead>
<tbody>@forelse(($enrolments ?? []) as $e)<tr><td><a href="{{ route('applications.show', $e->application_id) }}">{{ $e->application->uid ?? $e->application_id }}</a></td><td>{{ $e->application->candidate->first_name ?? '' }}</td><td>{{ $e->student_id ?? '—' }}</td><td><x-status-badge :status="$e->status ?? 'pending'"/></td><td>{{ $e->enrolled_at ?? '—' }}</td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
