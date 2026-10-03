<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContact extends Model
{
    protected $fillable = ['candidate_id', 'name', 'relationship', 'phone', 'email', 'address'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
