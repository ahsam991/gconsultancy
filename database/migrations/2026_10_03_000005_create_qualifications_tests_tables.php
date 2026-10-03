<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('level');
            $table->string('institution');
            $table->string('country')->nullable();
            $table->integer('passing_year')->nullable();
            $table->string('result')->nullable();
            $table->string('grading_scale')->nullable();
            $table->string('certificate_path')->nullable();
            $table->timestamps();
            $table->index('candidate_id');
        });

        Schema::create('english_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('test_type');
            $table->decimal('overall', 3, 1)->nullable();
            $table->decimal('listening', 3, 1)->nullable();
            $table->decimal('reading', 3, 1)->nullable();
            $table->decimal('writing', 3, 1)->nullable();
            $table->decimal('speaking', 3, 1)->nullable();
            $table->date('test_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('certificate_path')->nullable();
            $table->timestamps();
            $table->index('candidate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('english_tests');
        Schema::dropIfExists('academic_qualifications');
    }
};
