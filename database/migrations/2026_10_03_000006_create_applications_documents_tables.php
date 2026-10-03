<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('intake_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('DRAFT');
            $table->string('priority')->default('Normal');
            $table->decimal('tuition_fee', 12, 2)->nullable();
            $table->decimal('deposit_required', 12, 2)->nullable();
            $table->string('currency', 10)->default('GBP');
            $table->string('university_ref')->nullable();
            $table->date('submission_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('uid');
            $table->index('candidate_id');
            $table->index('university_id');
            $table->index('course_id');
            $table->index('status');
        });

        Schema::create('candidate_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('stored_path');
            $table->string('mime')->nullable();
            $table->integer('size_kb')->nullable();
            $table->string('verification_status')->default('UPLOADED');
            $table->integer('version')->default(1);
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('candidate_id');
            $table->index('application_id');
            $table->index('verification_status');
        });

        Schema::create('application_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('reason')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_status_history');
        Schema::dropIfExists('candidate_documents');
        Schema::dropIfExists('applications');
    }
};
