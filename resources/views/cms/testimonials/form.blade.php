<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" value="{{ old("name", $item->name ?? "") }}" class="form-control" required></div><div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="draft">draft</option><option value="published" @selected(old("status",$item->status ?? "")=="published")>published</option></select></div>
<div class="col-12 mb-3"><label class="form-label">Message</label><textarea name="message" rows="3" class="form-control">{{ old("message", $item->message ?? "") }}</textarea></div>
</div>
