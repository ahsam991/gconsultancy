<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $fillable = ['name', 'category', 'required_default', 'active'];

    protected $casts = ['required_default' => 'boolean', 'active' => 'boolean'];

    public function documents(): HasMany
    {
        return $this->hasMany(CandidateDocument::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
