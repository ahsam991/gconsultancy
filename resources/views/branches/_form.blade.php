<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $branch->name ?? '') }}" class="form-control" required maxlength="255"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Code <span class="text-danger">*</span></label><input name="code" value="{{ old('code', $branch->code ?? '') }}" class="form-control" required maxlength="50" placeholder="e.g. LON-01"></div>
    <div class="col-12 mb-3"><label class="form-label">Address</label><input name="address" value="{{ old('address', $branch->address ?? '') }}" class="form-control" maxlength="500"></div>
    <div class="col-md-6 mb-3"><label class="form-label">City</label><input name="city" value="{{ old('city', $branch->city ?? '') }}" class="form-control" maxlength="255"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Country</label><input name="country" value="{{ old('country', $branch->country ?? '') }}" class="form-control" maxlength="255"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Phone</label><input name="phone" value="{{ old('phone', $branch->phone ?? '') }}" class="form-control" maxlength="50"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $branch->email ?? '') }}" class="form-control" maxlength="255"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Manager</label><select name="manager_id" class="form-select"><option value="">—</option>@foreach(($managers ?? []) as $m)<option value="{{ $m->id }}" @selected((string) old('manager_id', $branch->manager_id ?? '') === (string) $m->id)>{{ $m->name }} ({{ $m->email }})</option>@endforeach</select></div>
    <div class="col-md-6 mb-3"><label class="form-label">Active</label><select name="active" class="form-select"><option value="1" @selected(old('active', $branch->active ?? true))>Yes</option><option value="0" @selected(!old('active', $branch->active ?? true))>No</option></select></div>
</div>
