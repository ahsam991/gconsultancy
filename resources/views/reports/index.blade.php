@extends('layouts.app')
@section('title','Reports')
@section('content')
<h4 class="mb-3">Reports</h4>
<div class="row">
<div class="col-md-3 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Candidates Report</h6><p class="small text-muted">By status, destination, source.</p><a href="{{ route('reports.candidates') }}" class="btn btn-sm btn-primary">Open</a></div></div></div>
<div class="col-md-3 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Applications Report</h6><p class="small text-muted">Pipeline by stage & university.</p><a href="{{ route('reports.applications') }}" class="btn btn-sm btn-primary">Open</a></div></div></div>
<div class="col-md-3 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Visa Report</h6><p class="small text-muted">Outcomes & refusal analysis.</p><a href="{{ route('reports.visa') }}" class="btn btn-sm btn-primary">Open</a></div></div></div>
<div class="col-md-3 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Finance Report</h6><p class="small text-muted">Commissions & invoices.</p><a href="{{ route('reports.finance') }}" class="btn btn-sm btn-primary">Open</a></div></div></div>
<div class="col-md-3 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Enrolments Report</h6><p class="small text-muted">Enrolment by status.</p><a href="{{ route('reports.enrolments') }}" class="btn btn-sm btn-primary">Open</a></div></div></div>
<div class="col-md-3 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Staff Performance</h6><p class="small text-muted">Caseload by staff.</p><a href="{{ route('reports.staff') }}" class="btn btn-sm btn-primary">Open</a></div></div></div>
</div>
@endsection
