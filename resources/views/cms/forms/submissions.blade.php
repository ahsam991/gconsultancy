@extends('layouts.app')
@section('title', 'Form Submissions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Submissions: {{ $form->name }} <small class="text-muted">({{ $form->slug }})</small></h4>
    <div class="d-flex gap-2">
        <a href="{{ Route::has('cms.forms.edit') ? route('cms.forms.edit', $form) : url('/crm/cms/forms/' . $form->id . '/edit') }}" class="btn btn-sm btn-outline-warning">Edit Form</a>
        <a href="{{ Route::has('cms.forms.index') ? route('cms.forms.index') : url('/crm/cms/forms') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>

@if(isset($submission))
<div class="card shadow-sm mb-3">
    <div class="card-header fw-semibold">Submission #{{ $submission->id }} <span class="text-muted small">{{ $submission->created_at?->format('Y-m-d H:i') }}</span></div>
    <div class="card-body">
        <dl class="row mb-0">
            @foreach(($submission->data ?? []) as $key => $value)
                <dt class="col-sm-3">{{ $key }}</dt>
                <dd class="col-sm-9">{{ is_array($value) ? json_encode($value) : $value }}</dd>
            @endforeach
        </dl>
        <p class="small text-muted mt-2 mb-0">IP: {{ $submission->ip ?? '—' }} · Agent: {{ $submission->user_agent ?? '—' }}</p>
    </div>
</div>
@endif

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>#</th><th>Submitted</th><th>Summary</th><th></th></tr></thead>
<tbody>
@forelse(($submissions ?? []) as $s)
<tr>
    <td>{{ $s->id }}</td>
    <td class="small">{{ $s->created_at?->format('Y-m-d H:i') }}</td>
    <td class="small">{{ \Illuminate\Support\Str::limit(json_encode($s->data), 120) }}</td>
    <td>
        <a href="{{ Route::has('cms.forms.submissions.show') ? route('cms.forms.submissions.show', [$form, $s]) : url('/crm/cms/forms/' . $form->id . '/submissions/' . $s->id) }}" class="btn btn-sm btn-outline-info">View</a>
    </td>
</tr>
@empty
<tr><td colspan="4" class="text-center text-muted">No submissions yet.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($submissions) && method_exists($submissions, 'links')){{ $submissions->links() }}@endif
</div></div>
@endsection
