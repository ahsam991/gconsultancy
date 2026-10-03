<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Commission;
use App\Models\CommissionRule;

class CommissionService
{
    public static function rateFor(Application $app): float
    {
        $rule = CommissionRule::where('university_id', $app->university_id)
            ->where('active', true)
            ->orderByDesc('id')
            ->first();

        if ($rule) {
            return (float) $rule->rate;
        }

        $university = $app->university;
        if ($university && ! empty($university->commission_rate)) {
            return (float) $university->commission_rate;
        }

        return 10.0;
    }

    public static function calculate(Application $app): Commission
    {
        $rate = self::rateFor($app);
        $tuition = (float) ($app->tuition_fee ?? $app->course?->tuition_fee ?? 0);
        $amount = round($tuition * ($rate / 100), 2);

        $commission = Commission::updateOrCreate(
            ['application_id' => $app->id],
            [
                'university_id' => $app->university_id,
                'candidate_id' => $app->candidate_id,
                'intake_id' => $app->intake_id,
                'rate' => $rate,
                'amount' => $amount,
                'currency' => $app->currency ?? 'GBP',
            ]
        );

        AuditService::log('commission.calculated', $app, null, $commission->toArray());

        return $commission;
    }

    public static function claimable()
    {
        return Commission::whereIn('status', ['PENDING', 'READY_TO_CLAIM', 'DUE'])
            ->whereHas('application', function ($q) {
                $q->where('status', 'ENROLLED');
            })->with(['application', 'candidate', 'university']);
    }
}
