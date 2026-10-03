<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('purpose')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('candidate_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_access_logs');
    }
};
