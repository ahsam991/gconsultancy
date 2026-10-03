@extends('layouts.app')
@section('title', 'Messages')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Message Threads</h4>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @forelse(($candidates ?? []) as $candidate)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        @if(Route::has('messages.show'))
                            <a href="{{ route('messages.show', $candidate) }}" class="fw-medium">{{ $candidate->first_name }} {{ $candidate->last_name }}</a>
                        @else
                            <span class="fw-medium">{{ $candidate->first_name }} {{ $candidate->last_name }}</span>
                        @endif
                        <div class="small text-muted">{{ $candidate->email ?? 'No email' }} &middot; {{ $candidate->uid ?? '' }}</div>
                        @if(optional($candidate->messages)->first())
                            <div class="small text-truncate" style="max-width: 420px;">{{ \Illuminate\Support\Str::limit(optional($candidate->messages)->first()->body, 80) }}</div>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if(($candidate->unread_count ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill">{{ $candidate->unread_count }} unread</span>
                        @else
                            <span class="badge bg-secondary rounded-pill">0 unread</span>
                        @endif
                        @if(Route::has('messages.show'))
                            <a href="{{ route('messages.show', $candidate) }}" class="btn btn-sm btn-outline-primary">Open</a>
                        @endif
                    </div>
                </li>
            @empty
                <li class="list-group-item text-muted">No message threads yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@if(isset($candidates) && method_exists($candidates, 'links'))
    <div class="mt-3">{{ $candidates->links() }}</div>
@endif
@endsection
