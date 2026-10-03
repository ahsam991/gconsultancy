<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteEnquiry extends Model
{
    protected $fillable = ['type', 'name', 'email', 'phone', 'destination', 'level', 'subject', 'preferred_date', 'message', 'status', 'assigned_to', 'converted_candidate_id'];

    protected $casts = ['preferred_date' => 'date'];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedCandidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'converted_candidate_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%"));
    }
}
