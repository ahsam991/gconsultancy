<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    protected $fillable = ['application_id', 'type', 'offer_date', 'deadline', 'deposit_amount', 'scholarship', 'conditions', 'document_path', 'status'];

    protected $casts = ['offer_date' => 'date', 'deadline' => 'date', 'deposit_amount' => 'decimal:2', 'scholarship' => 'decimal:2'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function conditions()
    {
        return $this->hasMany(OfferCondition::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
