@php $i = $invoice ?? null; @endphp
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">University <span class="text-danger">*</span></label><select name="university_id" class="form-select" required><option value="">Select…</option>@foreach(($universities ?? []) as $u)<option value="{{ $u->id }}" @selected(old('university_id',$i->university_id ?? '')==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
<div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['draft','sent','paid','partial','overdue','cancelled'] as $s)<option @selected(old('status',$i->status ?? 'draft')==$s)>{{ $s }}</option>@endforeach</select></div>
<div class="col-12 mb-3"><label class="form-label">Line Items (description | amount per line)</label><textarea name="items_text" rows="4" class="form-control" placeholder="Commission — App UID123 | 1500">{{ old('items_text', $i->items_text ?? '') }}</textarea></div>
</div>
