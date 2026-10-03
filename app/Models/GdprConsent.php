<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class GdprConsent extends Model
{
    protected $fillable = ['candidate_id', 'consent_type', 'given', 'ip', 'policy_version'];

    protected $casts = ['given' => 'boolean'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }


}
