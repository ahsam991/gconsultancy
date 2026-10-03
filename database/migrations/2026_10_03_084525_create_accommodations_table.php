<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->nullable();
            $table->string('property')->nullable();
            $table->string('room_type')->nullable();
            $table->string('location')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 10)->default('GBP');
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->string('booking_status')->default('REQUIRED');
            $table->decimal('deposit', 12, 2)->nullable();
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('application_id');
            $table->index('booking_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
