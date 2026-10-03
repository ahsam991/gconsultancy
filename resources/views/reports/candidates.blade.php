@extends('layouts.app')
@section('title','Candidates Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Candidates Report</h4><a href="{{ route('reports.candidates', ['export'=>'csv'] + request()->query()) }}" class="btn btn-sm btn-outline-success">Export CSV</a></div>
<x-filter-panel><form method="GET" action="{{ route('reports.candidates') }}" class="row g-2 align-items-end w-100">
<div class="col-md-3"><label class="form-label small">From</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label small">To</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['new','contacted','counselling','applied','offered','visa','enrolled','rejected'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
<div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Run</button><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
</form></x-filter-panel>
<div class="card shadow-sm"><div class="card-body"><x-datatable id="repCand"><thead><tr><th>UID</th><th>Name</th><th>Status</th><th>Destination</th><th>Created</th></tr></thead><tbody>
@forelse(($rows ?? $candidates ?? []) as $c)<tr><td>{{ $c->uid ?? $c->id }}</td><td>{{ $c->first_name }} {{ $c->last_name }}</td><td><x-status-badge :status="$c->status ?? ''"/></td><td>{{ $c->preferred_destination ?? '—' }}</td><td>{{ $c->created_at ?? '' }}</td></tr>@empty @endforelse
</tbody></x-datatable></div></div>
@endsection
