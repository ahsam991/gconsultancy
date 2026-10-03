@extends('layouts.app')
@section('title','Admissions Pipeline & Case Registry')
@section('content')
@php
$c = $counts ?? [];
$base = max(1, $funnelBase ?? 1);
$stageLabels = ['SUBMITTED'=>'Submitted','CONDITIONAL_OFFER'=>'Conditional Offer','UNCONDITIONAL_OFFER'=>'Unconditional Offer','DEPOSIT_PAID'=>'Deposit Paid','CAS_ISSUED'=>'CAS Issued','VISA_APPROVED'=>'Visa Approved','ENROLLED'=>'Enrolled'];
@endphp
<div class="d-flex justify-content-between align-items-end mb-1 flex-wrap gap-2">
    <div><span class="gc-eyebrow blue">Adviser desk · {{ ucfirst($role ?? 'team') }} view</span>
    <h1 class="h3 mb-0 gc-display">Admissions Pipeline &amp; Case Registry</h1></div>
    <span class="text-muted small tnum">{{ now()->format('l, d M Y') }}</span>
</div>

<div class="gc-stats mt-3 mb-3">
    <div class="gc-stat"><div class="v tnum">{{ $c['candidates'] ?? 0 }}</div><div class="l">Candidates</div><div class="s">active caseload</div></div>
    <div class="gc-stat"><div class="v tnum">{{ $c['applications'] ?? 0 }}</div><div class="l">Applications</div><div class="s">in pipeline</div></div>
    <div class="gc-stat"><div class="v tnum">{{ $c['offers'] ?? 0 }}</div><div class="l">Offers</div><div class="s">conditional + unconditional</div></div>
    <div class="gc-stat brass"><div class="v tnum">£{{ number_format($c['commissions'] ?? 0, 0) }}</div><div class="l">Commission</div><div class="s">total claimed value</div></div>
    <div class="gc-stat"><div class="v tnum">{{ $c['visa'] ?? 0 }}</div><div class="l">Visa cases</div><div class="s">prep → decision</div></div>
    <div class="gc-stat"><div class="v tnum">{{ $c['enrolled'] ?? 0 }}</div><div class="l">Enrolled</div><div class="s">sealed this term</div></div>
    <div class="gc-stat"><div class="v tnum">{{ $c['tasks_due'] ?? 0 }}</div><div class="l">Tasks due</div><div class="s">next 7 days</div></div>
    <div class="gc-stat"><div class="v tnum">{{ ($appointments ?? collect())->count() }}</div><div class="l">Today</div><div class="s">appointments</div></div>
</div>

<div class="row">
    <div class="col-lg-7 mb-3"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><span>Application Pipeline Funnel</span><small class="text-muted tnum">base {{ $base }}</small></div>
        <div class="card-body">
        @foreach(($funnel ?? []) as $stage => $total)
        <div class="mb-2"><div class="d-flex justify-content-between small mb-1"><span>{{ $stageLabels[$stage] ?? $stage }}</span><span class="tnum fw-semibold">{{ $total }} · {{ (int) round($total / $base * 100) }}%</span></div>
        <div class="progress" style="height:8px"><div class="progress-bar" style="width:{{ (int) round($total / $base * 100) }}%"></div></div></div>
        @endforeach
        <small class="text-muted">Conversion SUBMITTED → ENROLLED this view.</small>
        </div></div></div>
    <div class="col-lg-5 mb-3"><div class="card h-100"><div class="card-header">Applications by Month</div><div class="card-body"><canvas id="appsChart" height="200"></canvas></div></div></div>
</div>
<div class="row">
    <div class="col-lg-5 mb-3"><div class="card h-100"><div class="card-header">Visa Outcomes</div><div class="card-body"><canvas id="visaChart" height="200"></canvas></div></div></div>
    <div class="col-lg-7 mb-3"><div class="card h-100"><div class="card-header d-flex justify-content-between"><span>Upcoming Tasks</span>@if(Route::has('tasks.index'))<a href="{{ route('tasks.index') }}" class="small">View all</a>@endif</div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Title</th><th>Candidate</th><th>Due</th><th>Status</th></tr></thead><tbody>
        @forelse(($tasks ?? []) as $t)<tr><td>{{ $t->title ?? '' }}</td><td>{{ $t->candidate->first_name ?? '—' }}</td><td class="tnum">{{ $t->due_date?->format('d M Y') ?? '—' }}</td><td><x-status-badge :status="$t->status ?? ''"/></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">No upcoming tasks.</td></tr>@endforelse
        </tbody></table></div></div></div></div>
</div>
<div class="row">
    <div class="col-12 mb-3"><div class="card"><div class="card-header d-flex justify-content-between"><span>Applicant Case Ledger</span>@if(Route::has('applications.index'))<a href="{{ route('applications.index') }}" class="small">Open registry</a>@endif</div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>UID</th><th>Candidate</th><th>University</th><th>Course</th><th>Status</th><th>Updated</th></tr></thead><tbody>
        @forelse(($recentApplications ?? []) as $a)<tr><td><a href="{{ route('applications.show', $a) }}">{{ $a->uid }}</a></td><td>{{ $a->candidate->first_name ?? '' }} {{ $a->candidate->last_name ?? '' }}</td><td>{{ $a->university->name ?? '—' }}</td><td>{{ $a->course->name ?? '—' }}</td><td><x-status-badge :status="$a->status ?? ''"/></td><td class="tnum">{{ $a->updated_at?->format('d M Y') ?? '' }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-3">No applications yet.</td></tr>@endforelse
        </tbody></table></div></div></div></div>
</div>
@push('scripts')
<script>
(function(){
var m=@json(array_keys(($appsByMonth ?? collect())->toArray() ?: ['—'=>0]));
var v=@json(array_values(($appsByMonth ?? collect())->toArray() ?: [0]));
var vl=@json(array_keys(($visaOutcomes ?? collect())->toArray() ?: ['No data'=>0]));
var vv=@json(array_values(($visaOutcomes ?? collect())->toArray() ?: [0]));
if(window.Chart){
new Chart(document.getElementById('appsChart'),{type:'bar',data:{labels:m,datasets:[{data:v,backgroundColor:'#1e40af',borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}}}});
new Chart(document.getElementById('visaChart'),{type:'doughnut',data:{labels:vl,datasets:[{data:vv,backgroundColor:['#166534','#991b1b','#1e40af'],borderWidth:2,borderColor:'#fff'}]},options:{responsive:true,cutout:'62%'}});
}})();
</script>
@endpush
@endsection
