<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StudentPayment extends Model
{
    protected $fillable = ['candidate_id', 'application_id', 'amount', 'currency', 'purpose', 'status', 'payment_date', 'transaction_ref', 'receipt_path', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }


}
