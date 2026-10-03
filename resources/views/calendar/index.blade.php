@extends('layouts.app')
@section('title', 'Calendar — ' . ($first->format('F Y') ?? ''))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $first->format('F Y') }}</h4>
    <div class="d-flex gap-1">
        @if(Route::has('calendar.index'))
            <a href="{{ route('calendar.index', ['year' => $prev->year, 'month' => $prev->month]) }}" class="btn btn-sm btn-outline-secondary">&laquo; Prev</a>
            <a href="{{ route('calendar.index', ['year' => now()->year, 'month' => now()->month]) }}" class="btn btn-sm btn-outline-secondary">Today</a>
            <a href="{{ route('calendar.index', ['year' => $next->year, 'month' => $next->month]) }}" class="btn btn-sm btn-outline-secondary">Next &raquo;</a>
        @endif
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body p-1 p-md-2">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dayName)
                            <th class="text-center small">{{ $dayName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(($weeks ?? []) as $week)
                        <tr>
                            @foreach($week as $day)
                                @php
                                    $key = $day->toDateString();
                                    $dayAppointments = ($appointments[$key] ?? collect());
                                    $isCurrentMonth = $day->month === $month;
                                    $isToday = $key === now()->toDateString();
                                @endphp
                                <td style="min-width: 130px; height: 110px; vertical-align: top;" class="{{ $isCurrentMonth ? '' : 'bg-light text-muted' }} {{ $isToday ? 'border-primary border-2' : '' }}">
                                    <div class="fw-medium small {{ $isToday ? 'text-primary' : '' }}">{{ $day->format('j') }}</div>
                                    @foreach($dayAppointments->take(4) as $appointment)
                                        <div class="small text-truncate mt-1">
                                            <span class="badge bg-info text-dark">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}</span>
                                            {{ trim(($appointment->candidate->first_name ?? '') . ' ' . ($appointment->candidate->last_name ?? '')) ?: ($appointment->type ?? 'Appointment') }}
                                        </div>
                                    @endforeach
                                    @if($dayAppointments->count() > 4)
                                        <div class="small text-muted">+{{ $dayAppointments->count() - 4 }} more</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
