<div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Student ID</th><th>Status</th><th>Date</th></tr></thead><tbody>
@forelse(($enrolments ?? []) as $e)<tr><td>{{ $e->student_id ?? $e->enrolment_no ?? '—' }}</td><td><x-status-badge :status="$e->status ?? 'pending'"/></td><td>{{ isset($e->enrolled_at) ? \Carbon\Carbon::parse($e->enrolled_at)->format('d M Y') : '—' }}</td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No enrolments.</td></tr>@endforelse
</tbody></table></div>
