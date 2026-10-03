<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'country_id', 'preferred_destination', 'preferred_level', 'source_id', 'message', 'status', 'assigned_to', 'notes'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(fn ($q) => $q->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%"));
    }
}
