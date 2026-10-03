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
        // Tier-aware: uses commission_rules.tier_config JSON [{min,max,rate}]
        // when that column exists, otherwise falls back to legacy rateFor().
        $rate = self::rateForWithTiers($app);
        $tuition = (float) ($app->tuition_fee ?? $app->course?->tuition_fee ?? 0);
        $amount = round($tuition * ($rate / 100), 2);

        $commission = Commission::updateOrCreate(
            ['application_id' => $app->id],
            [
                'university_id' => $app->university_id,
                'candidate_id' => $app->candidate_id,
                'intake_id' => $app->intake_id,
                // 'rate' kept for BC (not fillable on current schema, ignored);
                // 'rate_percent' is the persisted column.
                'rate' => $rate,
                'rate_percent' => $rate,
                'amount' => $amount,
                'currency' => $app->currency ?? 'GBP',
            ]
        );

        AuditService::log('commission.calculated', $app, null, $commission->toArray());

        return $commission;
    }

    /**
     * Tiered rate lookup. Reads commission_rules.tier_config JSON
     * shaped as [{min,max,rate}, ...] and picks the tier matching the
     * application's tuition. Returns null when no tier applies or the
     * column/data is absent, so callers can fall back safely.
     */
    public static function tierRateFor(Application $app, ?float $tuition = null): ?float
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasColumn('commission_rules', 'tier_config')) {
                return null;
            }
            $tuition ??= (float) ($app->tuition_fee ?? $app->course?->tuition_fee ?? 0);
            $rule = CommissionRule::where('university_id', $app->university_id)
                ->where('active', true)
                ->orderByDesc('id')
                ->first();
            if (! $rule || empty($rule->tier_config)) {
                return null;
            }
            $tiers = is_string($rule->tier_config) ? json_decode($rule->tier_config, true) : $rule->tier_config;
            if (! is_array($tiers)) {
                return null;
            }
            foreach ($tiers as $tier) {
                $min = isset($tier['min']) ? (float) $tier['min'] : 0;
                $max = array_key_exists('max', $tier) && $tier['max'] !== null && $tier['max'] !== '' ? (float) $tier['max'] : null;
                $rate = isset($tier['rate']) ? (float) $tier['rate'] : null;
                if ($rate === null) {
                    continue;
                }
                if ($tuition >= $min && ($max === null || $tuition <= $max)) {
                    return $rate;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    /**
     * Tier-aware wrapper around legacy rateFor(). Existing behaviour is
     * fully preserved when no tier_config column/row matches.
     */
    public static function rateForWithTiers(Application $app): float
    {
        $tiered = self::tierRateFor($app);
        if ($tiered !== null) {
            return $tiered;
        }

        return self::rateFor($app);
    }

    public static function claimable()
    {
        return Commission::whereIn('status', ['PENDING', 'READY_TO_CLAIM', 'DUE'])
            ->whereHas('application', function ($q) {
                $q->where('status', 'ENROLLED');
            })->with(['application', 'candidate', 'university']);
    }
}
