<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyLevel extends Model
{
    protected $fillable = ['name', 'code', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
