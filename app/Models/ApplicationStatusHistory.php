<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusHistory extends Model
{
    protected $table = 'application_status_history';

    protected $fillable = ['application_id', 'previous_status', 'new_status', 'changed_by', 'reason', 'note'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
