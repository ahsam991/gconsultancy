@php $u = $university ?? null; @endphp
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $u->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6 mb-3"><label class="form-label">Country <span class="text-danger">*</span></label><select name="country" class="form-select" required>@foreach(['UK','Canada','Australia','USA','New Zealand','Ireland'] as $ct)<option @selected(old('country',$u->country ?? 'UK')==$ct)>{{ $ct }}</option>@endforeach</select></div>
    <div class="col-md-6 mb-3"><label class="form-label">City</label><input name="city" value="{{ old('city', $u->city ?? '') }}" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Ranking</label><input name="ranking" value="{{ old('ranking', $u->ranking ?? '') }}" class="form-control"></div>
    <div class="col-md-4 mb-3"><label class="form-label">Partner</label><select name="is_partner" class="form-select"><option value="0" @selected(old('is_partner',$u->is_partner ?? 0)==0)>No</option><option value="1" @selected(old('is_partner',$u->is_partner ?? 0)==1)>Yes</option></select></div>
    <div class="col-md-4 mb-3"><label class="form-label">Commission %</label><input type="number" step="0.01" name="commission_rate" value="{{ old('commission_rate', $u->commission_rate ?? '') }}" class="form-control"></div>
    <div class="col-md-4 mb-3"><label class="form-label">Website</label><input name="website" value="{{ old('website', $u->website ?? '') }}" class="form-control"></div>
    <div class="col-12 mb-3"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control">{{ old('description', $u->description ?? '') }}</textarea></div>
</div>
