<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('candidates', 'consent_given')) {
                $table->boolean('consent_given')->default(false);
            }
            if (!Schema::hasColumn('candidates', 'consent_date')) {
                $table->timestamp('consent_date')->nullable();
            }
            if (!Schema::hasColumn('candidates', 'consent_ip')) {
                $table->string('consent_ip', 45)->nullable();
            }
            if (!Schema::hasColumn('candidates', 'policy_version')) {
                $table->string('policy_version')->nullable();
            }
            if (!Schema::hasColumn('candidates', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('candidates', 'accommodation_required')) {
                $table->boolean('accommodation_required')->default(false);
            }
            if (!Schema::hasColumn('candidates', 'accommodation_booked')) {
                $table->boolean('accommodation_booked')->default(false);
            }
            if (!Schema::hasColumn('candidates', 'accommodation_confirmed')) {
                $table->boolean('accommodation_confirmed')->default(false);
            }
            if (!Schema::hasColumn('candidates', 'arrived')) {
                $table->boolean('arrived')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            try { $table->dropForeign(['branch_id']); } catch (\Throwable $e) {}
            foreach (['consent_given','consent_date','consent_ip','policy_version','branch_id','accommodation_required','accommodation_booked','accommodation_confirmed','arrived'] as $col) {
                if (Schema::hasColumn('candidates', $col)) {
                    try { $table->dropColumn($col); } catch (\Throwable $e) {}
                }
            }
        });
    }
};
