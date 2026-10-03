@extends('layouts.app')
@section('title', 'Automation Rules')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Automation Rules</h4>
    @if(Route::has('automation.logs'))
        <a href="{{ route('automation.logs') }}" class="btn btn-sm btn-outline-secondary">View Logs</a>
    @endif
</div>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <x-datatable id="automationRulesTable">
            <thead><tr><th>Name</th><th>Trigger</th><th>Status Filter</th><th>Action</th><th>Active</th><th>Runs</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse(($rules ?? []) as $rule)
                    <tr>
                        <td>{{ $rule->name }}</td>
                        <td><code>{{ $rule->trigger_event }}</code></td>
                        <td>{{ $rule->trigger_status ?? '—' }}</td>
                        <td><code>{{ $rule->action_type }}</code></td>
                        <td><x-status-badge :status="$rule->active ? 'yes' : 'no'" /></td>
                        <td>{{ $rule->logs_count ?? $rule->logs()->count() }}</td>
                        <td class="text-nowrap">
                            @if(Route::has('automation.rules.update'))
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#ruleEdit{{ $rule->id }}">Edit</button>
                            @endif
                            @if(Route::has('automation.rules.destroy'))
                                <form method="POST" action="{{ route('automation.rules.destroy', $rule) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">No automation rules yet. Use the form below to create one.</td></tr>
                @endforelse
            </tbody>
        </x-datatable>
    </div>
</div>
@if(isset($rules) && method_exists($rules, 'links'))
    <div class="mt-3 mb-3">{{ $rules->links() }}</div>
@endif
@if(Route::has('automation.rules.store'))
    <div class="card shadow-sm">
        <div class="card-header fw-medium">Create Rule</div>
        <div class="card-body">
            <form method="POST" action="{{ route('automation.rules.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Trigger event <span class="text-danger">*</span></label>
                        <select name="trigger_event" class="form-select @error('trigger_event') is-invalid @enderror" required>
                            @foreach(($triggerEvents ?? ['status_changed','document_rejected','task_overdue','offer_received']) as $event)
                                <option value="{{ $event }}" @selected(old('trigger_event') === $event)>{{ $event }}</option>
                            @endforeach
                        </select>
                        @error('trigger_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Trigger status (optional)</label>
                        <input name="trigger_status" value="{{ old('trigger_status') }}" class="form-control" placeholder="e.g. REJECTED">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Action <span class="text-danger">*</span></label>
                        <select name="action_type" class="form-select @error('action_type') is-invalid @enderror" required>
                            @foreach(($actionTypes ?? ['create_task','notify_staff','email_candidate','set_followup']) as $action)
                                <option value="{{ $action }}" @selected(old('action_type') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                        @error('action_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-7 mb-3">
                        <label class="form-label">Action config (JSON)</label>
                        <textarea name="action_config" rows="3" class="form-control font-monospace @error('action_config') is-invalid @enderror" placeholder='{"title":"Follow up","assigned_to":1,"priority":"High"}'>{{ old('action_config') }}</textarea>
                        @error('action_config')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="active" id="rule_active" value="1" class="form-check-input" @checked(old('active', true))>
                            <label class="form-check-label" for="rule_active">Active</label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary">Create Rule</button>
            </form>
        </div>
    </div>
@endif
@foreach(($rules ?? []) as $rule)
    @if(Route::has('automation.rules.update'))
        <x-modal id="ruleEdit{{ $rule->id }}" title="Edit Rule: {{ $rule->name }}">
            <form method="POST" action="{{ route('automation.rules.update', $rule) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input name="name" value="{{ old('name', $rule->name) }}" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trigger event</label>
                        <select name="trigger_event" class="form-select">
                            @foreach(($triggerEvents ?? ['status_changed','document_rejected','task_overdue','offer_received']) as $event)
                                <option value="{{ $event }}" @selected(old('trigger_event', $rule->trigger_event) === $event)>{{ $event }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trigger status</label>
                        <input name="trigger_status" value="{{ old('trigger_status', $rule->trigger_status) }}" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Action</label>
                        <select name="action_type" class="form-select">
                            @foreach(($actionTypes ?? ['create_task','notify_staff','email_candidate','set_followup']) as $action)
                                <option value="{{ $action }}" @selected(old('action_type', $rule->action_type) === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="active" value="1" class="form-check-input" @checked(old('active', $rule->active))>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Action config (JSON)</label>
                    <textarea name="action_config" rows="3" class="form-control font-monospace">{{ old('action_config', is_array($rule->action_config) ? json_encode($rule->action_config, JSON_PRETTY_PRINT) : $rule->action_config) }}</textarea>
                </div>
                <button class="btn btn-primary">Update Rule</button>
            </form>
        </x-modal>
    @endif
@endforeach
@endsection
