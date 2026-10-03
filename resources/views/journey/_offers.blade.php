<div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Type</th><th>Amount</th><th>Status</th></tr></thead><tbody>
@forelse(($offers ?? []) as $o)<tr><td>{{ $o->offer_type ?? $o->type ?? '—' }}</td><td>£{{ number_format($o->amount ?? $o->scholarship ?? 0,2) }}</td><td><x-status-badge :status="$o->status ?? 'pending'"/></td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No offers.</td></tr>@endforelse
</tbody></table></div>
