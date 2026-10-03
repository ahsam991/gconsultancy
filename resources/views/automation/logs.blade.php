@extends('layouts.app')
@section('title', 'Automation Logs')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Automation Logs</h4>
    @if(Route::has('automation.rules'))
        <a href="{{ route('automation.rules') }}" class="btn btn-sm btn-outline-secondary">Back to Rules</a>
    @endif
</div>
@if(Route::has('automation.logs'))
    <x-filter-panel>
        <form method="GET" action="{{ route('automation.logs') }}" class="row g-2 align-items-end w-100">
            <div class="col-md-4">
                <label class="form-label small">Trigger event</label>
                <select name="event" class="form-select">
                    <option value="">All events</option>
                    @foreach(['status_changed','document_rejected','task_overdue','offer_received'] as $event)
                        <option value="{{ $event }}" @selected(request('event') === $event)>{{ $event }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-1">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('automation.logs') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </x-filter-panel>
@endif
<div class="card shadow-sm">
    <div class="card-body">
        <x-datatable id="automationLogsTable">
            <thead><tr><th>When</th><th>Rule</th><th>Event</th><th>Related</th><th>Result</th></tr></thead>
            <tbody>
                @forelse(($logs ?? []) as $log)
                    <tr>
                        <td>{{ optional($log->created_at)->format('d M Y H:i') }}</td>
                        <td>{{ $log->rule->name ?? ('Rule #' . ($log->automation_rule_id ?? '—')) }}</td>
                        <td><code>{{ $log->trigger_event }}</code></td>
                        <td>{{ $log->related_type ? class_basename($log->related_type) . ' #' . $log->related_id : '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($log->result ?? '', 120) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">No automation runs logged yet.</td></tr>
                @endforelse
            </tbody>
        </x-datatable>
    </div>
</div>
@if(isset($logs) && method_exists($logs, 'links'))
    <div class="mt-3">{{ $logs->links() }}</div>
@endif
@endsection
