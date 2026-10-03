<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CounsellingSession extends Model
{
    protected $fillable = ['candidate_id', 'staff_id', 'session_date', 'purpose', 'discussion', 'recommendation', 'preferred_destination', 'preferred_level', 'preferred_subject', 'budget', 'ielts', 'next_action', 'followup_date', 'status'];

    protected $casts = ['session_date' => 'datetime', 'followup_date' => 'date', 'budget' => 'decimal:2'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }


}
