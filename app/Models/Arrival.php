<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Arrival extends Model
{
    protected $fillable = ['candidate_id', 'arrived', 'arrival_date', 'university_registered', 'accommodation_confirmed', 'brp_number', 'notes'];

    protected $casts = ['arrived' => 'boolean', 'arrival_date' => 'date', 'university_registered' => 'boolean', 'accommodation_confirmed' => 'boolean'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }


}
