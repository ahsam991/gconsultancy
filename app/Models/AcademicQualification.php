<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicQualification extends Model
{
    protected $fillable = ['candidate_id', 'level', 'institution', 'country', 'passing_year', 'result', 'grading_scale', 'certificate_path'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
