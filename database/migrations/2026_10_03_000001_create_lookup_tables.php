<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['country_id', 'name']);
        });

        Schema::create('study_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category')->default('Other');
            $table->boolean('required_default')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index('category');
        });

        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('year');
            $table->integer('month');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intakes');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('lead_sources');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('study_levels');
        Schema::dropIfExists('cities');
    }
};
