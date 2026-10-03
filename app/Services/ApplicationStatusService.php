<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use InvalidArgumentException;

class ApplicationStatusService
{
    public const TRANSITIONS = [
        'DRAFT' => ['PROFILE_CHECK', 'WITHDRAWN'],
        'PROFILE_CHECK' => ['DOCUMENT_PENDING', 'WITHDRAWN'],
        'DOCUMENT_PENDING' => ['READY_TO_APPLY', 'WITHDRAWN'],
        'READY_TO_APPLY' => ['SUBMITTED', 'WITHDRAWN'],
        'SUBMITTED' => ['ACKNOWLEDGED', 'WITHDRAWN', 'REJECTED'],
        'ACKNOWLEDGED' => ['UNDER_REVIEW', 'WITHDRAWN'],
        'UNDER_REVIEW' => ['INTERVIEW_REQUIRED', 'CONDITIONAL_OFFER', 'UNCONDITIONAL_OFFER', 'REJECTED', 'WITHDRAWN'],
        'INTERVIEW_REQUIRED' => ['CONDITIONAL_OFFER', 'UNCONDITIONAL_OFFER', 'REJECTED'],
        'CONDITIONAL_OFFER' => ['UNCONDITIONAL_OFFER', 'DEPOSIT_REQUIRED', 'WITHDRAWN'],
        'UNCONDITIONAL_OFFER' => ['DEPOSIT_REQUIRED', 'DEPOSIT_PAID'],
        'DEPOSIT_REQUIRED' => ['DEPOSIT_PAID'],
        'DEPOSIT_PAID' => ['CAS_REQUESTED'],
        'CAS_REQUESTED' => ['CAS_ISSUED'],
        'CAS_ISSUED' => ['VISA_PREPARATION'],
        'VISA_PREPARATION' => ['VISA_APPLIED'],
        'VISA_APPLIED' => ['VISA_APPROVED', 'VISA_REFUSED'],
        'VISA_APPROVED' => ['ENROLLED'],
        'VISA_REFUSED' => ['WITHDRAWN', 'REJECTED', 'CLOSED'],
        'ENROLLED' => ['CLOSED'],
        'WITHDRAWN' => ['CLOSED'],
        'REJECTED' => ['CLOSED'],
        'CLOSED' => [],
    ];

    protected const TERMINAL_ESCAPES = ['WITHDRAWN', 'REJECTED', 'CLOSED'];

    public static function allowed(string $from): array
    {
        return self::TRANSITIONS[$from] ?? [];
    }

    public static function canTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return false;
        }
        $allowed = self::allowed($from);
        if (in_array($to, $allowed, true)) {
            return true;
        }
        if (in_array($to, self::TERMINAL_ESCAPES, true) && $from !== 'CLOSED') {
            return true;
        }
        return false;
    }

    public static function transition(Application $app, string $newStatus, $reason = null, $note = null): Application
    {
        $newStatus = strtoupper(trim($newStatus));
        $current = strtoupper(trim((string) $app->status));

        if (! self::canTransition($current, $newStatus)) {
            throw new InvalidArgumentException("Illegal status transition from {$current} to {$newStatus}.");
        }

        $previous = $app->status;

        $app->status = $newStatus;
        if ($newStatus === 'SUBMITTED' && empty($app->submission_date)) {
            $app->submission_date = now()->toDateString();
        }
        if ($newStatus === 'SUBMITTED' && empty($app->applied_at)) {
            $app->applied_at = now()->toDateString();
        }
        $app->save();

        ApplicationStatusHistory::create([
            'application_id' => $app->id,
            'previous_status' => $previous,
            'new_status' => $newStatus,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
            'reason' => $reason,
            'note' => $note,
        ]);

        AuditService::log("application.status.{$newStatus}", $app, ['status' => $previous], ['status' => $newStatus]);

        $fresh = $app->fresh();
        NotifyService::applicationStatusChanged($fresh);

        return $fresh;
    }

    public static function history(Application $app)
    {
        return $app->statusHistory()->with('changedBy')->get();
    }
}
