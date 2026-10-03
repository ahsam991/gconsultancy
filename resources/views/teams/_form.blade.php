<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $team->name ?? '') }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Manager</label><select name="manager_id" class="form-select"><option value="">—</option>@foreach(($managers ?? []) as $m)<option value="{{ $m->id }}" @selected(old('manager_id', $team->manager_id ?? '')==$m->id)>{{ $m->name }}</option>@endforeach</select></div>
<div class="col-12 mb-3"><label class="form-label">Description</label><textarea name="description" rows="2" class="form-control">{{ old('description', $team->description ?? '') }}</textarea></div>
@if(isset($team))<div class="col-md-6 mb-3"><label class="form-label">Active</label><select name="active" class="form-select"><option value="1" @selected(old('active', $team->active ?? true))>Yes</option><option value="0" @selected(!old('active', $team->active ?? true))>No</option></select></div>@endif
</div>
