<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_level_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('study_mode')->default('Full-time');
            $table->integer('duration_months')->default(12);
            $table->decimal('tuition_fee', 12, 2)->default(0);
            $table->string('currency', 10)->default('GBP');
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->decimal('ielts_required', 3, 1)->nullable();
            $table->string('intake_months')->nullable();
            $table->date('deadline')->nullable();
            $table->string('url')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index('uid');
            $table->index('university_id');
            $table->index('subject_id');
            $table->index('study_level_id');
            $table->index('name');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
