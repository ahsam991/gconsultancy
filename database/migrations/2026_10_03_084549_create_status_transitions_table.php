<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status');
            $table->string('to_status');
            $table->string('required_permission')->nullable();
            $table->json('automation')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index('workflow_template_id');
            $table->index('from_status');
            $table->index('to_status');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_transitions');
    }
};
