<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Branch extends Model
{
    protected $fillable = ['name', 'code', 'address', 'city', 'country', 'phone', 'email', 'manager_id', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(StaffAvailability::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }


}
