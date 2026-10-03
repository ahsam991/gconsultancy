<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            if (! Schema::hasColumn('commission_rules', 'tier_config')) $table->json('tier_config')->nullable();
        });
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'reminder_sent_at')) $table->timestamp('reminder_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at']);
        });
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->dropColumn(['tier_config']);
        });
    }
};
