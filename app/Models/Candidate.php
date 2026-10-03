<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['uid', 'user_id', 'first_name', 'last_name', 'email', 'phone', 'dob', 'gender', 'nationality', 'passport_no', 'passport_expiry', 'address', 'city', 'country_id', 'preferred_destination', 'preferred_level', 'preferred_subject', 'referral_source', 'assigned_staff_id', 'assigned_manager_id', 'status', 'profile_completion', 'created_by', 'updated_by'];

    protected $casts = ['dob' => 'date', 'passport_expiry' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function assignedManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_manager_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CandidateAddress::class);
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(AcademicQualification::class);
    }

    public function englishTests(): HasMany
    {
        return $this->hasMany(EnglishTest::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CandidateDocument::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function visaCases(): HasMany
    {
        return $this->hasMany(VisaCase::class);
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(GdprConsent::class);
    }

    public function gdprRequests(): HasMany
    {
        return $this->hasMany(GdprRequest::class);
    }

    public function counsellingSessions(): HasMany
    {
        return $this->hasMany(CounsellingSession::class);
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    public function predeparture()
    {
        return $this->hasOne(PredepartureChecklist::class);
    }

    public function arrival()
    {
        return $this->hasOne(Arrival::class);
    }

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function savedCourses(): HasMany
    {
        return $this->hasMany(SavedCourse::class);
    }

    public function shortlists(): HasMany
    {
        return $this->hasMany(CourseShortlist::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['WITHDRAWN', 'LOST', 'COMPLETED']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(fn ($q) => $q->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")->orWhere('uid', 'like', "%{$term}%")->orWhere('passport_no', 'like', "%{$term}%"));
    }
}
