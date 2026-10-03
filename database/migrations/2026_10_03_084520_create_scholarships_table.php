<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('amount_type')->default('fixed');
            $table->text('criteria')->nullable();
            $table->date('deadline')->nullable();
            $table->foreignId('university_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index('university_id');
            $table->index('course_id');
            $table->index('active');
            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
