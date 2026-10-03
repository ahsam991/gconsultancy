<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->text('condition_text');
            $table->boolean('is_required')->default(true);
            $table->boolean('is_submitted')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->date('deadline')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('offer_id');
            $table->index('is_completed');
            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_conditions');
    }
};
