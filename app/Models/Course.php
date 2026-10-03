<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['uid', 'university_id', 'campus_id', 'subject_id', 'study_level_id', 'name', 'code', 'study_mode', 'duration_months', 'tuition_fee', 'currency', 'deposit_amount', 'ielts_required', 'intake_months', 'deadline', 'url', 'active', 'featured'];

    protected $casts = ['tuition_fee' => 'decimal:2', 'deposit_amount' => 'decimal:2', 'ielts_required' => 'decimal:1', 'deadline' => 'date', 'active' => 'boolean', 'featured' => 'boolean'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function studyLevel(): BelongsTo
    {
        return $this->belongsTo(StudyLevel::class, 'study_level_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function courseIntakes(): HasMany
    {
        return $this->hasMany(CourseIntake::class);
    }

    public function intakesList()
    {
        return $this->belongsToMany(Intake::class, 'course_intakes')->withPivot(['opening_date', 'closing_date', 'deadline', 'active']);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(CourseRequirement::class);
    }

    public function scholarships(): HasMany
    {
        return $this->hasMany(Scholarship::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('uid', 'like', "%{$term}%")->orWhere('code', 'like', "%{$term}%"));
    }
}
