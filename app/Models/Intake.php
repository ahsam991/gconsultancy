<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intake extends Model
{
    protected $fillable = ['name', 'year', 'month', 'start_date', 'deadline', 'active'];

    protected $casts = ['start_date' => 'date', 'deadline' => 'date', 'active' => 'boolean'];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
