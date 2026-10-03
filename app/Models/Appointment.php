<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = ['candidate_id', 'staff_id', 'type', 'appointment_date', 'location', 'meeting_url', 'status', 'notes'];

    protected $casts = ['appointment_date' => 'datetime'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
