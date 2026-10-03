<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ReferralPartner extends Model
{
    protected $fillable = ['name', 'company', 'email', 'phone', 'country', 'commission_share_percent', 'type', 'active'];

    protected $casts = ['commission_share_percent' => 'decimal:2', 'active' => 'boolean'];

    public function payments(): HasMany
    {
        return $this->hasMany(ReferralPayment::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }


}
