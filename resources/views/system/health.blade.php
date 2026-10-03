@extends('layouts.app')
@section('title','System Health')
@section('content')
<h4 class="mb-3">System Health</h4>
<div class="row g-3 mb-3">
    <div class="col-md-3"><x-stat-card label="PHP Version" :value="$phpVersion ?? '—'"/></div>
    <div class="col-md-3"><x-stat-card label="Laravel Version" :value="$laravelVersion ?? '—'"/></div>
    <div class="col-md-3"><x-stat-card label="Database" :value="$dbStatus ?? '—'"/></div>
    <div class="col-md-3"><x-stat-card label="Queue / Failed" :value="($queueSize ?? 0).' / '.($failedCount ?? 0)"/></div>
</div>
<div class="row g-3">
    <div class="col-md-6"><div class="card shadow-sm"><div class="card-header fw-semibold">Storage Disk Usage</div><div class="card-body">
        <p class="mb-1">Used: {{ number_format(($diskUsed ?? 0) / 1024 / 1024 / 1024, 2) }} GB of {{ number_format(($diskTotal ?? 0) / 1024 / 1024 / 1024, 2) }} GB (free {{ number_format(($diskFree ?? 0) / 1024 / 1024 / 1024, 2) }} GB)</p>
        @php $pct = ($diskTotal ?? 0) > 0 ? min(100, round((($diskUsed ?? 0) / $diskTotal) * 100)) : 0; @endphp
        <div class="progress"><div class="progress-bar" style="width: {{ $pct }}%">{{ $pct }}%</div></div>
    </div></div></div>
    <div class="col-md-6"><div class="card shadow-sm"><div class="card-header fw-semibold">Backups &amp; Errors</div><div class="card-body">
        <p class="mb-1">Last backup: @if($lastBackup ?? null){{ $lastBackup->created_at?->format('d M Y H:i') }} ({{ $lastBackup->type }}, {{ $lastBackup->status }}) @else None recorded @endif</p>
        <p class="mb-0">Recent errors (last 50 log lines): <strong>{{ $recentErrors ?? 0 }}</strong></p>
    </div></div></div>
</div>
<div class="card shadow-sm mt-3"><div class="card-header fw-semibold">Recent Log Tail (last 20 lines)</div><div class="card-body">
<pre class="small bg-light p-3 rounded mb-0" style="max-height:320px;overflow:auto">@foreach(($logTail ?? []) as $line){{ $line }}
@endforeach @if(empty($logTail ?? []))No log lines available.@endif</pre>
</div></div>
@endsection
