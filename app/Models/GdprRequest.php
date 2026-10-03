<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class GdprRequest extends Model
{
    protected $fillable = ['candidate_id', 'type', 'status', 'requested_by', 'completed_at', 'notes'];

    protected $casts = ['completed_at' => 'datetime'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }


}
