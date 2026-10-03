<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referral_partner_id')->constrained()->cascadeOnDelete();
            $table->decimal('share_percent', 5, 2);
            $table->decimal('share_amount', 12, 2);
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index('commission_id');
            $table->index('referral_partner_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_payments');
    }
};
