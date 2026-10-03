<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Backup extends Model
{
    protected $fillable = ['type', 'path', 'size_kb', 'status', 'created_by', 'notes'];

    protected $casts = ['size_kb' => 'integer'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
