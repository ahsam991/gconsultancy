<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Scholarship extends Model
{
    protected $fillable = ['title', 'amount', 'amount_type', 'criteria', 'deadline', 'university_id', 'course_id', 'active'];

    protected $casts = ['amount' => 'decimal:2', 'deadline' => 'date', 'active' => 'boolean'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }


}
