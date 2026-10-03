<div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Type</th><th>Status</th><th>Applied</th></tr></thead><tbody>
@forelse(($visaCases ?? $visas ?? []) as $v)<tr><td>{{ $v->visa_type ?? $v->type ?? '—' }}</td><td><x-status-badge :status="$v->status ?? $v->outcome ?? 'pending'"/></td><td>{{ isset($v->applied_at) ? \Carbon\Carbon::parse($v->applied_at)->format('d M Y') : '—' }}</td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No visa cases.</td></tr>@endforelse
</tbody></table></div>
