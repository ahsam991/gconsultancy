@php $ap = $appointment ?? null; @endphp
<div class="row">
    <div class="col-12 mb-3"><label class="form-label">Title <span class="text-danger">*</span></label><input name="title" value="{{ old('title', $ap->title ?? '') }}" class="form-control" required></div>
    <div class="col-md-6 mb-3"><label class="form-label">Scheduled At <span class="text-danger">*</span></label><input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', isset($ap->scheduled_at) ? \Carbon\Carbon::parse($ap->scheduled_at)->format('Y-m-d\TH:i') : '') }}" class="form-control" required></div>
    <div class="col-md-6 mb-3"><label class="form-label">Type</label><select name="type" class="form-select">@foreach(['Counselling','Follow-up','Visa Interview Prep','Document Check','Phone Call'] as $ty)<option @selected(old('type',$ap->type ?? '')==$ty)>{{ $ty }}</option>@endforeach</select></div>
    <div class="col-md-6 mb-3"><label class="form-label">Candidate</label><select name="candidate_id" class="form-select"><option value="">—</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}" @selected(old('candidate_id',$ap->candidate_id ?? '')==$c->id)>{{ $c->first_name }} {{ $c->last_name }}</option>@endforeach</select></div>
    <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['scheduled','completed','cancelled','no_show'] as $s)<option @selected(old('status',$ap->status ?? 'scheduled')==$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="col-12 mb-3"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control">{{ old('notes', $ap->notes ?? '') }}</textarea></div>
</div>
