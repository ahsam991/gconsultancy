<div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $form->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="slug">Slug (optional — auto-generated)</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $form->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" maxlength="255">
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="active">Status</label>
        <select name="active" id="active" class="form-select">
            <option value="1" @selected(old('active', $form->active ?? true) == true)>Active</option>
            <option value="0" @selected(old('active', $form->active ?? true) == false)>Inactive</option>
        </select>
    </div>
</div>
<div class="mb-3">
    <label class="form-label" for="fields">Fields (valid JSON array, e.g. [{"name":"email","type":"email","label":"Email"}])</label>
    <textarea name="fields" id="fields" rows="8" class="form-control font-monospace @error('fields') is-invalid @enderror" required>{{ old('fields', isset($form->fields) && $form->fields ? json_encode($form->fields, JSON_PRETTY_PRINT) : '[]') }}</textarea>
    @error('fields')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
