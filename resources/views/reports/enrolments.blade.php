@extends('layouts.app')
@section('title','Enrolments Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Enrolments Report</h4><a href="{{ route('reports.enrolments', ['format' => 'csv'] + request()->query()) }}" class="btn btn-sm btn-outline-success">Export CSV</a></div>
<x-filter-panel><form method="GET" action="{{ route('reports.enrolments') }}" class="row g-2 align-items-end w-100">
<div class="col-md-4"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['PENDING','CONFIRMED','ENROLLED','DEFERRED','WITHDRAWN','COMPLETED'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
<div class="col-md-2 d-flex gap-1"><button class="btn btn-primary">Run</button><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
</form></x-filter-panel>
<div class="card shadow-sm"><div class="card-body"><x-datatable id="repEnr"><thead><tr><th>Candidate</th><th>Student No</th><th>Campus</th><th>Status</th><th>Date</th></tr></thead><tbody>
@forelse(($rows ?? []) as $e)<tr><td>{{ $e->candidate->first_name ?? '' }} {{ $e->candidate->last_name ?? '' }}</td><td>{{ $e->student_id_no ?? '—' }}</td><td>{{ $e->campus ?? '—' }}</td><td><x-status-badge :status="$e->status ?? ''"/></td><td>{{ $e->enrolment_date?->format('d M Y') ?? '' }}</td></tr>@empty @endforelse
</tbody></x-datatable></div></div>
@endsection
