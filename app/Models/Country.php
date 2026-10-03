<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['name', 'iso_code', 'dial_code', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
