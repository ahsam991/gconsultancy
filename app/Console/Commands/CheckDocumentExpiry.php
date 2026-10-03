<?php

namespace App\Console\Commands;

use App\Models\CandidateDocument;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckDocumentExpiry extends Command
{
    protected $signature = 'app:check-document-expiry';

    protected $description = 'Notify staff and create tasks for candidate documents expiring within 30 days or past.';

    public function handle(): int
    {
        if (! Schema::hasColumn('candidate_documents', 'expiry_date')) {
            $this->warn('candidate_documents.expiry_date column missing; skipping.');

            return 0;
        }

        $docs = CandidateDocument::with(['candidate.assignedStaff', 'documentType'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString())
            ->get();

        $notified = 0;
        $tasks = 0;
        $skipped = 0;

        foreach ($docs as $doc) {
            $staff = $doc->candidate->assignedStaff ?? null;

            // Idempotent: skip when an open expiry task already exists for this document.
            $openTask = Task::where('candidate_id', $doc->candidate_id)
                ->where('title', 'like', '%Document expiry%')
                ->where('description', 'like', '%#'.$doc->id.'%')
                ->whereNotIn('status', ['completed', 'COMPLETED', 'cancelled', 'CANCELLED'])
                ->where('created_at', '>=', now()->subDays(30))
                ->exists();
            if ($openTask) {
                $skipped++;
                continue;
            }

            $expiryLabel = $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('d M Y') : 'date unknown';
            $label = ($doc->documentType->name ?? $doc->original_filename).' expires '.$expiryLabel;
            if ($staff && class_exists(\App\Notifications\RecordNotification::class)) {
                try {
                    $staff->notify(new \App\Notifications\RecordNotification(
                        'Document expiring: '.($doc->documentType->name ?? $doc->original_filename),
                        trim(($doc->candidate->first_name ?? '').' '.($doc->candidate->last_name ?? '')).' — '.$label.'.',
                        ['candidate_document_id' => $doc->id, 'candidate_id' => $doc->candidate_id]
                    ));
                    $notified++;
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            if ($staff) {
                Task::create([
                    'title' => 'Document expiry: '.($doc->documentType->name ?? $doc->original_filename),
                    'description' => 'Candidate document #'.$doc->id.' ('.$label.') needs renewal.',
                    'candidate_id' => $doc->candidate_id,
                    'application_id' => $doc->application_id,
                    'assigned_to' => $staff->id,
                    'priority' => 'High',
                    'status' => 'NEW',
                    'due_date' => now()->addDays(7)->toDateString(),
                ]);
                $tasks++;
            } else {
                $skipped++;
            }
        }

        $this->info("Document expiry: checked {$docs->count()}, notified {$notified}, tasks {$tasks}, skipped {$skipped}.");

        return 0;
    }
}
