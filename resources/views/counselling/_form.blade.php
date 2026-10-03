@php $sess = $session ?? null; @endphp
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="candidate_id">Candidate <span class="text-danger">*</span></label>
        <select name="candidate_id" id="candidate_id" class="form-select @error('candidate_id') is-invalid @enderror" required>
            <option value="">Select candidate</option>
            @foreach(($candidates ?? []) as $c)
            <option value="{{ $c->id }}" @selected(old('candidate_id', $sess->candidate_id ?? request('candidate_id')) == $c->id)>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->uid }})</option>
            @endforeach
        </select>
        @error('candidate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="session_date">Session Date <span class="text-danger">*</span></label>
        <input type="date" name="session_date" id="session_date" value="{{ old('session_date', isset($sess->session_date) ? \Carbon\Carbon::parse($sess->session_date)->format('Y-m-d') : date('Y-m-d')) }}" class="form-control @error('session_date') is-invalid @enderror" required>
        @error('session_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="status">Status</label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
            @foreach(['scheduled','completed','follow_up','cancelled'] as $st)
            <option value="{{ $st }}" @selected(old('status', $sess->status ?? 'scheduled') === $st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label" for="purpose">Purpose <span class="text-danger">*</span></label>
        <input name="purpose" id="purpose" value="{{ old('purpose', $sess->purpose ?? '') }}" class="form-control @error('purpose') is-invalid @enderror" placeholder="e.g. Initial counselling — UK undergraduate options" required maxlength="255">
        @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label" for="discussion">Discussion <span class="text-danger">*</span></label>
        <textarea name="discussion" id="discussion" rows="4" class="form-control @error('discussion') is-invalid @enderror" placeholder="Summary of what was discussed with the candidate" required>{{ old('discussion', $sess->discussion ?? '') }}</textarea>
        @error('discussion')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label" for="recommendation">Recommendation</label>
        <textarea name="recommendation" id="recommendation" rows="3" class="form-control @error('recommendation') is-invalid @enderror" placeholder="Recommended universities, courses or next steps">{{ old('recommendation', $sess->recommendation ?? '') }}</textarea>
        @error('recommendation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="preferred_destination">Preferred Destination</label>
        <input name="preferred_destination" id="preferred_destination" value="{{ old('preferred_destination', $sess->preferred_destination ?? '') }}" class="form-control @error('preferred_destination') is-invalid @enderror" placeholder="e.g. United Kingdom" maxlength="100">
        @error('preferred_destination')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="preferred_level">Preferred Level</label>
        <input name="preferred_level" id="preferred_level" value="{{ old('preferred_level', $sess->preferred_level ?? '') }}" class="form-control @error('preferred_level') is-invalid @enderror" placeholder="e.g. Undergraduate" maxlength="100">
        @error('preferred_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="preferred_subject">Preferred Subject</label>
        <input name="preferred_subject" id="preferred_subject" value="{{ old('preferred_subject', $sess->preferred_subject ?? '') }}" class="form-control @error('preferred_subject') is-invalid @enderror" placeholder="e.g. Computer Science" maxlength="255">
        @error('preferred_subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="budget">Budget (GBP)</label>
        <input type="number" step="0.01" min="0" name="budget" id="budget" value="{{ old('budget', $sess->budget ?? '') }}" class="form-control @error('budget') is-invalid @enderror" placeholder="e.g. 25000">
        @error('budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="ielts">IELTS / English Level</label>
        <input name="ielts" id="ielts" value="{{ old('ielts', $sess->ielts ?? '') }}" class="form-control @error('ielts') is-invalid @enderror" placeholder="e.g. 6.5 overall" maxlength="50">
        @error('ielts')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="followup_date">Follow-up Date</label>
        <input type="date" name="followup_date" id="followup_date" value="{{ old('followup_date', isset($sess->followup_date) ? \Carbon\Carbon::parse($sess->followup_date)->format('Y-m-d') : '') }}" class="form-control @error('followup_date') is-invalid @enderror">
        @error('followup_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label" for="next_action">Next Action</label>
        <input name="next_action" id="next_action" value="{{ old('next_action', $sess->next_action ?? '') }}" class="form-control @error('next_action') is-invalid @enderror" placeholder="e.g. Send shortlisted universities by email" maxlength="255">
        @error('next_action')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
