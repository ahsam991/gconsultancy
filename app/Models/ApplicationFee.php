<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ApplicationFee extends Model
{
    protected $fillable = ['application_id', 'amount', 'currency', 'status', 'payment_date', 'receipt_path', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }


}
