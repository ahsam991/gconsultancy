<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->boolean('successful')->default(false);
            $table->string('session_id')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('email');
            $table->index('successful');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
