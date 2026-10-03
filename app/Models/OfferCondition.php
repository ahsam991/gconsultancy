<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OfferCondition extends Model
{
    protected $fillable = ['offer_id', 'condition_text', 'is_required', 'is_submitted', 'is_verified', 'is_completed', 'deadline', 'completed_at', 'verified_at', 'notes'];

    protected $casts = ['is_required' => 'boolean', 'is_submitted' => 'boolean', 'is_verified' => 'boolean', 'is_completed' => 'boolean', 'deadline' => 'date', 'completed_at' => 'datetime', 'verified_at' => 'datetime'];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }


}
