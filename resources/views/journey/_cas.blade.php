<div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>CAS No</th><th>Status</th><th>Issued</th></tr></thead><tbody>
@forelse(($casRecords ?? $cas ?? []) as $c)<tr><td>{{ $c->cas_number ?? $c->number ?? '—' }}</td><td><x-status-badge :status="$c->status ?? 'pending'"/></td><td>{{ isset($c->issued_at) ? \Carbon\Carbon::parse($c->issued_at)->format('d M Y') : '—' }}</td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No CAS records.</td></tr>@endforelse
</tbody></table></div>
