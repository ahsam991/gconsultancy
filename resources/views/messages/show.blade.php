@extends('layouts.app')
@section('title', 'Thread — ' . (($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Thread: {{ $candidate->first_name }} {{ $candidate->last_name }}</h4>
    @if(Route::has('messages.index'))
        <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary">Back to threads</a>
    @endif
</div>
<div class="card shadow-sm mb-3">
    <div class="card-body" style="max-height: 55vh; overflow-y: auto;">
        @forelse(($messages ?? []) as $message)
            @php
                $mine = auth()->id() === $message->sender_id;
                $internal = (bool) $message->is_internal;
            @endphp
            <div class="d-flex mb-2 {{ $mine ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="p-2 rounded {{ $mine ? 'bg-primary text-white' : 'bg-light border' }} {{ $internal ? 'border-warning border-2' : '' }}" style="max-width: 70%;">
                    @if($internal)
                        <div><span class="badge bg-warning text-dark mb-1">Internal note (staff only)</span></div>
                    @endif
                    <div>{{ $message->body }}</div>
                    <div class="small {{ $mine ? 'text-white-50' : 'text-muted' }} mt-1">
                        {{ $message->sender->name ?? 'Unknown' }} &middot; {{ optional($message->created_at)->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">No messages in this thread yet.</p>
        @endforelse
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ Route::has('messages.store') ? route('messages.store') : url()->current() }}">
            @csrf
            <input type="hidden" name="candidate_id" value="{{ old('candidate_id', $candidate->id) }}">
            <div class="mb-3">
                <label class="form-label" for="body">Reply</label>
                <textarea name="body" id="body" rows="3" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if(auth()->user()?->role?->name !== 'candidate')
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_internal" id="is_internal" value="1" class="form-check-input" @checked(old('is_internal'))>
                    <label class="form-check-label" for="is_internal">Internal note (visible to staff only)</label>
                </div>
            @endif
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
    </div>
</div>
@endsection
