@extends('layouts.app')
@section('title','Branches')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Branches</h4>
    @if(Route::has('branches.create'))
    <a href="{{ route('branches.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>New Branch</a>
    @endif
</div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="branchesTable">
<thead><tr><th>Name</th><th>Code</th><th>City</th><th>Country</th><th>Manager</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($branches ?? []) as $b)
<tr>
    <td>@if(Route::has('branches.show'))<a href="{{ route('branches.show', $b) }}">{{ $b->name }}</a>@else{{ $b->name }}@endif</td>
    <td>{{ $b->code }}</td>
    <td>{{ $b->city ?? '—' }}</td>
    <td>{{ $b->country ?? '—' }}</td>
    <td>{{ $b->manager->name ?? '—' }}</td>
    <td><x-status-badge :status="$b->active ? 'active' : 'inactive'"/></td>
    <td class="text-nowrap">
        @if(Route::has('branches.show'))<a href="{{ route('branches.show', $b) }}" class="btn btn-sm btn-outline-primary">View</a>@endif
        @if(Route::has('branches.edit'))<a href="{{ route('branches.edit', $b) }}" class="btn btn-sm btn-outline-warning">Edit</a>@endif
    </td>
</tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($branches ?? null, 'links'))<div class="mt-3">{{ $branches->links() }}</div>@endif
</div></div>
@endsection
