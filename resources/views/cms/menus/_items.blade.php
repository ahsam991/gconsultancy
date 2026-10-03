<div class="card shadow-sm mb-3">
    <div class="card-header fw-semibold">Add Item to {{ $menu->name }}</div>
    <div class="card-body">
        <form method="POST" action="{{ Route::has('cms.menus.items.store') ? route('cms.menus.items.store', $menu) : url('/crm/cms/menus/' . $menu->id . '/items') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-3">
                <label class="form-label small" for="item_label">Label</label>
                <input type="text" name="label" id="item_label" value="{{ old('label') }}" class="form-control @error('label') is-invalid @enderror" required maxlength="255">
                @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label small" for="item_url">URL</label>
                <input type="text" name="url" id="item_url" value="{{ old('url') }}" class="form-control @error('url') is-invalid @enderror" required maxlength="500" placeholder="/about">
                @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label small" for="item_parent">Parent</label>
                <select name="parent_id" id="item_parent" class="form-select">
                    <option value="">None</option>
                    @foreach(($parents ?? $menu->items ?? []) as $p)
                        <option value="{{ $p->id }}" @selected(old('parent_id') == $p->id)>{{ $p->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small" for="item_sort">Sort</label>
                <input type="number" name="sort_order" id="item_sort" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm">Add Item</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>#</th><th>Label</th><th>URL</th><th>Parent</th><th>Sort</th><th>Active</th><th></th></tr></thead>
<tbody>
@forelse(($menu->items ?? []) as $item)
<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->label }}</td>
    <td class="small">{{ $item->url }}</td>
    <td>{{ $item->parent->label ?? '—' }}</td>
    <td>{{ $item->sort_order }}</td>
    <td>{{ $item->active ? 'Yes' : 'No' }}</td>
    <td class="text-nowrap">
        <form method="POST" action="{{ Route::has('cms.menus.items.update') ? route('cms.menus.items.update', [$menu, $item]) : url('/crm/cms/menus/' . $menu->id . '/items/' . $item->id) }}" class="d-inline">
            @csrf @method('PUT')
            <input type="hidden" name="label" value="{{ $item->label }}">
            <input type="hidden" name="url" value="{{ $item->url }}">
            <input type="hidden" name="sort_order" value="{{ $item->sort_order }}">
            <input type="hidden" name="active" value="{{ $item->active ? 0 : 1 }}">
            <button class="btn btn-sm btn-outline-secondary">{{ $item->active ? 'Hide' : 'Show' }}</button>
        </form>
        <form method="POST" action="{{ Route::has('cms.menus.items.destroy') ? route('cms.menus.items.destroy', [$menu, $item]) : url('/crm/cms/menus/' . $menu->id . '/items/' . $item->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted">No items yet. Add the first one above.</td></tr>
@endforelse
</tbody>
</table></div>
</div></div>
