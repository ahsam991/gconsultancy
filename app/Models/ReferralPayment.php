<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ReferralPayment extends Model
{
    protected $fillable = ['commission_id', 'referral_partner_id', 'share_percent', 'share_amount', 'status', 'paid_at'];

    protected $casts = ['share_percent' => 'decimal:2', 'share_amount' => 'decimal:2', 'paid_at' => 'datetime'];

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function referralPartner(): BelongsTo
    {
        return $this->belongsTo(ReferralPartner::class);
    }


}
