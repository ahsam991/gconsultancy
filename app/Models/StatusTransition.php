<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StatusTransition extends Model
{
    protected $fillable = ['workflow_template_id', 'from_status', 'to_status', 'required_permission', 'automation', 'active'];

    protected $casts = ['automation' => 'array', 'active' => 'boolean'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }


}
