<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Commission;
use App\Models\VisaCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function candidates(Request $request)
    {
        $query = Candidate::query();
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->get('from'));
        if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->get('to'));
        if ($request->filled('status')) $query->where('status', strtoupper($request->get('status')));
        if ($request->get('format') === 'csv') {
            return $this->exportCsv($query->orderBy('id')->get(), 'candidates-report.csv', ['uid', 'first_name', 'last_name', 'email', 'phone', 'status', 'preferred_destination', 'created_at']);
        }
        $rows = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        return view('reports.candidates', compact('rows'));
    }

    public function applications(Request $request)
    {
        $query = Application::with(['candidate', 'university']);
        if ($request->filled('status')) $query->where('status', strtoupper($request->get('status')));
        if ($request->filled('university_id')) $query->where('university_id', $request->get('university_id'));
        if ($request->get('format') === 'csv') {
            return $this->exportCsv($query->orderBy('id')->get(), 'applications-report.csv', ['uid', 'status', 'university_ref', 'created_at']);
        }
        $rows = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        return view('reports.applications', compact('rows'));
    }

    public function visa(Request $request)
    {
        $query = VisaCase::with(['application', 'candidate']);
        if ($request->filled('status')) $query->where('status', strtoupper($request->get('status')));
        if ($request->filled('outcome')) $query->where('result', $request->get('outcome'));
        if ($request->get('format') === 'csv') {
            return $this->exportCsv($query->orderBy('id')->get(), 'visa-report.csv', ['status', 'result', 'destination', 'reference_no', 'decision_date']);
        }
        $rows = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        return view('reports.visa', compact('rows'));
    }

    public function finance(Request $request)
    {
        $query = Commission::with(['university', 'candidate']);
        if ($request->filled('status')) $query->where('status', strtoupper($request->get('status')));
        if ($request->get('format') === 'csv') {
            return $this->exportCsv($query->orderBy('id')->get(), 'finance-report.csv', ['status', 'amount', 'currency', 'expected_date', 'received_date']);
        }
        $rows = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        return view('reports.finance', compact('rows'));
    }

    public function enrolments(Request $request)
    {
        $query = \App\Models\Enrolment::with(['candidate', 'application']);
        if ($request->filled('status')) $query->where('status', strtoupper($request->get('status')));
        if ($request->get('format') === 'csv') {
            return $this->exportCsv($query->orderBy('id')->get(), 'enrolments-report.csv', ['status', 'student_id_no', 'campus', 'enrolment_date']);
        }
        $rows = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        return view('reports.enrolments', compact('rows'));
    }

    public function staff(Request $request)
    {
        $query = \App\Models\User::with('role')->whereHas('role', fn ($q) => $q->whereIn('name', ['staff', 'manager']));
        if ($request->filled('q')) {
            $s = $request->get('q');
            $query->where(fn ($qq) => $qq->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        $rows = $query->withCount([
            'assignedCandidates as candidates_count' => fn ($q) => $q,
            'assignedApplications as applications_count' => fn ($q) => $q,
        ])->orderBy('name')->paginate(25)->withQueryString();
        return view('reports.staff', compact('rows'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'candidates');
        return match ($type) {
            'applications' => $this->applications($request->merge(['format' => 'csv'])),
            'visa' => $this->visa($request->merge(['format' => 'csv'])),
            'finance' => $this->finance($request->merge(['format' => 'csv'])),
            default => $this->candidates($request->merge(['format' => 'csv'])),
        };
    }

    protected function exportCsv($rows, string $filename, array $columns)
    {
        return response()->stream(function () use ($rows, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $r) {
                $line = [];
                foreach ($columns as $c) {
                    $line[] = $r->{$c} ?? '';
                }
                fputcsv($out, $line);
            }
            fclose($out);
        }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""]);
    }
}
