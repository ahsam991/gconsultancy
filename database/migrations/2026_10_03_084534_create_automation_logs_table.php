<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->string('trigger_event');
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->text('result')->nullable();
            $table->timestamps();
            $table->index('automation_rule_id');
            $table->index(['related_type', 'related_id']);
            $table->index('trigger_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
    }
};
