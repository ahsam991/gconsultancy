<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $template->name ?? '') }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Slug <span class="text-danger">*</span></label><input name="slug" value="{{ old('slug', $template->slug ?? '') }}" class="form-control" required pattern="[a-z0-9_-]+"></div>
<div class="col-12 mb-3"><label class="form-label">Subject <span class="text-danger">*</span></label><input name="subject" value="{{ old('subject', $template->subject ?? '') }}" class="form-control" required></div>
<div class="col-12 mb-3"><label class="form-label">Body <span class="text-danger">*</span></label><textarea name="body" rows="8" class="form-control" required>{{ old('body', $template->body ?? '') }}</textarea><small class="text-muted">Use @{{name}}, @{{uid}}, @{{status}} style placeholders — list them in Variables.</small></div>
<div class="col-md-6 mb-3"><label class="form-label">Variables (comma separated)</label><input name="variables" value="{{ old('variables', $template->variables ?? '') }}" class="form-control" placeholder="name, uid, status"></div>
@if(isset($template))<div class="col-md-6 mb-3"><label class="form-label">Active</label><select name="active" class="form-select"><option value="1" @selected(old('active', $template->active ?? true))>Yes</option><option value="0" @selected(!old('active', $template->active ?? true))>No</option></select></div>@endif
</div>
