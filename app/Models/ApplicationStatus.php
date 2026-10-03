<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStatus extends Model
{
    protected $fillable = ['code', 'label', 'color', 'sort_order', 'terminal', 'active'];

    protected $casts = ['terminal' => 'boolean', 'active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
