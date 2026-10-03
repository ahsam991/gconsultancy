<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Appointment;
use App\Models\Candidate;
use App\Models\Commission;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role?->name ?? 'staff';

        if ($role === 'candidate') {
            return redirect()->route('portal.dashboard');
        }

        $candidateQuery = Candidate::query();
        $applicationQuery = Application::query();

        if ($role === 'staff') {
            $candidateQuery->where('assigned_staff_id', $user->id);
            $applicationQuery->where('assigned_staff_id', $user->id);
        }

        $counts = [
            'candidates' => (clone $candidateQuery)->count(),
            'applications' => (clone $applicationQuery)->count(),
            'offers' => (clone $applicationQuery)->whereIn('status', ['CONDITIONAL_OFFER', 'UNCONDITIONAL_OFFER'])->count(),
            'visa' => (clone $applicationQuery)->whereIn('status', ['VISA_PREPARATION', 'VISA_APPLIED', 'VISA_APPROVED', 'VISA_REFUSED'])->count(),
            'enrolled' => (clone $applicationQuery)->where('status', 'ENROLLED')->count(),
            'tasks_due' => Task::when($role === 'staff', fn ($q) => $q->where('assigned_user_id', $user->id))
                ->where('due_date', '<=', now()->addDays(7))
                ->whereNotIn('status', ['COMPLETED', 'CANCELLED'])
                ->count(),
            'commissions' => $role === 'staff' ? 0 : Commission::sum('amount'),
        ];

        $byStatus = (clone $applicationQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentApplications = (clone $applicationQuery)
            ->with(['candidate', 'university', 'course'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $tasks = Task::with('candidate')
            ->when($role === 'staff', fn ($q) => $q->where('assigned_user_id', $user->id))
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED'])
            ->orderBy('due_date')->limit(6)->get();

        $appointments = Appointment::with('candidate')
            ->when($role === 'staff', fn ($q) => $q->where('staff_id', $user->id))
            ->whereDate('appointment_date', now()->toDateString())
            ->orderBy('appointment_date')->limit(6)->get();

        $appsByMonth = (clone $applicationQuery)
            ->selectRaw("strftime('%Y-%m', created_at) as ym, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(7)->startOfMonth())
            ->groupBy('ym')->orderBy('ym')->pluck('total', 'ym');

        $visaOutcomes = (clone $applicationQuery)
            ->whereIn('status', ['VISA_APPROVED', 'VISA_REFUSED', 'VISA_APPLIED'])
            ->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $funnelStages = ['SUBMITTED', 'CONDITIONAL_OFFER', 'UNCONDITIONAL_OFFER', 'DEPOSIT_PAID', 'CAS_ISSUED', 'VISA_APPROVED', 'ENROLLED'];
        $funnel = [];
        foreach ($funnelStages as $stage) {
            $funnel[$stage] = (clone $applicationQuery)->where('status', $stage)->count();
        }
        $funnelBase = max(1, (clone $applicationQuery)->whereIn('status', array_merge($funnelStages, ['UNDER_REVIEW', 'ACKNOWLEDGED']))->count());

        return view('dashboard.index', compact('counts', 'byStatus', 'recentApplications', 'role', 'tasks', 'appointments', 'appsByMonth', 'visaOutcomes', 'funnel', 'funnelBase'));
    }
}
