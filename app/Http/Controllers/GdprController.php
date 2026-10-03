<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\DataAccessLog;
use App\Models\GdprConsent;
use App\Models\GdprRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;

class GdprController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (auth()->user()?->role?->name !== 'admin') {
            abort(403, 'Admin access only.');
        }
    }

    public function consents(Request $request)
    {
        $this->ensureAdmin();
        $query = GdprConsent::with('candidate')->orderByDesc('created_at');
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->integer('candidate_id'));
        }
        if ($request->filled('consent_type')) {
            $query->where('consent_type', $request->string('consent_type'));
        }
        $consents = $query->paginate(15)->withQueryString();
        $candidates = Candidate::orderBy('first_name')->limit(500)->get(['id', 'first_name', 'last_name', 'email']);

        return view('gdpr.consents', compact('consents', 'candidates'));
    }

    public function requests(Request $request)
    {
        $this->ensureAdmin();
        $query = GdprRequest::with(['candidate', 'requester'])->orderByDesc('created_at');
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->integer('candidate_id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        $gdprRequests = $query->paginate(15)->withQueryString();
        $candidates = Candidate::orderBy('first_name')->limit(500)->get(['id', 'first_name', 'last_name', 'email']);

        return view('gdpr.requests', compact('gdprRequests', 'candidates'));
    }

    public function storeRequest(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'type' => 'required|in:export,anonymize,delete',
            'notes' => 'nullable|string|max:2000',
        ]);
        $gdprRequest = GdprRequest::create([
            'candidate_id' => $data['candidate_id'],
            'type' => $data['type'],
            'status' => 'pending',
            'requested_by' => auth()->id(),
            'notes' => $data['notes'] ?? null,
        ]);
        AuditService::log('gdpr.request.created', null, null, $gdprRequest->toArray());

        return redirect()->route('gdpr.requests')->with('status', 'GDPR request recorded.');
    }

    public function updateRequest(Request $request, GdprRequest $gdprRequest)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'notes' => 'nullable|string|max:2000',
        ]);

        $gdprRequest->status = $data['status'];
        $gdprRequest->notes = $data['notes'] ?? $gdprRequest->notes;

        if ($data['status'] === 'completed' && $gdprRequest->completed_at === null) {
            $gdprRequest->completed_at = now();
            $gdprRequest->save();

            DataAccessLog::create([
                'user_id' => auth()->id(),
                'candidate_id' => $gdprRequest->candidate_id,
                'action' => 'gdpr_'.$gdprRequest->type.'_completed',
                'purpose' => 'GDPR request #'.$gdprRequest->id.' ('.$gdprRequest->type.') completed',
                'ip' => $request->ip(),
            ]);
            AuditService::log('gdpr.request.completed', null, null, $gdprRequest->toArray());

            if ($gdprRequest->type === 'export') {
                return $this->exportDownload($gdprRequest);
            }
            if ($gdprRequest->type === 'anonymize') {
                $this->anonymizeCandidate($gdprRequest->candidate_id);
            }
            if ($gdprRequest->type === 'delete') {
                $candidate = Candidate::find($gdprRequest->candidate_id);
                if ($candidate && ! $candidate->trashed()) {
                    $candidate->delete();
                }
            }
        } else {
            $gdprRequest->save();
            AuditService::log('gdpr.request.updated', null, null, $gdprRequest->toArray());
        }

        return redirect()->route('gdpr.requests')->with('status', 'GDPR request updated.');
    }

    public function downloadExport(GdprRequest $gdprRequest)
    {
        $this->ensureAdmin();
        if ($gdprRequest->type !== 'export') {
            abort(404, 'Only export requests can be downloaded.');
        }

        DataAccessLog::create([
            'user_id' => auth()->id(),
            'candidate_id' => $gdprRequest->candidate_id,
            'action' => 'gdpr_export_downloaded',
            'purpose' => 'SAR export download for request #'.$gdprRequest->id,
            'ip' => request()->ip(),
        ]);

        return $this->exportDownload($gdprRequest);
    }

    protected function exportDownload(GdprRequest $gdprRequest)
    {
        $candidate = Candidate::withTrashed()->with([
            'applications.university',
            'applications.course',
            'documents.documentType',
            'consents',
            'addresses',
            'qualifications',
            'englishTests',
        ])->findOrFail($gdprRequest->candidate_id);

        $dump = [
            'request' => $gdprRequest->only(['id', 'type', 'status', 'completed_at', 'notes', 'created_at']),
            'candidate' => $candidate->toArray(),
            'applications' => $candidate->applications->map(fn ($a) => $a->toArray())->values()->all(),
            // Documents list only (metadata, never file contents).
            'documents' => $candidate->documents->map(fn ($d) => $d->only([
                'id', 'application_id', 'document_type_id', 'original_filename',
                'mime', 'size_kb', 'verification_status', 'version', 'created_at',
            ]))->values()->all(),
            'note' => 'ZIP archive optional — JSON dump provided to avoid ZIP dependency issues.',
        ];

        $filename = 'sar-candidate-'.$candidate->id.'-'.now()->format('Ymd-His').'.json';

        // Stream JSON download (no ZIP dependency).
        return response()->streamDownload(function () use ($dump) {
            echo json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    protected function anonymizeCandidate(int $candidateId): void
    {
        $candidate = Candidate::find($candidateId);
        if (! $candidate) {
            return;
        }
        // Replace PII; financial records (invoices, payments, commissions) are untouched.
        $candidate->update([
            'first_name' => 'ANONYMIZED_'.$candidate->id,
            'last_name' => 'ANONYMIZED_'.$candidate->id,
            'email' => 'anonymized_'.$candidate->id.'@deleted.local',
            'phone' => 'ANONYMIZED_'.$candidate->id,
            'passport_no' => 'ANONYMIZED_'.$candidate->id,
            'address' => 'ANONYMIZED',
            'city' => 'ANONYMIZED',
            'nationality' => null,
        ]);
    }

    public function accessLogs(Request $request)
    {
        $this->ensureAdmin();
        $query = DataAccessLog::with(['user', 'candidate'])->orderByDesc('created_at');
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->integer('candidate_id'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->string('action').'%');
        }
        $logs = $query->paginate(20)->withQueryString();
        $candidates = Candidate::withTrashed()->orderBy('first_name')->limit(500)->get(['id', 'first_name', 'last_name', 'email']);

        return view('gdpr.access-logs', compact('logs', 'candidates'));
    }
}
