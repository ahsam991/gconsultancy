@extends('layouts.app')
@section('title','Visa Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Visa Report</h4><a href="{{ route('reports.visa', ['export'=>'csv'] + request()->query()) }}" class="btn btn-sm btn-outline-success">Export CSV</a></div>
<x-filter-panel><form method="GET" action="{{ route('reports.visa') }}" class="row g-2 align-items-end w-100">
<div class="col-md-4"><label class="form-label small">Outcome</label><select name="outcome" class="form-select"><option value="">All</option>@foreach(['pending','granted','refused'] as $s)<option @selected(request('outcome')==$s)>{{ $s }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label small">Country</label><select name="country" class="form-select"><option value="">All</option>@foreach(['UK','Canada','Australia','USA'] as $ct)<option @selected(request('country')==$ct)>{{ $ct }}</option>@endforeach</select></div>
<div class="col-md-4 d-flex gap-1"><button class="btn btn-primary">Run</button><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
</form></x-filter-panel>
<div class="card shadow-sm"><div class="card-body"><x-datatable id="repVisa"><thead><tr><th>Application</th><th>Candidate</th><th>Outcome</th><th>Decided</th></tr></thead><tbody>
@forelse(($rows ?? $visaCases ?? []) as $v)<tr><td>{{ $v->application->uid ?? $v->application_id }}</td><td>{{ $v->application->candidate->first_name ?? '' }}</td><td><x-status-badge :status="$v->outcome ?? $v->status ?? ''"/></td><td>{{ $v->decision_date ?? '—' }}</td></tr>@empty @endforelse
</tbody></x-datatable></div></div>
@endsection
