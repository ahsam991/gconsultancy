<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->mediumText('content')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->index('slug');
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('country')->nullable();
            $table->string('university')->nullable();
            $table->integer('rating')->default(5);
            $table->text('content');
            $table->string('photo')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('website_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('Contact');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('destination')->nullable();
            $table->string('level')->nullable();
            $table->string('subject')->nullable();
            $table->date('preferred_date')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('NEW');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('converted_candidate_id')->nullable()->constrained('candidates')->nullOnDelete();
            $table->timestamps();
            $table->index('email');
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_enquiries');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('website_pages');
    }
};
