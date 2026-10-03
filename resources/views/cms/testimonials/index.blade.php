@extends('layouts.app')
@section('title','Testimonials')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Testimonials</h4><a href="{{ route('cms.testimonials.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add</a></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="cmsTestimonialsTable">
<thead><tr><th>#</th><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($items ?? []) as $i)<tr><td>{{ $i->id }}</td><td>{{ $i->name ?? '' }}</td><td><x-status-badge :status="$i->status ?? 'draft'"/></td>
<td class="text-nowrap"><a href="{{ route('cms.testimonials.edit', $i) }}" class="btn btn-sm btn-outline-warning">Edit</a><form method="POST" action="{{ route('cms.testimonials.destroy', $i) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
