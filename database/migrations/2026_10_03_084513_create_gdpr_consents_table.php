<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('consent_type')->default('privacy_policy');
            $table->boolean('given')->default(false);
            $table->string('ip', 45)->nullable();
            $table->string('policy_version')->nullable();
            $table->timestamps();
            $table->unique(['candidate_id', 'consent_type', 'policy_version']);
            $table->index('candidate_id');
            $table->index('consent_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_consents');
    }
};
