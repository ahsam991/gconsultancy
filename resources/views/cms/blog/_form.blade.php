<div class="mb-3">
    <label class="form-label" for="title">Title</label>
    <input type="text" name="title" id="title" value="{{ old('title', $post->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required maxlength="255">
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="slug">Slug (optional — auto-generated)</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" maxlength="255">
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="blog_category_id">Category</label>
        <select name="blog_category_id" id="blog_category_id" class="form-select @error('blog_category_id') is-invalid @enderror">
            <option value="">No category</option>
            @foreach(($categories ?? []) as $cat)
                <option value="{{ $cat->id }}" @selected(old('blog_category_id', $post->blog_category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        @error('blog_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-3">
    <label class="form-label" for="excerpt">Excerpt</label>
    <textarea name="excerpt" id="excerpt" rows="2" class="form-control @error('excerpt') is-invalid @enderror" maxlength="1000">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
    @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label" for="content">Content</label>
    <textarea name="content" id="content" rows="6" class="form-control @error('content') is-invalid @enderror">{{ old('content', $post->content ?? '') }}</textarea>
    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label" for="featured_image">Featured Image (max 2MB)</label>
    <input type="file" name="featured_image" id="featured_image" class="form-control @error('featured_image') is-invalid @enderror" accept="image/*">
    @error('featured_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if(!empty($post->featured_image))
        <div class="mt-2"><img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" style="max-height:120px" class="img-thumbnail"></div>
    @endif
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label" for="status">Status</label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="draft" @selected(old('status', $post->status ?? 'draft') == 'draft')>Draft</option>
            <option value="published" @selected(old('status', $post->status ?? '') == 'published')>Published</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="published_at">Publish At (scheduling)</label>
        <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', isset($post->published_at) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('published_at') is-invalid @enderror">
        @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="meta_title">Meta Title</label>
        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}" class="form-control" maxlength="255">
    </div>
</div>
<div class="mb-3">
    <label class="form-label" for="meta_description">Meta Description</label>
    <textarea name="meta_description" id="meta_description" rows="2" class="form-control" maxlength="1000">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
</div>
