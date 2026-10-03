<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->decimal('commission_share_percent', 5, 2)->default(0);
            $table->string('type')->default('Agent');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index('active');
            $table->index('type');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_partners');
    }
};
