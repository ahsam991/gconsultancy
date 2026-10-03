<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('event');
            $table->integer('hours');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index('event');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};
