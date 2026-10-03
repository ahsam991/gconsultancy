<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predeparture_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete()->unique();
            $table->boolean('visa_approved')->default(false);
            $table->boolean('accommodation')->default(false);
            $table->boolean('flight')->default(false);
            $table->boolean('airport_pickup')->default(false);
            $table->boolean('insurance')->default(false);
            $table->boolean('orientation')->default(false);
            $table->boolean('documents_ready')->default(false);
            $table->boolean('emergency_contact')->default(false);
            $table->timestamps();
            $table->index('candidate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predeparture_checklists');
    }
};
