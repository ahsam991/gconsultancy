<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRule extends Model
{
    protected $fillable = ['university_id', 'study_level', 'rate_percent', 'fixed_amount', 'currency', 'effective_from', 'effective_to', 'active'];

    protected $casts = ['rate_percent' => 'decimal:2', 'fixed_amount' => 'decimal:2', 'effective_from' => 'date', 'effective_to' => 'date', 'active' => 'boolean'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
