<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrolment extends Model
{
    protected $fillable = ['application_id', 'candidate_id', 'enrolment_date', 'student_id_no', 'campus', 'status', 'notes'];

    protected $casts = ['enrolment_date' => 'date'];

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
