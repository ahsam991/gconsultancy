<div class="mb-3">
    <label class="form-label" for="title">Title</label>
    <input type="text" name="title" id="title" value="{{ old('title', $banner->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required maxlength="255">
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label" for="image">Image (max 2MB)</label>
    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if(!empty($banner->image))
        <div class="mt-2"><img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" style="max-height:120px" class="img-thumbnail"></div>
    @endif
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="link">Link</label>
        <input type="text" name="link" id="link" value="{{ old('link', $banner->link ?? '') }}" class="form-control @error('link') is-invalid @enderror" maxlength="500" placeholder="https://…">
        @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="location">Location</label>
        <select name="location" id="location" class="form-select @error('location') is-invalid @enderror" required>
            @foreach(['home_hero' => 'Home Hero', 'sidebar' => 'Sidebar', 'footer' => 'Footer', 'popup' => 'Popup'] as $val => $label)
                <option value="{{ $val }}" @selected(old('location', $banner->location ?? 'home_hero') == $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="sort_order">Sort Order</label>
        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror" min="0">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="active">Status</label>
        <select name="active" id="active" class="form-select">
            <option value="1" @selected(old('active', $banner->active ?? true) == true)>Active</option>
            <option value="0" @selected(old('active', $banner->active ?? true) == false)>Inactive</option>
        </select>
    </div>
</div>
