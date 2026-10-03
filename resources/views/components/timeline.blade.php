@props(['items' => []])
<ul class="list-group list-group-flush">
@forelse($items as $t)
<li class="list-group-item"><div class="d-flex justify-content-between"><strong>{{ $t->title ?? $t['title'] ?? 'Update' }}</strong><small class="text-muted">{{ isset($t->created_at) ? \Carbon\Carbon::parse($t->created_at)->diffForHumans() : ($t['created_at'] ?? '') }}</small></div><div class="small text-muted">{{ $t->description ?? $t['description'] ?? $t->body ?? $t['body'] ?? '' }}</div></li>
@empty
<li class="list-group-item text-muted">No timeline entries.</li>
@endforelse
</ul>
