@extends('layouts.app')
@section('title','Email Templates')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Email Templates</h4><a href="{{ route('email-templates.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>New Template</a></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="tplTable">
<thead><tr><th>Name</th><th>Slug</th><th>Subject</th><th>Active</th><th>Actions</th></tr></thead>
<tbody>@forelse(($templates ?? []) as $t)<tr><td><a href="{{ route('email-templates.show', $t) }}">{{ $t->name }}</a></td><td><code>{{ $t->slug }}</code></td><td>{{ $t->subject }}</td><td><x-status-badge :status="$t->active ? 'active' : 'inactive'"/></td>
<td class="text-nowrap"><a href="{{ route('email-templates.edit', $t) }}" class="btn btn-sm btn-outline-warning">Edit</a></td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
