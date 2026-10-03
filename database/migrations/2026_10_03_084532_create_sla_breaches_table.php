<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_breaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sla_policy_id')->constrained()->cascadeOnDelete();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamp('detected_at');
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('notified')->default(false);
            $table->timestamps();
            $table->index('sla_policy_id');
            $table->index(['related_type', 'related_id']);
            $table->index('detected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_breaches');
    }
};
