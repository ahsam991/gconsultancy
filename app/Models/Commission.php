<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commission extends Model
{
    protected $fillable = ['application_id', 'candidate_id', 'university_id', 'amount', 'currency', 'rate_percent', 'status', 'expected_date', 'claimed_date', 'received_date', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'rate_percent' => 'decimal:2', 'expected_date' => 'date', 'claimed_date' => 'date', 'received_date' => 'date'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
