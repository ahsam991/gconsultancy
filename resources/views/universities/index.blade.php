@extends('layouts.app')
@section('title','Universities')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Universities</h4><a href="{{ route('universities.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add University</a></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="uniTable">
<thead><tr><th>Name</th><th>Country</th><th>Partner</th><th>Commission %</th><th>Courses</th><th>Actions</th></tr></thead>
<tbody>@forelse(($universities ?? []) as $u)<tr>
<td><a href="{{ route('universities.show', $u) }}">{{ $u->name }}</a></td><td>{{ $u->country }}</td>
<td>@if($u->is_partner ?? false)<span class="badge bg-success">Partner</span>@else<span class="badge bg-secondary">No</span>@endif</td>
<td>{{ $u->commission_rate ?? '—' }}</td><td>{{ $u->courses_count ?? $u->courses->count() ?? 0 }}</td>
<td class="text-nowrap"><a href="{{ route('universities.show', $u) }}" class="btn btn-sm btn-outline-info">View</a><a href="{{ route('universities.edit', $u) }}" class="btn btn-sm btn-outline-warning">Edit</a><form method="POST" action="{{ route('universities.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
</tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
