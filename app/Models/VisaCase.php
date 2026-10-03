<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaCase extends Model
{
    protected $fillable = ['application_id', 'candidate_id', 'destination', 'visa_type', 'application_date', 'biometrics_date', 'interview_date', 'decision_date', 'reference_no', 'result', 'status', 'notes'];

    protected $casts = ['application_date' => 'date', 'biometrics_date' => 'date', 'interview_date' => 'date', 'decision_date' => 'date'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
