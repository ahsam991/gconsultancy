<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Form extends Model
{
    protected $fillable = ['name', 'slug', 'fields', 'active'];

    protected $casts = ['fields' => 'array', 'active' => 'boolean'];

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }


}
