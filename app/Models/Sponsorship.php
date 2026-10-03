<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Sponsorship extends Model
{
    protected $fillable = ['candidate_id', 'sponsor_name', 'relationship', 'occupation', 'country', 'income', 'contact', 'funding_amount', 'evidence_path', 'verified'];

    protected $casts = ['income' => 'decimal:2', 'funding_amount' => 'decimal:2', 'verified' => 'boolean'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }


}
