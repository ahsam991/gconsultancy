<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $fillable = ['application_id', 'required_amount', 'paid_amount', 'due_date', 'paid_date', 'transaction_ref', 'receipt_path', 'status'];

    protected $casts = ['required_amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'due_date' => 'date', 'paid_date' => 'date'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
