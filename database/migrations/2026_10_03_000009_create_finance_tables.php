<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->nullable()->constrained()->nullOnDelete();
            $table->string('study_level')->nullable();
            $table->decimal('rate_percent', 5, 2)->default(0);
            $table->decimal('fixed_amount', 12, 2)->nullable();
            $table->string('currency', 10)->default('GBP');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index('university_id');
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('GBP');
            $table->decimal('rate_percent', 5, 2)->nullable();
            $table->string('status')->default('PENDING');
            $table->date('expected_date')->nullable();
            $table->date('claimed_date')->nullable();
            $table->date('received_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('application_id');
            $table->index('candidate_id');
            $table->index('university_id');
            $table->index('status');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commission_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status')->default('PENDING');
            $table->text('notes')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('invoice_number');
            $table->index('university_id');
            $table->index('status');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
            $table->index('invoice_id');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('payment_date');
            $table->string('method')->default('Bank Transfer');
            $table->string('transaction_ref')->nullable();
            $table->string('receipt_path')->nullable();
            $table->foreignId('received_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('commission_rules');
    }
};
