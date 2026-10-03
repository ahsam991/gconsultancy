<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Candidate;
use App\Models\CandidateDocument;
use App\Models\DocumentType;
use App\Services\AuditService;
use App\Services\DocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role?->name;
        $query = CandidateDocument::with(['candidate', 'documentType', 'application']);
        if ($role === 'staff') {
            $query->whereHas('candidate', fn ($q) => $q->where('assigned_staff_id', $user->id));
        }
        if ($role === 'candidate') {
            $query->whereHas('candidate', fn ($q) => $q->where('user_id', $user->id));
        }
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->get('candidate_id'));
        }
        $documents = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('documents.index', compact('documents'));
    }

    public function create(Request $request)
    {
        $types = DocumentType::orderBy('name')->get();
        $candidates = Candidate::orderBy('first_name')->limit(500)->get();
        return view('documents.create', compact('types', 'candidates'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $candidate = Candidate::findOrFail($request->candidate_id);
        $this->authorize('view', $candidate);
        $typeId = $request->document_type_id
            ?? DocumentType::where('name', $request->type)->value('id')
            ?? DocumentType::firstOrCreate(['name' => $request->type ?? 'Other'], ['category' => 'Other', 'active' => true])->id;
        $doc = DocumentService::store(
            $request->file('file'),
            $candidate,
            $typeId,
            $request->application_id,
            $request->user()->id
        );
        if ($request->filled('notes')) {
            $doc->update(['notes' => $request->notes]);
        }
        return redirect()->route('documents.show', $doc)->with('status', 'Document uploaded.');
    }

    public function show(CandidateDocument $document)
    {
        $this->authorize('view', $document);
        $document->load(['candidate', 'documentType', 'application', 'versions.uploader']);
        return view('documents.show', compact('document'));
    }

    public function edit(CandidateDocument $document)
    {
        $this->authorize('view', $document);
        $types = DocumentType::orderBy('name')->get();
        return view('documents.edit', compact('document', 'types'));
    }

    public function update(Request $request, CandidateDocument $document)
    {
        $this->authorize('view', $document);
        $request->validate(['notes' => 'nullable|string']);
        $document->update($request->only('notes'));
        AuditService::log('document.updated', $document, null, $document->toArray());
        return redirect()->route('documents.show', $document)->with('status', 'Document updated.');
    }

    public function destroy(CandidateDocument $document)
    {
        $this->authorize('delete', $document);
        $document->delete();
        AuditService::log('document.deleted', $document);
        return redirect()->route('documents.index')->with('status', 'Document deleted.');
    }

    public function download(CandidateDocument $document)
    {
        return DocumentService::download($document);
    }

    public function verify(Request $request, CandidateDocument $document)
    {
        $this->authorize('verify', $document);
        $document->update([
            'verification_status' => 'VERIFIED',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);
        AuditService::log('document.verified', $document, null, $document->toArray());
        \App\Services\NotifyService::documentVerified($document->fresh());
        return redirect()->back()->with('status', 'Document verified.');
    }

    public function reject(Request $request, CandidateDocument $document)
    {
        $this->authorize('verify', $document);
        $request->validate(['rejection_reason' => 'required|string']);
        $document->update([
            'verification_status' => 'REJECTED',
            'rejection_reason' => $request->rejection_reason,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);
        AuditService::log('document.rejected', $document, null, $document->toArray());
        return redirect()->back()->with('status', 'Document rejected.');
    }
}
