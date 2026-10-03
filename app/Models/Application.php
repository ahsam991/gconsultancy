<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['uid', 'candidate_id', 'university_id', 'campus_id', 'course_id', 'intake_id', 'assigned_staff_id', 'status', 'priority', 'tuition_fee', 'deposit_required', 'currency', 'university_ref', 'submission_date', 'notes', 'created_by', 'updated_by'];

    protected $casts = ['tuition_fee' => 'decimal:2', 'deposit_required' => 'decimal:2', 'submission_date' => 'date'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function intake(): BelongsTo
    {
        return $this->belongsTo(Intake::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CandidateDocument::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ActionNote::class);
    }

    public function visaCases(): HasMany
    {
        return $this->hasMany(VisaCase::class);
    }

    public function offer(): HasOne
    {
        return $this->hasOne(Offer::class);
    }

    public function deposit(): HasOne
    {
        return $this->hasOne(Deposit::class);
    }

    public function casRecord(): HasOne
    {
        return $this->hasOne(CasRecord::class);
    }

    public function enrolment(): HasOne
    {
        return $this->hasOne(Enrolment::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(ApplicationFee::class);
    }

    public function offerConditions()
    {
        return $this->hasManyThrough(OfferCondition::class, Offer::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(fn ($q) => $q->where('uid', 'like', "%{$term}%")->orWhere('university_ref', 'like', "%{$term}%"));
    }
}
