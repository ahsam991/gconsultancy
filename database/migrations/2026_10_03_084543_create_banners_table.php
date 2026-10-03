<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->string('location')->default('home_hero');
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('location');
            $table->index('active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
