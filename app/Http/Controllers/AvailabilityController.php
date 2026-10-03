<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\StaffAvailability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    protected function blockCandidates(Request $request): void
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
    }

    /**
     * Staff manage their own availabilities (admins/managers may browse all).
     */
    public function index(Request $request)
    {
        $this->blockCandidates($request);

        $role = $request->user()?->role?->name;
        $query = StaffAvailability::with(['user', 'branch'])->orderBy('day_of_week')->orderBy('start_time');

        if (! in_array($role, ['admin', 'manager'], true)) {
            $query->where('user_id', $request->user()->id);
        } elseif ($request->filled('staff_id')) {
            $query->where('user_id', $request->get('staff_id'));
        }

        $availabilities = $query->paginate(15)->withQueryString();
        $staff = User::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('availabilities.index', compact('availabilities', 'staff', 'branches'));
    }

    public function store(Request $request)
    {
        $this->blockCandidates($request);

        $data = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'nullable|boolean',
            'branch_id' => 'nullable|exists:branches,id',
            'staff_id' => 'nullable|exists:users,id',
        ]);

        $role = $request->user()?->role?->name;
        $userId = in_array($role, ['admin', 'manager'], true) && ! empty($data['staff_id'])
            ? (int) $data['staff_id']
            : $request->user()->id;

        StaffAvailability::create([
            'user_id' => $userId,
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'is_available' => (bool) ($data['is_available'] ?? true),
            'branch_id' => $data['branch_id'] ?? null,
        ]);

        return redirect()->route('availabilities.index')->with('status', 'Availability added.');
    }

    public function update(Request $request, StaffAvailability $availability)
    {
        $this->blockCandidates($request);

        $role = $request->user()?->role?->name;
        if (! in_array($role, ['admin', 'manager'], true) && $availability->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'nullable|boolean',
            'branch_id' => 'nullable|exists:branches,id',
        ]);
        $data['is_available'] = (bool) ($data['is_available'] ?? false);

        $availability->update($data);

        return redirect()->route('availabilities.index')->with('status', 'Availability updated.');
    }

    public function destroy(Request $request, StaffAvailability $availability)
    {
        $this->blockCandidates($request);

        $role = $request->user()?->role?->name;
        if (! in_array($role, ['admin', 'manager'], true) && $availability->user_id !== $request->user()->id) {
            abort(403);
        }

        $availability->delete();

        return redirect()->route('availabilities.index')->with('status', 'Availability removed.');
    }

    /**
     * Public JSON: free 30-minute slots for a staff member on a date,
     * derived from availabilities minus booked appointments.
     * No auth required; route must add throttle middleware.
     */
    public function slots(Request $request)
    {
        $data = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $data['date']);
        $dayOfWeek = (int) $date->dayOfWeek;

        $windows = StaffAvailability::where('user_id', $data['staff_id'])
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();

        $booked = Appointment::where('staff_id', $data['staff_id'])
            ->whereDate('appointment_date', $date->toDateString())
            ->whereNotIn('status', ['CANCELLED', 'cancelled'])
            ->pluck('appointment_date')
            ->map(fn ($d) => Carbon::parse($d)->format('H:i'))
            ->all();
        $bookedLookup = array_flip($booked);

        $slots = [];
        foreach ($windows as $window) {
            try {
                $start = Carbon::parse($date->toDateString().' '.(string) $window->start_time);
                $end = Carbon::parse($date->toDateString().' '.(string) $window->end_time);
            } catch (\Throwable $e) {
                continue;
            }
            $cursor = $start->copy();
            while ($cursor->lt($end)) {
                $slotEnd = $cursor->copy()->addMinutes(30);
                if ($slotEnd->gt($end)) {
                    break;
                }
                $label = $cursor->format('H:i');
                if (! isset($bookedLookup[$label])) {
                    $slots[] = $label;
                }
                $cursor = $slotEnd;
            }
        }

        sort($slots);

        return response()->json([
            'staff_id' => (int) $data['staff_id'],
            'date' => $date->toDateString(),
            'day_of_week' => $dayOfWeek,
            'slots' => array_values(array_unique($slots)),
        ]);
    }

    /**
     * Month calendar view data for Blade month grid.
     */
    public function calendar(Request $request)
    {
        $this->blockCandidates($request);

        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $year = max(2000, min(2100, $year));
        $month = max(1, min(12, $month));

        $first = Carbon::create($year, $month, 1)->startOfDay();
        // Monday-first grid.
        $gridStart = $first->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $first->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        $week = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $week[] = $cursor->copy();
            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }
            $cursor->addDay();
        }

        $query = Appointment::with(['candidate', 'staff'])
            ->whereBetween('appointment_date', [$gridStart->toDateTimeString(), $gridEnd->toDateTimeString()]);
        if ($request->user()?->role?->name === 'staff') {
            $query->where('staff_id', $request->user()->id);
        }
        $appointments = $query->orderBy('appointment_date')->get()->groupBy(
            fn ($a) => Carbon::parse($a->appointment_date)->toDateString()
        );

        $prev = $first->copy()->subMonth();
        $next = $first->copy()->addMonth();

        return view('calendar.index', [
            'weeks' => $weeks,
            'appointments' => $appointments,
            'year' => $year,
            'month' => $month,
            'first' => $first,
            'prev' => $prev,
            'next' => $next,
        ]);
    }
}
