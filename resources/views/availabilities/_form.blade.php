@php $availability = $availability ?? null; @endphp
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Day of week <span class="text-danger">*</span></label>
        <select name="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>
            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $day)
                <option value="{{ $i }}" @selected((int) old('day_of_week', $availability->day_of_week ?? request('day', 1)) === $i)>{{ $day }}</option>
            @endforeach
        </select>
        @error('day_of_week')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Start time <span class="text-danger">*</span></label>
        <input type="time" name="start_time" value="{{ old('start_time', isset($availability->start_time) ? substr((string) $availability->start_time, 0, 5) : '09:00') }}" class="form-control @error('start_time') is-invalid @enderror" required>
        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">End time <span class="text-danger">*</span></label>
        <input type="time" name="end_time" value="{{ old('end_time', isset($availability->end_time) ? substr((string) $availability->end_time, 0, 5) : '17:00') }}" class="form-control @error('end_time') is-invalid @enderror" required>
        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Branch</label>
        <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
            <option value="">—</option>
            @foreach(($branches ?? []) as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $availability->branch_id ?? '') === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @if(in_array(auth()->user()?->role?->name, ['admin', 'manager'], true))
        <div class="col-md-6 mb-3">
            <label class="form-label">Staff member</label>
            <select name="staff_id" class="form-select">
                <option value="">Myself</option>
                @foreach(($staff ?? []) as $member)
                    <option value="{{ $member->id }}" @selected((string) old('staff_id') === (string) $member->id)>{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-6 mb-3">
        <div class="form-check mt-4">
            <input type="checkbox" name="is_available" id="is_available_{{ $availability->id ?? 'new' }}" value="1" class="form-check-input" @checked(old('is_available', $availability->is_available ?? true))>
            <label class="form-check-label" for="is_available_{{ $availability->id ?? 'new' }}">Available in this window</label>
        </div>
    </div>
</div>
