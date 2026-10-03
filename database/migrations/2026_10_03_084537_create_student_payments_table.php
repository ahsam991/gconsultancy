<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('GBP');
            $table->string('purpose');
            $table->string('status')->default('pending');
            $table->date('payment_date')->nullable();
            $table->string('transaction_ref')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('application_id');
            $table->index('status');
            $table->index('purpose');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
