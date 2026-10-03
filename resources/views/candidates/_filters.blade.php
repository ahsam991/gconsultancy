<x-filter-panel>
<form method="GET" action="{{ route('candidates.index') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-4"><label class="form-label small">Search</label><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, email, phone, UID"></div>
    <div class="col-md-2"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['new','contacted','counselling','applied','offered','visa','enrolled','rejected'] as $s)<option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Destination</label><select name="destination" class="form-select"><option value="">All</option>@foreach(['UK','Canada','Australia','USA','New Zealand','Ireland'] as $d)<option @selected(request('destination')==$d)>{{ $d }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Assigned</label><select name="assigned_to" class="form-select"><option value="">All</option>@foreach(($staff ?? []) as $u)<option value="{{ $u->id }}" @selected(request('assigned_to')==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
    <div class="col-md-2 d-flex gap-1"><button class="btn btn-primary">Filter</button><a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>
