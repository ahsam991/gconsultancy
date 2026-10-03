<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SavedCourse extends Model
{
    protected $fillable = ['candidate_id', 'course_id'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }


}
