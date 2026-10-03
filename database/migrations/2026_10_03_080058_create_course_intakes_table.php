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
        Schema::create('course_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('intake_id')->constrained()->cascadeOnDelete();
            $table->date('opening_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->date('deadline')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['course_id', 'intake_id']);
            $table->index('course_id');
            $table->index('intake_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_intakes');
    }
};
