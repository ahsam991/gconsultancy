<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class AutomationRule extends Model
{
    protected $fillable = ['name', 'trigger_event', 'trigger_status', 'action_type', 'action_config', 'active'];

    protected $casts = ['action_config' => 'array', 'active' => 'boolean'];

    public function logs(): HasMany
    {
        return $this->hasMany(AutomationLog::class);
    }


}
