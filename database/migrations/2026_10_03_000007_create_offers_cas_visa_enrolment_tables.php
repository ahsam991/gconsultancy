<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('Conditional');
            $table->date('offer_date')->nullable();
            $table->date('deadline')->nullable();
            $table->decimal('deposit_amount', 12, 2)->nullable();
            $table->decimal('scholarship', 12, 2)->nullable();
            $table->text('conditions')->nullable();
            $table->string('document_path')->nullable();
            $table->string('status')->default('RECEIVED');
            $table->timestamps();
            $table->index('application_id');
            $table->index('status');
        });

        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->decimal('required_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('transaction_ref')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamps();
            $table->index('application_id');
            $table->index('status');
        });

        Schema::create('cas_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('cas_number')->nullable()->unique();
            $table->date('requested_date')->nullable();
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('status')->default('NOT_REQUESTED');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('application_id');
            $table->index('status');
        });

        Schema::create('visa_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('destination')->default('UK');
            $table->string('visa_type')->default('Student');
            $table->date('application_date')->nullable();
            $table->date('biometrics_date')->nullable();
            $table->date('interview_date')->nullable();
            $table->date('decision_date')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('result')->nullable();
            $table->string('status')->default('NOT_STARTED');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('application_id');
            $table->index('candidate_id');
            $table->index('status');
        });

        Schema::create('enrolments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->date('enrolment_date')->nullable();
            $table->string('student_id_no')->nullable();
            $table->string('campus')->nullable();
            $table->string('status')->default('PENDING');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('application_id');
            $table->index('candidate_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrolments');
        Schema::dropIfExists('visa_cases');
        Schema::dropIfExists('cas_records');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('offers');
    }
};
