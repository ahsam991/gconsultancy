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

    /* ------------------------------------------------------------------
     | New panel reports (added, existing methods untouched)
     |------------------------------------------------------------------ */

    protected function blockCandidate(Request $request): void
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
    }

    /**
     * Generic CSV/XLSX responder for the NEW report methods only.
     * Existing exportCsv()/export() above are never modified or called here
     * for legacy types. $table is a collection of plain row arrays ordered
     * to match $headings. Returns null when no ?format=csv|xlsx requested.
     */
    protected function exportCsvOrExcel(Request $request, $table, array $headings, string $basename)
    {
        $format = strtolower((string) $request->get('format', ''));
        if (! in_array($format, ['csv', 'xlsx', 'xls'], true)) {
            return null;
        }
        $rows = $table instanceof \Illuminate\Support\Collection ? $table->values() : collect($table)->values();
        if ($format === 'csv') {
            $filename = $basename.'.csv';

            return response()->stream(function () use ($rows, $headings) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $headings);
                foreach ($rows as $r) {
                    fputcsv($out, is_array($r) ? array_values($r) : [(string) $r]);
                }
                fclose($out);
            }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""]);
        }
        $filename = $basename.'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PanelExport($rows, $headings), $filename);
    }

    protected function applyDateRange($query, Request $request, string $column = 'created_at')
    {
        if ($request->filled('from')) {
            $query->whereDate($column, '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate($column, '<=', $request->get('to'));
        }

        return $query;
    }

    public function funnel(Request $request)
    {
        $this->blockCandidate($request);
        $staffId = $request->get('staff_id');
        $country = $request->get('country');
        $from = $request->get('from');
        $to = $request->get('to');

        $leadsQ = \App\Models\Lead::query();
        $candQ = \App\Models\Candidate::query();
        $appQ = \App\Models\Application::query();
        $offerQ = \App\Models\Offer::query();
        $depositQ = \App\Models\Deposit::query();
        $visaQ = \App\Models\VisaCase::query();
        $enrolQ = \App\Models\Enrolment::query();

        if ($staffId) {
            $leadsQ->where('assigned_to', $staffId);
            $candQ->where('assigned_staff_id', $staffId);
            $appQ->where('assigned_staff_id', $staffId);
            $offerQ->whereHas('application', fn ($q) => $q->where('assigned_staff_id', $staffId));
            $depositQ->whereHas('application', fn ($q) => $q->where('assigned_staff_id', $staffId));
            $visaQ->whereHas('application', fn ($q) => $q->where('assigned_staff_id', $staffId));
            $enrolQ->whereHas('application', fn ($q) => $q->where('assigned_staff_id', $staffId));
        }
        if ($country) {
            $leadsQ->where(fn ($q) => $q->where('preferred_destination', $country)->orWhereHas('country', fn ($c) => $c->where('name', $country)));
            $candQ->where(fn ($q) => $q->where('preferred_destination', $country)->orWhereHas('country', fn ($c) => $c->where('name', $country)));
            $appQ->whereHas('candidate', fn ($q) => $q->where('preferred_destination', $country));
            $offerQ->whereHas('application.candidate', fn ($q) => $q->where('preferred_destination', $country));
            $depositQ->whereHas('application.candidate', fn ($q) => $q->where('preferred_destination', $country));
            $visaQ->where(fn ($q) => $q->where('destination', $country)->orWhereHas('candidate', fn ($c) => $c->where('preferred_destination', $country)));
            $enrolQ->whereHas('candidate', fn ($q) => $q->where('preferred_destination', $country));
        }
        foreach ([[$leadsQ, 'created_at'], [$candQ, 'created_at'], [$appQ, 'created_at'], [$offerQ, 'created_at'], [$depositQ, 'created_at'], [$visaQ, 'created_at'], [$enrolQ, 'created_at']] as [$q, $col]) {
            $this->applyDateRange($q, $request, $col);
        }

        $counts = [
            'Leads' => (clone $leadsQ)->count(),
            'Candidates' => (clone $candQ)->count(),
            'Applications' => (clone $appQ)->count(),
            'Offers' => (clone $offerQ)->count(),
            'Deposits' => (clone $depositQ)->where('paid_amount', '>', 0)->count(),
            'Visas' => (clone $visaQ)->where(fn ($q) => $q->whereIn('result', ['APPROVED', 'GRANTED', 'ISSUED'])->orWhere('status', 'APPROVED'))->count(),
            'Enrolments' => (clone $enrolQ)->count(),
        ];
        // Visa stage: fall back to all visa cases when no outcome-filtered rows exist.
        if ($counts['Visas'] === 0) {
            $counts['Visas'] = (clone $visaQ)->count();
        }

        $stages = [];
        $prev = null;
        $first = reset($counts);
        foreach ($counts as $label => $count) {
            $ofPrev = $prev === null || $prev === 0 ? null : round($count / $prev * 100, 1);
            $ofFirst = $first === 0 ? null : round($count / max($first, 1) * 100, 1);
            $stages[] = ['label' => $label, 'count' => $count, 'of_prev' => $ofPrev, 'of_first' => $ofFirst];
            $prev = $count;
        }

        $staff = \App\Models\User::with('role')->whereHas('role', fn ($q) => $q->whereIn('name', ['staff', 'manager']))->orderBy('name')->limit(200)->get();
        $countries = \App\Models\Country::orderBy('name')->limit(200)->get();

        if ($export = $this->exportCsvOrExcel($request, collect($stages)->map(fn ($s) => [$s['label'], $s['count'], $s['of_prev'] ?? '', $s['of_first'] ?? '']), ['Stage', 'Count', '% of previous', '% of leads'], 'funnel-report')) {
            return $export;
        }

        return view('reports.funnel', compact('stages', 'staff', 'countries'));
    }

    public function marketing(Request $request)
    {
        $this->blockCandidate($request);
        $rows = \App\Models\Lead::selectRaw('COALESCE(lead_sources.name, ?) as source, COUNT(leads.id) as leads, SUM(CASE WHEN leads.status = ? THEN 1 ELSE 0 END) as converted', ['Unknown', 'CONVERTED'])
            ->leftJoin('lead_sources', 'lead_sources.id', '=', 'leads.source_id')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('leads.created_at', '>=', $request->get('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('leads.created_at', '<=', $request->get('to')))
            ->groupBy('lead_sources.name')
            ->orderByDesc('leads')
            ->get()
            ->map(function ($r) {
                $r->conversion = $r->leads > 0 ? round($r->converted / $r->leads * 100, 1) : 0;

                return $r;
            });

        if ($export = $this->exportCsvOrExcel($request, $rows->map(fn ($r) => [$r->source, $r->leads, $r->converted, $r->conversion.'%']), ['Source', 'Leads', 'Converted', 'Conversion'], 'marketing-report')) {
            return $export;
        }

        return view('reports.marketing', compact('rows'));
    }

    public function universities(Request $request)
    {
        $this->blockCandidate($request);
        $rows = \App\Models\Application::selectRaw('universities.name as university, COUNT(applications.id) as applications')
            ->join('universities', 'universities.id', '=', 'applications.university_id')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('applications.created_at', '>=', $request->get('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('applications.created_at', '<=', $request->get('to')))
            ->groupBy('universities.name')
            ->orderByDesc('applications')
            ->limit(50)
            ->get();
        $total = $rows->sum('applications');
        $rows->each(function ($r) use ($total) {
            $r->share = $total > 0 ? round($r->applications / $total * 100, 1) : 0;
        });

        if ($export = $this->exportCsvOrExcel($request, $rows->map(fn ($r) => [$r->university, $r->applications, $r->share.'%']), ['University', 'Applications', 'Share'], 'universities-report')) {
            return $export;
        }

        return view('reports.universities', compact('rows', 'total'));
    }

    public function deadlines(Request $request)
    {
        $this->blockCandidate($request);
        $today = now()->toDateString();
        $intakeLimit = now()->addDays(14)->toDateString();
        $docLimit = now()->addDays(30)->toDateString();

        $intakes = \App\Models\Application::with(['candidate', 'university', 'intake'])
            ->whereHas('intake', fn ($q) => $q->whereDate('deadline', '>=', $today)->whereDate('deadline', '<=', $intakeLimit))
            ->orderBy('id')
            ->limit(200)
            ->get();

        $docs = \App\Models\CandidateDocument::with(['candidate', 'documentType'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate('expiry_date', '<=', $docLimit)
            ->orderBy('expiry_date')
            ->limit(200)
            ->get();

        $table = $request->get('table', 'intakes');
        if ($table === 'docs') {
            if ($export = $this->exportCsvOrExcel($request, $docs->map(fn ($d) => [$d->candidate->first_name.' '.$d->candidate->last_name ?? '', $d->documentType->name ?? $d->original_filename, $d->expiry_date?->toDateString()]), ['Candidate', 'Document', 'Expiry date'], 'documents-expiring')) {
                return $export;
            }
        } else {
            if ($export = $this->exportCsvOrExcel($request, $intakes->map(fn ($a) => [$a->uid, trim(($a->candidate->first_name ?? '').' '.($a->candidate->last_name ?? '')), $a->university->name ?? '', $a->intake->name ?? '', $a->intake->deadline?->toDateString()]), ['Application', 'Candidate', 'University', 'Intake', 'Deadline'], 'intake-deadlines')) {
                return $export;
            }
        }

        return view('reports.deadlines', compact('intakes', 'docs', 'intakeLimit', 'docLimit'));
    }

    public function sla(Request $request)
    {
        $this->blockCandidate($request);
        $notice = null;
        if (! class_exists(\App\Models\SlaBreach::class)) {
            $breaches = collect();
            $notice = 'SLA breach tracking is not enabled (SlaBreach model missing).';
        } else {
            try {
                $breaches = \App\Models\SlaBreach::with('policy')
                    ->when($request->filled('from'), fn ($q) => $q->whereDate('detected_at', '>=', $request->get('from')))
                    ->when($request->filled('to'), fn ($q) => $q->whereDate('detected_at', '<=', $request->get('to')))
                    ->orderByDesc('detected_at')
                    ->paginate(25)
                    ->withQueryString();
            } catch (\Throwable $e) {
                $breaches = collect();
                $notice = 'SLA breach table is unavailable: '.$e->getMessage();
            }
        }

        if ($export = $this->exportCsvOrExcel($request, collect($breaches instanceof \Illuminate\Pagination\AbstractPaginator ? $breaches->items() : $breaches)->map(fn ($b) => is_array($b) ? $b : [$b->id ?? '', $b->related_type ?? '', $b->related_id ?? '', $b->detected_at ?? '']), ['ID', 'Related type', 'Related ID', 'Detected at'], 'sla-breaches')) {
            return $export;
        }

        return view('reports.sla', compact('breaches', 'notice'));
    }
}
