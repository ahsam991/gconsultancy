<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->string('preferred_destination')->nullable();
            $table->string('preferred_level')->nullable();
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('status')->default('NEW');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('email');
            $table->index('phone');
            $table->index('status');
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('passport_no')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->string('preferred_destination')->nullable();
            $table->string('preferred_level')->nullable();
            $table->string('preferred_subject')->nullable();
            $table->string('referral_source')->nullable();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('NEW');
            $table->integer('profile_completion')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('uid');
            $table->index('email');
            $table->index('phone');
            $table->index('passport_no');
            $table->index('status');
        });

        Schema::create('candidate_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('present');
            $table->string('address_line');
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('postcode')->nullable();
            $table->timestamps();
            $table->index('candidate_id');
        });

        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->index('candidate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('candidate_addresses');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('leads');
    }
};
