<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SlaBreach extends Model
{
    protected $fillable = ['sla_policy_id', 'related_type', 'related_id', 'detected_at', 'resolved_at', 'notified'];

    protected $casts = ['detected_at' => 'datetime', 'resolved_at' => 'datetime', 'notified' => 'boolean'];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class, 'sla_policy_id');
    }


}
