<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('candidate_documents', 'expiry_date')) {
                $table->date('expiry_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidate_documents', function (Blueprint $table) {
            if (Schema::hasColumn('candidate_documents', 'expiry_date')) {
                try { $table->dropColumn('expiry_date'); } catch (\Throwable $e) {}
            }
        });
    }
};
