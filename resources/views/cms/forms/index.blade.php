@extends('layouts.app')
@section('title', 'Forms')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Forms</h4>
    <a href="{{ Route::has('cms.forms.create') ? route('cms.forms.create') : url('/crm/cms/forms/create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Form</a>
</div>
<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>#</th><th>Name</th><th>Slug</th><th>Submissions</th><th>Active</th><th></th></tr></thead>
<tbody>
@forelse(($forms ?? []) as $f)
<tr>
    <td>{{ $f->id }}</td>
    <td>{{ $f->name }}</td>
    <td class="small">{{ $f->slug }}</td>
    <td>{{ $f->submissions_count ?? 0 }}</td>
    <td>{{ $f->active ? 'Yes' : 'No' }}</td>
    <td class="text-nowrap">
        <a href="{{ Route::has('cms.forms.submissions.index') ? route('cms.forms.submissions.index', $f) : url('/crm/cms/forms/' . $f->id . '/submissions') }}" class="btn btn-sm btn-outline-info">Submissions</a>
        <a href="{{ Route::has('cms.forms.edit') ? route('cms.forms.edit', $f) : url('/crm/cms/forms/' . $f->id . '/edit') }}" class="btn btn-sm btn-outline-warning">Edit</a>
        <form method="POST" action="{{ Route::has('cms.forms.destroy') ? route('cms.forms.destroy', $f) : url('/crm/cms/forms/' . $f->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">No forms yet.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($forms) && method_exists($forms, 'links')){{ $forms->links() }}@endif
</div></div>
@endsection
