<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Accommodation extends Model
{
    protected $fillable = ['candidate_id', 'application_id', 'provider', 'property', 'room_type', 'location', 'price', 'currency', 'check_in', 'check_out', 'booking_status', 'deposit'];

    protected $casts = ['price' => 'decimal:2', 'deposit' => 'decimal:2', 'check_in' => 'date', 'check_out' => 'date'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }


}
