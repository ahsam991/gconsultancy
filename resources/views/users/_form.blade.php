@php $u = $user ?? null; @endphp
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $u->name ?? '') }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" value="{{ old('email', $u->email ?? '') }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Password {{ isset($u) ? '(leave blank to keep)' : '*' }}</label><input type="password" name="password" class="form-control" {{ isset($u) ? '' : 'required' }} minlength="8"><input type="password" name="password_confirmation" class="form-control mt-2" placeholder="Confirm password"></div>
<div class="col-md-6 mb-3"><label class="form-label">Role <span class="text-danger">*</span></label><select name="role_id" class="form-select" required>@foreach(($roles ?? []) as $r)<option value="{{ $r->id }}" @selected(old('role_id', $u->role_id ?? '')==$r->id)>{{ ucfirst($r->name) }}</option>@endforeach</select></div>
<div class="col-md-6 mb-3"><label class="form-label">Team</label><select name="team_id" class="form-select"><option value="">—</option>@foreach(($teams ?? []) as $t)<option value="{{ $t->id }}" @selected(old('team_id', $u->team_id ?? '')==$t->id)>{{ $t->name }}</option>@endforeach</select></div>
<div class="col-md-6 mb-3"><label class="form-label">Phone</label><input name="phone" value="{{ old('phone', $u->phone ?? '') }}" class="form-control"></div>
</div>
