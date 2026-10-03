<x-timeline :items="$statusHistory ?? $timeline ?? []"/>
@if(!empty($statusHistory) || !empty($timeline))
@else
<p class="text-muted small">Status changes will appear here.</p>
@endif
