<div class="row">
    <div class="col-md-4 mb-2">
        <label class="form-label small" for="menu_name">Name</label>
        <input type="text" name="name" id="menu_name" value="{{ old('name', $menu->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label small" for="menu_location">Location</label>
        <select name="location" id="menu_location" class="form-select @error('location') is-invalid @enderror" required>
            @foreach(['header' => 'Header', 'footer' => 'Footer', 'sidebar' => 'Sidebar', 'mobile' => 'Mobile'] as $val => $label)
                <option value="{{ $val }}" @selected(old('location', $menu->location ?? 'header') == $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-2">
        <label class="form-label small" for="menu_active">Status</label>
        <select name="active" id="menu_active" class="form-select">
            <option value="1" @selected(old('active', $menu->active ?? true) == true)>Active</option>
            <option value="0" @selected(old('active', $menu->active ?? true) == false)>Inactive</option>
        </select>
    </div>
</div>
