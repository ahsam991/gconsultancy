<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['uid', 'name', 'country_id', 'city', 'website', 'partner_status', 'commission_rate', 'description', 'logo', 'active', 'featured'];

    protected $casts = ['commission_rate' => 'decimal:2', 'active' => 'boolean', 'featured' => 'boolean'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function campuses(): HasMany
    {
        return $this->hasMany(Campus::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function commissionRules(): HasMany
    {
        return $this->hasMany(CommissionRule::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(UniversityContact::class);
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
        return $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('uid', 'like', "%{$term}%")->orWhere('city', 'like', "%{$term}%"));
    }
}
