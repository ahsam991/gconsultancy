<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('sponsor_name');
            $table->string('relationship')->nullable();
            $table->string('occupation')->nullable();
            $table->string('country')->nullable();
            $table->decimal('income', 12, 2)->nullable();
            $table->string('contact')->nullable();
            $table->decimal('funding_amount', 12, 2)->nullable();
            $table->string('evidence_path')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
