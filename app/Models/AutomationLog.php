<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AutomationLog extends Model
{
    protected $fillable = ['automation_rule_id', 'trigger_event', 'related_type', 'related_id', 'result'];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AutomationRule::class, 'automation_rule_id');
    }


}
