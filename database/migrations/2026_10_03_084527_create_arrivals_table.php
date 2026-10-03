<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arrivals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete()->unique();
            $table->boolean('arrived')->default(false);
            $table->date('arrival_date')->nullable();
            $table->boolean('university_registered')->default(false);
            $table->boolean('accommodation_confirmed')->default(false);
            $table->string('brp_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('arrived');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arrivals');
    }
};
