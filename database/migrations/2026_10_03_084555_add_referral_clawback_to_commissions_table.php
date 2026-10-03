<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('commissions', 'referral_partner_id')) {
                $table->foreignId('referral_partner_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('commissions', 'clawback_amount')) {
                $table->decimal('clawback_amount', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('commissions', 'clawback_reason')) {
                $table->text('clawback_reason')->nullable();
            }
            if (!Schema::hasColumn('commissions', 'clawback_deadline')) {
                $table->date('clawback_deadline')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            try { $table->dropForeign(['referral_partner_id']); } catch (\Throwable $e) {}
            foreach (['referral_partner_id','clawback_amount','clawback_reason','clawback_deadline'] as $col) {
                if (Schema::hasColumn('commissions', $col)) {
                    try { $table->dropColumn($col); } catch (\Throwable $e) {}
                }
            }
        });
    }
};
