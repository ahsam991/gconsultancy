<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'manager_id', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'team_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
