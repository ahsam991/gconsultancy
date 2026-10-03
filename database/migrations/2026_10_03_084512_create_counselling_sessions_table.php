<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('session_date');
            $table->string('purpose');
            $table->text('discussion')->nullable();
            $table->text('recommendation')->nullable();
            $table->string('preferred_destination')->nullable();
            $table->string('preferred_level')->nullable();
            $table->string('preferred_subject')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->string('ielts')->nullable();
            $table->string('next_action')->nullable();
            $table->date('followup_date')->nullable();
            $table->string('status')->default('COMPLETED');
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('staff_id');
            $table->index('session_date');
            $table->index('status');
            $table->index('followup_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselling_sessions');
    }
};
