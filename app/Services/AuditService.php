<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public static function log(string $action, ?Model $model = null, $old = null, $new = null): AuditLog
    {
        $candidateId = null;
        $applicationId = null;

        if ($model) {
            $class = get_class($model);
            if ($class === \App\Models\Candidate::class) {
                $candidateId = $model->getKey();
            } elseif ($class === \App\Models\Application::class) {
                $applicationId = $model->getKey();
                $candidateId = $model->candidate_id ?? null;
            } elseif ($class === \App\Models\CandidateDocument::class) {
                $candidateId = $model->candidate_id ?? null;
                $applicationId = $model->application_id ?? null;
            }
        }

        if (is_object($old)) {
            $old = method_exists($old, 'toArray') ? $old->toArray() : (array) $old;
        }
        if (is_object($new)) {
            $new = method_exists($new, 'toArray') ? $new->toArray() : (array) $new;
        }

        return AuditLog::create([
            'action' => $action,
            'user_id' => auth()->id(),
            'candidate_id' => $candidateId,
            'application_id' => $applicationId,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model ? $model->getKey() : null,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 1024),
            'created_at' => now(),
        ]);
    }
}
