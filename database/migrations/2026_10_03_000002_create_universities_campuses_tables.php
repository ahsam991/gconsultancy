<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('city')->nullable();
            $table->string('website')->nullable();
            $table->string('partner_status')->default('Partner');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index('uid');
            $table->index('name');
            $table->index('partner_status');
        });

        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index('university_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campuses');
        Schema::dropIfExists('universities');
    }
};
