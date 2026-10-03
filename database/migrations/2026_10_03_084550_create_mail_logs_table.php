<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to_email');
            $table->string('subject');
            $table->string('template_slug')->nullable();
            $table->string('status')->default('queued');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index('to_email');
            $table->index('status');
            $table->index('template_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
