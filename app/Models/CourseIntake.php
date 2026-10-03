<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseIntake extends Model
{
    protected $fillable = ['course_id', 'intake_id', 'opening_date', 'closing_date', 'deadline', 'active'];

    protected $casts = ['opening_date' => 'date', 'closing_date' => 'date', 'deadline' => 'date', 'active' => 'boolean'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function intake(): BelongsTo
    {
        return $this->belongsTo(Intake::class);
    }
}
