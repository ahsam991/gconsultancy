<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CasRecord extends Model
{
    protected $fillable = ['application_id', 'cas_number', 'requested_date', 'issued_date', 'expiry_date', 'status', 'notes'];

    protected $casts = ['requested_date' => 'date', 'issued_date' => 'date', 'expiry_date' => 'date'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
