<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StaffAvailability extends Model
{
    protected $fillable = ['user_id', 'day_of_week', 'start_time', 'end_time', 'is_available', 'branch_id'];

    protected $casts = ['day_of_week' => 'integer', 'is_available' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }


}
