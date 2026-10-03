<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['country_id', 'name', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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
