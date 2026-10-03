@extends('layouts.app')
@section('title','Finance Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Finance Report</h4><a href="{{ route('reports.finance', ['export'=>'csv'] + request()->query()) }}" class="btn btn-sm btn-outline-success">Export CSV</a></div>
<x-filter-panel><form method="GET" action="{{ route('reports.finance') }}" class="row g-2 align-items-end w-100">
<div class="col-md-3"><label class="form-label small">From</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label small">To</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
<div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['pending','claimed','received','paid','unpaid'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
<div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Run</button><a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back</a></div>
</form></x-filter-panel>
<div class="card shadow-sm"><div class="card-body"><x-datatable id="repFin"><thead><tr><th>Ref</th><th>University</th><th>Amount</th><th>Status</th></tr></thead><tbody>
@forelse(($rows ?? $commissions ?? []) as $r)<tr><td>{{ $r->id }}</td><td>{{ $r->university->name ?? '—' }}</td><td>£{{ number_format($r->amount ?? $r->total ?? 0,2) }}</td><td><x-status-badge :status="$r->status ?? ''"/></td></tr>@empty @endforelse
</tbody></x-datatable></div></div>
@endsection
