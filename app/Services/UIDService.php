<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class UIDService
{
    public static function candidateUid(): string
    {
        return DB::transaction(function () {
            $last = Candidate::orderByDesc('id')->first();
            $next = $last ? ((int) $last->id + 1) : 1;
            $uid = 'GC-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            while (Candidate::where('uid', $uid)->exists()) {
                $next++;
                $uid = 'GC-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            }
            return $uid;
        });
    }

    public static function applicationUid(): string
    {
        return DB::transaction(function () {
            $last = Application::orderByDesc('id')->first();
            $next = $last ? ((int) $last->id + 1) : 1;
            $uid = 'APP-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            while (Application::where('uid', $uid)->exists()) {
                $next++;
                $uid = 'APP-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            }
            return $uid;
        });
    }

    public static function invoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "INV-{$year}-";
        $last = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('id')->first();
        $next = 1;
        if ($last && preg_match('/(\d+)$/', $last->invoice_number, $m)) {
            $next = ((int) $m[1]) + 1;
        }
        $number = $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        while (Invoice::where('invoice_number', $number)->exists()) {
            $next++;
            $number = $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        }
        return $number;
    }
}
