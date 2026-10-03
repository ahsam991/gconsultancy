<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    protected $fillable = ['university_id', 'name', 'city', 'address', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
