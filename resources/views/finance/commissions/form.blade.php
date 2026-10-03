@php $c = $commission ?? null; @endphp
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Application <span class="text-danger">*</span></label><select name="application_id" class="form-select" required><option value="">Select…</option>@foreach(($applications ?? []) as $a)<option value="{{ $a->id }}" @selected(old('application_id',$c->application_id ?? '')==$a->id)>{{ $a->uid ?? $a->id }}</option>@endforeach</select></div>
<div class="col-md-6 mb-3"><label class="form-label">Amount (£) <span class="text-danger">*</span></label><input type="number" step="0.01" name="amount" value="{{ old('amount', $c->amount ?? '') }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['pending','claimed','received','cancelled'] as $s)<option @selected(old('status',$c->status ?? 'pending')==$s)>{{ $s }}</option>@endforeach</select></div>
</div>
