@extends('layouts.app')
@section('title','Audit Log')
@section('content')
<h4 class="mb-3">Audit Log</h4>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="auditTable">
<thead><tr><th>Action</th><th>User</th><th>Model</th><th>Time</th></tr></thead>
<tbody>@forelse(($audits ?? $logs ?? []) as $a)<tr><td>{{ $a->action ?? $a->event ?? '' }}</td><td>{{ $a->user->name ?? $a->user_name ?? '—' }}</td><td>{{ $a->auditable_type ?? $a->model ?? '—' }} #{{ $a->auditable_id ?? $a->model_id ?? '' }}</td><td>{{ $a->created_at ?? '' }}</td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
