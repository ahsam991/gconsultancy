<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" value="{{ old("name", $item->name ?? "") }}" class="form-control" required></div><div class="col-md-6 mb-3"><label class="form-label">Role</label><input name="role" value="{{ old("role", $item->role ?? "") }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">Bio</label><textarea name="bio" rows="2" class="form-control">{{ old("bio", $item->bio ?? "") }}</textarea></div>
</div>
