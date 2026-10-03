@extends('layouts.app')
@section('title','Applications Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Applications Report</h4><a href="{{ route('reports.applications', ['export'=>'csv'] + request()->query()) }}" class="btn btn-sm btn-outline-success">Export CSV</a></div>
<x-filter-panel><form method="GET" action="{{ route('reports.applications') }}" class="row g-2 align-items-end w-100">
<div class="col-md-3"><label class="form-label small">From</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label small">To</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['draft','submitted','offer_received','deposit_paid','cas_issued','visa_applied','visa_granted','visa_refused','enrolled'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
<div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Run</button><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
</form></x-filter-panel>
<div class="card shadow-sm"><div class="card-body"><x-datatable id="repApp"><thead><tr><th>UID</th><th>Candidate</th><th>University</th><th>Status</th><th>Created</th></tr></thead><tbody>
@forelse(($rows ?? $applications ?? []) as $a)<tr><td>{{ $a->uid ?? $a->id }}</td><td>{{ $a->candidate->first_name ?? '' }}</td><td>{{ $a->university->name ?? '—' }}</td><td><x-status-badge :status="$a->status ?? ''"/></td><td>{{ $a->created_at ?? '' }}</td></tr>@empty @endforelse
</tbody></x-datatable></div></div>
@endsection
