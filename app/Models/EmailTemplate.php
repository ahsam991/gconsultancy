<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['slug', 'name', 'subject', 'body', 'variables', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
