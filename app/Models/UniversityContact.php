<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class UniversityContact extends Model
{
    protected $fillable = ['university_id', 'name', 'position', 'department', 'email', 'phone', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }


}
