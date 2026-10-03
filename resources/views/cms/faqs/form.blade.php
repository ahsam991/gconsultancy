<div class="row">
<div class="col-md-9 mb-3"><label class="form-label">Question <span class="text-danger">*</span></label><input name="question" value="{{ old("question", $item->question ?? "") }}" class="form-control" required></div><div class="col-md-3 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="draft">draft</option><option value="published" @selected(old("status",$item->status ?? "")=="published")>published</option></select></div>
<div class="col-12 mb-3"><label class="form-label">Answer <span class="text-danger">*</span></label><textarea name="answer" rows="3" class="form-control" required>{{ old("answer", $item->answer ?? "") }}</textarea></div>
</div>
