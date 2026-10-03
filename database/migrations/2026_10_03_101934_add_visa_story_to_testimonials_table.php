<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            if (! Schema::hasColumn('testimonials', 'visa_success_story')) $table->boolean('visa_success_story')->default(false);
            if (! Schema::hasColumn('testimonials', 'visa_type')) $table->string('visa_type')->nullable();
            if (! Schema::hasColumn('testimonials', 'country_flag')) $table->string('country_flag', 10)->nullable();
            if (! Schema::hasColumn('testimonials', 'course')) $table->string('course')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['visa_success_story', 'visa_type', 'country_flag', 'course']);
        });
    }
};
