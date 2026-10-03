<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = ['name', 'role', 'bio', 'photo', 'email', 'phone', 'sort_order', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
