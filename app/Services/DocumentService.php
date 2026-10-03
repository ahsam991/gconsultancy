<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\CandidateDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class DocumentService
{
    public static function store(
        UploadedFile $file,
        Candidate $candidate,
        $typeId,
        $applicationId = null,
        $userId = null
    ): CandidateDocument {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $maxKb = 10240;

        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, $allowed, true)) {
            throw ValidationException::withMessages([
                'file' => ['Invalid file type. Allowed: pdf,jpg,jpeg,png,doc,docx.'],
            ]);
        }

        if ($file->getSize() > $maxKb * 1024) {
            throw ValidationException::withMessages([
                'file' => ['File too large. Max 10MB.'],
            ]);
        }

        $dir = "candidates/{$candidate->id}";
        $storedFilename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
        $path = $file->storeAs($dir, $storedFilename, 'local');

        $maxVersion = (int) CandidateDocument::where('candidate_id', $candidate->id)
            ->where('document_type_id', $typeId)
            ->when($applicationId, fn ($q) => $q->where('application_id', $applicationId))
            ->max('version');

        $doc = CandidateDocument::create([
            'candidate_id' => $candidate->id,
            'application_id' => $applicationId,
            'document_type_id' => $typeId,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'mime' => $file->getMimeType(),
            'size_kb' => (int) ceil($file->getSize() / 1024),
            'uploaded_by' => $userId ?? auth()->id(),
            'verification_status' => 'UPLOADED',
            'version' => $maxVersion + 1,
        ]);

        \App\Models\DocumentVersion::create([
            'candidate_document_id' => $doc->id,
            'version' => $doc->version,
            'stored_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size_kb' => (int) ceil($file->getSize() / 1024),
            'uploaded_by' => $userId ?? auth()->id(),
        ]);

        AuditService::log('document.uploaded', $doc, null, $doc->toArray());

        return $doc;
    }

    /**
     * Document checklist for an application: required types (from course
     * requirements, falling back to default-required types) mapped to
     * Required / Uploaded / Verified / Missing + completion %.
     */
    public static function checklist(\App\Models\Application $application): array
    {
        $application->loadMissing(['course.requirements.documentType', 'candidate']);
        $required = $application->course?->requirements->where('required', true);
        if (! $required || $required->isEmpty()) {
            $required = \App\Models\DocumentType::where('required_default', true)->where('active', true)->get()
                ->map(fn ($t) => (object) ['document_type_id' => $t->id, 'documentType' => $t]);
        }
        $docs = \App\Models\CandidateDocument::where('candidate_id', $application->candidate_id)
            ->where(function ($q) use ($application) {
                $q->where('application_id', $application->id)->orWhereNull('application_id');
            })->get()->groupBy('document_type_id');

        $items = [];
        foreach ($required as $req) {
            $typeId = $req->document_type_id;
            $typeDocs = $docs->get($typeId, collect());
            $status = 'MISSING';
            if ($typeDocs->where('verification_status', 'VERIFIED')->isNotEmpty()) {
                $status = 'VERIFIED';
            } elseif ($typeDocs->whereIn('verification_status', ['UPLOADED', 'UNDER_REVIEW'])->isNotEmpty()) {
                $status = 'UPLOADED';
            }
            $items[] = [
                'type' => $req->documentType->name ?? ('Type '.$typeId),
                'status' => $status,
                'count' => $typeDocs->count(),
            ];
        }
        $total = count($items);
        $done = count(array_filter($items, fn ($i) => $i['status'] !== 'MISSING'));
        return ['items' => $items, 'percent' => $total ? (int) round($done / $total * 100) : 100, 'total' => $total, 'done' => $done];
    }

    public static function download(CandidateDocument $doc)
    {
        Gate::authorize('download', $doc);

        $full = storage_path('app/private/'.$doc->stored_path);
        if (! file_exists($full)) {
            $alt = storage_path('app/'.$doc->stored_path);
            if (file_exists($alt)) {
                $full = $alt;
            } else {
                abort(404, 'File not found.');
            }
        }

        return response()->download($full, $doc->original_filename);
    }
}
