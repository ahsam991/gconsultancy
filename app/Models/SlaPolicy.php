<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class SlaPolicy extends Model
{
    protected $fillable = ['name', 'event', 'hours', 'active'];

    protected $casts = ['hours' => 'integer', 'active' => 'boolean'];

    public function breaches(): HasMany
    {
        return $this->hasMany(SlaBreach::class);
    }


}
