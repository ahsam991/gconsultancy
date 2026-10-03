<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PredepartureChecklist extends Model
{
    protected $fillable = ['candidate_id', 'visa_approved', 'accommodation', 'flight', 'airport_pickup', 'insurance', 'orientation', 'documents_ready', 'emergency_contact'];

    protected $casts = ['visa_approved' => 'boolean', 'accommodation' => 'boolean', 'flight' => 'boolean', 'airport_pickup' => 'boolean', 'insurance' => 'boolean', 'orientation' => 'boolean', 'documents_ready' => 'boolean', 'emergency_contact' => 'boolean'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }


}
