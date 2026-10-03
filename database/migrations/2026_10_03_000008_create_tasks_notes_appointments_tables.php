<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('NEW');
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('application_id');
            $table->index('assigned_to');
            $table->index('status');
        });

        Schema::create('action_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('application_id');
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('Counselling');
            $table->dateTime('appointment_date');
            $table->string('location')->nullable();
            $table->string('meeting_url')->nullable();
            $table->string('status')->default('SCHEDULED');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('staff_id');
            $table->index('status');
        });

        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('channel');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('direction')->default('Outbound');
            $table->timestamps();
            $table->index('candidate_id');
            $table->index('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('action_notes');
        Schema::dropIfExists('tasks');
    }
};
